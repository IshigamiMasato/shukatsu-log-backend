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

        $applies = $this->service->index($userId);
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

        return $this->service->show($userId, $applyId);
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
        if ( isset($result['errors']) ) {
            return response()->badRequest( errors: $result['errors'] );
        }

        return $this->service->store($userId, $postedParams);
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
            return response()->badRequest( errors: $result['errors'] );
        }

        return $this->service->update($userId, $applyId, $postedParams);
    }

    public function delete(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        return $this->service->delete($userId, $applyId);
    }
}
