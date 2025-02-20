<?php

namespace App\Services;

use App\Repositories\ApplyRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\FileRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentService extends Service
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    /** @var \App\Repositories\ApplyRepository */
    private $applyRepository;

    /** @var \App\Repositories\DocumentRepository */
    private $documentRepository;

    /** @var \App\Repositories\FileRepository */
    private $fileRepository;

    public function __construct(
        UserRepository $userRepository,
        ApplyRepository $applyRepository,
        DocumentRepository $documentRepository,
        FileRepository $fileRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->applyRepository = $applyRepository;
        $this->documentRepository = $documentRepository;
        $this->fileRepository = $fileRepository;
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'submission_date' => ['required', 'date'],
            'files'           => ['nullable', 'array'],
            'memo'            => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, int $applyId, array $postedParams): \App\Models\Document|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $apply = $this->applyRepository->findBy(['user_id' => $userId, 'apply_id' => $applyId]);
            if ( $apply === null ) {
                Log::error( __METHOD__ . ": Apply not found. (user_id={$userId}, apply_id={$applyId})" );
                return $this->errorNotFound( config('api.response.code.apply_not_found') );
            }

            DB::beginTransaction();

            // 応募ステータスを書類選考中に更新
            $this->applyRepository->update($apply, ['status' => config('const.apply_status.document_selection')]);

            // 書類作成
            $documentParams = [
                'apply_id'        => $applyId,
                'submission_date' => $postedParams['submission_date'],
                'memo'            => $postedParams['memo'],
            ];
            $document = $this->documentRepository->create($documentParams);

            // ファイルをストレージへ保存 && DBにファイルアップロード情報を保存
            foreach ( $postedParams['files'] as $file ) {
                $fileName = $this->getFileName($file);
                $filePath = $this->getFilePath($userId, $fileName);
                $this->uploadFile($filePath, $file);

                $fileParams = [
                    'document_id' => $document->document_id,
                    'name'        => $fileName,
                    'path'        => $filePath,
                ];
                $this->fileRepository->create($fileParams);
            }

            DB::commit();

        } catch ( Exception $e ) {
            DB::rollBack();

            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }

        return $document;
    }

    private function getExtensionFromBase64(string $base64String): string|null
    {
        if ( preg_match('/^data:(\w+\/([\w+]+));base64,/', $base64String, $matches) ) {
            return $matches[2]; // MIMEタイプのサブタイプ（拡張子）
        }

        return null;
    }

    private function getFileName(string $base64File): string
    {
        $extension = $this->getExtensionFromBase64($base64File);
        if ( $extension === null ) {
            throw new \Exception('無効なファイルです。');
        }

        $fileName =  date('YmdHis') . '_' . Str::random() . '.' . $extension;

        return $fileName;
    }

    private function getFilePath(int $userId, string $fileName): string
    {
        return "/documents/{$userId}/{$fileName}";
    }

    private function uploadFile(string $filePath, string $base64File): bool
    {
        $binaryFile = base64_decode($base64File);
        if ( $binaryFile === false ) {
            throw new \Exception('無効なファイルです。');
        }

        $result = Storage::disk('s3')->put($filePath, $binaryFile);
        if ( $result === false ) {
            throw new \Exception('ファイルのアップロードに失敗しました。');
        }

        return true;
    }
}
