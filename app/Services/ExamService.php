<?php

namespace App\Services;

use App\Repositories\ApplyRepository;
use App\Repositories\ExamRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExamService extends Service
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    /** @var \App\Repositories\ApplyRepository */
    private $applyRepository;

    /** @var \App\Repositories\ExamRepository */
    private $examRepository;

    public function __construct(
        UserRepository $userRepository,
        ApplyRepository $applyRepository,
        ExamRepository $examRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->applyRepository = $applyRepository;
        $this->examRepository = $examRepository;
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'exam_date' => ['required', 'date'],
            'content'   => ['required', 'string'],
            'memo'      => ['nullable', 'string'],
        ]);

        $validator->setAttributeNames(['content' => '試験内容']);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, int $applyId, array $postedParams): \App\Models\Exam|array
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

            // 応募ステータスを筆記試験選考中に更新
            $this->applyRepository->update($apply, ['status' => config('const.apply_status.exam_selection')]);

            $examParams = [
                'apply_id'  => $applyId,
                'exam_date' => $postedParams['exam_date'],
                'content'   => $postedParams['content'],
                'memo'      => $postedParams['memo'],
            ];
            $exam = $this->examRepository->create($examParams);

            DB::commit();

        } catch ( Exception $e ) {
            DB::rollBack();
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }

        return $exam;
    }
}
