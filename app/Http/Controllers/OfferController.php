<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfferResource;
use App\Services\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /** @var \App\Services\OfferService */
    private $service;

    public function __construct(OfferService $offerService)
    {
        $this->service = $offerService;
    }

    public function show(Request $request, int $applyId, int $offerId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $offer = $this->service->show($userId, $applyId, $offerId);
        if ( isset($offer['error_code']) ) {
            if ( $offer['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $offer['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $offer['error_code'] == config('api.response.code.offer_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.offer_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new OfferResource($offer) );
    }

    public function store(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only([
            'offer_date',
            'salary',
            'condition',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $offer = $this->service->store($userId, $applyId, $postedParams);
        if ( isset($offer['error_code']) ) {
            if ( $offer['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $offer['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new OfferResource($offer) );
    }

    public function delete(Request $request, int $applyId, int $offerId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $offer = $this->service->delete($userId, $applyId, $offerId);
        if ( isset($offer['error_code']) ) {
            if ( $offer['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $offer['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $offer['error_code'] == config('api.response.code.offer_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.offer_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new OfferResource($offer) );
    }
}
