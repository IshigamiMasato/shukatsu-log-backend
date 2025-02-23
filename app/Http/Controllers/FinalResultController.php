<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinalResultResource;
use App\Services\FinalResultService;
use Illuminate\Http\Request;

class FinalResultController extends Controller
{
    /** @var \App\Services\FinalResultService */
    private $service;

    public function __construct(FinalResultService $finalResultService)
    {
        $this->service = $finalResultService;
    }

    public function show(Request $request, int $applyId, int $finalResultId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $finalResult = $this->service->show($userId, $applyId, $finalResultId);
        if ( isset($finalResult['error_code']) ) {
            if ( $finalResult['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $finalResult['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $finalResult['error_code'] == config('api.response.code.final_result_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.final_result_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new FinalResultResource($finalResult) );
    }

    public function store(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only([
            'status',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $finalResult = $this->service->store($userId, $applyId, $postedParams);
        if ( isset($finalResult['error_code']) ) {
            if ( $finalResult['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $finalResult['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new FinalResultResource($finalResult) );
    }

    public function update(Request $request, int $applyId, int $finalResultId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only(['status', 'memo']);

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $finalResult = $this->service->update($userId, $applyId, $finalResultId, $postedParams);
        if ( isset($finalResult['error_code']) ) {
            if ( $finalResult['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $finalResult['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $finalResult['error_code'] == config('api.response.code.final_result_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.final_result_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new FinalResultResource($finalResult) );
    }

    public function delete(Request $request,int $applyId, int $finalResultId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $finalResult = $this->service->delete($userId, $applyId, $finalResultId);
        if ( isset($finalResult['error_code']) ) {
            if ( $finalResult['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $finalResult['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $finalResult['error_code'] == config('api.response.code.final_result_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.final_result_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new FinalResultResource($finalResult) );
    }
}
