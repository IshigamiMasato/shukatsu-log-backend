<?php

namespace App\Services;

use App\Models\Apply;
use App\Repositories\ApplyRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApplyService extends Service
{
    /** @var \App\Repositories\ApplyRepository */
    private $applyRepository;

    /** @var \App\Repositories\CompanyRepository */
    private $companyRepository;

    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    public function __construct(
        ApplyRepository $applyRepository,
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
    ) {
        $this->applyRepository = $applyRepository;
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
    }

    public function index(int $userId, array $postedParams): array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $result = $this->applyRepository->search($userId, $postedParams);

            return $result;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function show(int $userId, int $applyId): \App\Models\Apply|array
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

            return $apply;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'company_id'  => ['required', 'int', 'exists:companies,company_id'],
            'occupation'  => ['required', 'string'],
            'apply_route' => ['nullable', 'string'],
            'memo'        => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, array $postedParams): \App\Models\Apply|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            // 適切な企業か確認
            $companyId = $postedParams['company_id'];
            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);
            if ( $company === null ) {
                Log::error( __METHOD__ . ": Company not found. (user_id={$userId}, company_id={$companyId})" );
                return $this->errorNotFound( config('api.response.code.company_not_found') );
            }

            $params = array_merge(['user_id' => $userId], $postedParams);

            $apply = $this->applyRepository->create($params);

            return $apply;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function validateUpdate(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'status'      => ['required', 'int', Rule::in( config('const.applies.status') )],
            'occupation'  => ['required', 'string'],
            'apply_route' => ['nullable', 'string'],
            'memo'        => ['nullable', 'string'],
        ]);

        $validator->setAttributeNames(['status' => '選考ステータス']);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function update(int $userId, int $applyId, array $postedParams): \App\Models\Apply|array
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

            $isSuccess = $this->applyRepository->update($apply, $postedParams);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed update apply. (apply_id={$applyId}, user_id={$userId}, posted_params=" . json_encode($postedParams, JSON_UNESCAPED_UNICODE) . ")");
            }

            return $apply->fresh();

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function delete(int $userId, int $applyId): \App\Models\Apply|array
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

            $isSuccess = $this->applyRepository->delete($apply);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed delete apply. (user_id={$userId}, apply_id={$applyId})");
            }

            return $apply;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function getProcess(int $userId, int $applyId): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $apply = $this->applyRepository->findWithProcessBy(['user_id' => $userId, 'apply_id' => $applyId]);
            if ( $apply === null ) {
                Log::error( __METHOD__ . ": Apply not found. (user_id={$userId}, apply_id={$applyId})" );
                return $this->errorNotFound( config('api.response.code.apply_not_found') );
            }

            $process = $this->convertProcess($apply);

            return $process;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    /**
     * フロント用に選考プロセスを変換
     */
    private function convertProcess(Apply $apply): \Illuminate\Database\Eloquent\Collection
    {
        $documents = $apply->documents->map(function ($document) {
            return $document->setAttribute( 'type', config('const.applies.status.document_selection') );
        });
        $exams = $apply->exams->map(function ($exam) {
            return $exam->setAttribute( 'type', config('const.applies.status.exam_selection') );
        });
        $interviews = $apply->interviews->map(function ($exam) {
            return $exam->setAttribute( 'type', config('const.applies.status.interview_selection') );
        });
        $offers = $apply->offers->map(function ($offer) {
            return $offer->setAttribute( 'type', config('const.applies.status.offer') );
        });
        $finalResults = $apply->finalResults->map(function ($offer) {
            return $offer->setAttribute( 'type', config('const.applies.status.final') );
        });

        // 応募書類、試験情報、面接情報、内定情報を各日付カラムの降順で並び替え
        $process = $documents
                    ->concat($exams)
                    ->concat($interviews)
                    ->concat($offers);

        $sorted = $process->sortByDesc(function ($item) {
            if ($item instanceof \App\Models\Document) {
                return $item->submission_date;
            }

            if ($item instanceof \App\Models\Exam) {
                return $item->exam_date;
            }

            if ($item instanceof \App\Models\Interview) {
                return $item->interview_date;
            }

            if ($item instanceof \App\Models\Offer) {
                return $item->offer_date;
            }
        });

        // 選考終了情報は一番先頭に追加
        $sorted = $finalResults->concat($sorted);

        // 添字の振り直し
        $values = $sorted->values();

        return $values;
    }

    public function getStatusSummary(int $userId): Apply|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $statusSummary = $this->applyRepository->getStatusSummary($userId);

            return $statusSummary;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }
}
