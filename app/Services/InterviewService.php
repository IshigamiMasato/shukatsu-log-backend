<?php

namespace App\Services;

use App\Repositories\ApplyRepository;
use App\Repositories\InterviewRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InterviewService extends Service
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    /** @var \App\Repositories\ApplyRepository */
    private $applyRepository;

    /** @var \App\Repositories\InterviewRepository */
    private $interviewRepository;

    public function __construct(
        UserRepository $userRepository,
        ApplyRepository $applyRepository,
        InterviewRepository $interviewRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->applyRepository = $applyRepository;
        $this->interviewRepository = $interviewRepository;
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'interview_date'   => ['required', 'date'],
            'interviewer_info' => ['nullable', 'string'],
            'memo'             => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, int $applyId, array $postedParams): \App\Models\Interview|array
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

            // 応募ステータスを面接選考中に更新
            $this->applyRepository->update($apply, ['status' => config('const.apply_status.interview_selection')]);

            $interviewParams = [
                'apply_id'         => $applyId,
                'interview_date'   => $postedParams['interview_date'],
                'interviewer_info' => $postedParams['interviewer_info'],
                'memo'             => $postedParams['memo'],
            ];
            $interview = $this->interviewRepository->create($interviewParams);

            DB::commit();

        } catch ( Exception $e ) {
            DB::rollBack();
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }

        return $interview;
    }

    public function delete(int $userId, int $applyId, int $interviewId): \App\Models\Interview|array
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

            $interview = $this->interviewRepository->findBy(['apply_id' => $applyId, 'interview_id' => $interviewId]);
            if ( $interview === null ) {
                Log::error( __METHOD__ . ": Interview not found. (user_id={$userId}, apply_id={$applyId}, interview_id={$interviewId})" );
                return $this->errorNotFound( config('api.response.code.interview_not_found') );
            }

            $isSuccess = $this->interviewRepository->delete($interview);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed delete interview. (user_id={$userId}, apply_id={$applyId}, interview_id={$interviewId})");
            }

            return $interview;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }
}
