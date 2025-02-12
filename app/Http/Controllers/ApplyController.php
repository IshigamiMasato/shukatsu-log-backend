<?php

namespace App\Http\Controllers;

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

        return $this->service->index($userId);
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
}
