<?php

namespace App\Services;

use App\Repositories\ApplyRepository;
use App\Repositories\FinalResultRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FinalResultService extends Service
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    /** @var \App\Repositories\ApplyRepository */
    private $applyRepository;

    /** @var \App\Repositories\FinalResultRepository */
    private $finalResultRepository;

    public function __construct(
        UserRepository $userRepository,
        ApplyRepository $applyRepository,
        FinalResultRepository $finalResultRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->applyRepository = $applyRepository;
        $this->finalResultRepository = $finalResultRepository;
    }

    public function show(int $userId, int $applyId, int $finalResultId): \App\Models\FinalResult|array
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

            $finalResult = $this->finalResultRepository->findBy(['apply_id' => $applyId, 'final_result_id' => $finalResultId]);
            if ( $finalResult === null ) {
                Log::error( __METHOD__ . ": Final_Result not found. (user_id={$userId}, apply_id={$applyId}, final_result_id={$finalResultId})" );
                return $this->errorNotFound( config('api.response.code.final_result_not_found') );
            }

            return $finalResult;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'status' => ['required', 'int', Rule::in( (config('const.final_results.status')) )],
            'memo'   => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, int $applyId, array $postedParams): \App\Models\FinalResult|array
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

            // 応募ステータスを選考終了に更新
            $this->applyRepository->update($apply, ['status' => config('const.apply_status.final')]);

            $finalResultParams = [
                'apply_id' => $applyId,
                'status'   => $postedParams['status'],
                'memo'     => $postedParams['memo'],
            ];
            $finalResult = $this->finalResultRepository->create($finalResultParams);

            DB::commit();

        } catch ( Exception $e ) {
            DB::rollBack();
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }

        return $finalResult;
    }

    public function validateUpdate(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'status' => ['required', 'int', Rule::in( (config('const.final_results.status')) )],
            'memo'   => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function update(int $userId, int $applyId, int $finalResultId, array $postedParams): \App\Models\FinalResult|array
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

            $finalResult = $this->finalResultRepository->findBy(['apply_id' => $applyId, 'final_result_id' => $finalResultId]);
            if ( $finalResult === null ) {
                Log::error( __METHOD__ . ": Final_Result not found. (user_id={$userId}, apply_id={$applyId}, final_result_id={$finalResultId})" );
                return $this->errorNotFound( config('api.response.code.final_result_not_found') );
            }

            $isSuccess = $this->finalResultRepository->update($finalResult, $postedParams);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed update final_result. (final_result_id={$finalResultId}, user_id={$userId}, posted_params=" . json_encode($postedParams, JSON_UNESCAPED_UNICODE) . ")");
            }

            return $finalResult;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function delete(int $userId, int $applyId, int $finalResultId): \App\Models\FinalResult|array
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

            $finalResult = $this->finalResultRepository->findBy(['apply_id' => $applyId, 'final_result_id' => $finalResultId]);
            if ( $finalResult === null ) {
                Log::error( __METHOD__ . ": Final_Result not found. (user_id={$userId}, apply_id={$applyId}, final_result_id={$finalResultId})" );
                return $this->errorNotFound( config('api.response.code.final_result_not_found') );
            }

            $isSuccess = $this->finalResultRepository->delete($finalResult);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed delete final_result. (user_id={$userId}, apply_id={$applyId}, final_result_id={$finalResultId})");
            }

            return $finalResult;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }
}
