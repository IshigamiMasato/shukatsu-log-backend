<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplyResource;
use App\Services\ApplyService;
use Illuminate\Http\Request;

class ApplyController extends Controller
{
    /** @var \App\Services\ApplyService */
    private $service;

    public function __construct(ApplyService $applyService)
    {
        $this->service = $applyService;
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;
        $postedParams = $request->all();

        $applies = $this->service->index($userId, $postedParams);
        if ( isset($applies['error_code']) ) {
            if ( $applies['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( ApplyResource::collection($applies) );
    }

    public function show(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $apply = $this->service->show($userId, $applyId);
        if ( isset($apply['error_code']) ) {
            if ( $apply['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $apply['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new ApplyResource($apply) );
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only([
            'company_id',
            'status',
            'occupation',
            'apply_route',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $apply = $this->service->store($userId, $postedParams);
        if ( isset($apply['error_code']) ) {
            if ( $apply['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $apply['error_code'] == config('api.response.code.company_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.company_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new ApplyResource($apply) );
    }

    public function update(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only([
            'status',
            'occupation',
            'apply_route',
            'memo',
        ]);

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['errors']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $apply = $this->service->update($userId, $applyId, $postedParams);
        if ( isset($apply['error_code']) ) {
            if ( $apply['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $apply['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new ApplyResource($apply) );
    }

    public function delete(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $apply = $this->service->delete($userId, $applyId);
        if ( isset($apply['error_code']) ) {
            if ( $apply['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $apply['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new ApplyResource($apply) );
    }

    public function getProcess(Request $request, int $applyId)
    {
        $userId = $request->user_id;

        $process = $this->service->getProcess($userId, $applyId);
        if ( isset($process['error_code']) ) {
            if ( $process['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $process['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( $process );
    }

    public function getStatusSummary(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $statusSummary = $this->service->getStatusSummary($userId);
        if ( isset($statusSummary['error_code']) ) {
            if ( $statusSummary['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( $statusSummary );
    }
}
