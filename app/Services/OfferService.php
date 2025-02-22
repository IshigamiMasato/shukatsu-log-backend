<?php

namespace App\Services;

use App\Repositories\ApplyRepository;
use App\Repositories\OfferRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OfferService extends Service
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    /** @var \App\Repositories\ApplyRepository */
    private $applyRepository;

    /** @var \App\Repositories\OfferRepository */
    private $offerRepository;

    public function __construct(
        UserRepository $userRepository,
        ApplyRepository $applyRepository,
        OfferRepository $offerRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->applyRepository = $applyRepository;
        $this->offerRepository = $offerRepository;
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'offer_date' => ['required', 'date'],
            'salary'     => ['nullable', 'int'],
            'condition'  => ['nullable', 'string'],
            'memo'       => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, int $applyId, array $postedParams): \App\Models\Offer|array
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

            // 応募ステータスを内定に更新
            $this->applyRepository->update($apply, ['status' => config('const.apply_status.offer')]);

            $offerParams = [
                'apply_id'   => $applyId,
                'offer_date' => $postedParams['offer_date'],
                'salary'     => $postedParams['salary'],
                'condition'  => $postedParams['condition'],
                'memo'       => $postedParams['memo'],
            ];
            $offer = $this->offerRepository->create($offerParams);

            DB::commit();

        } catch ( Exception $e ) {
            DB::rollBack();
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }

        return $offer;
    }
}
