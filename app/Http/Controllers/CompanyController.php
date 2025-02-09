<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /** @var \App\Services\CompanyService */
    private $service;

    public function __construct(CompanyService $companyService)
    {
        $this->service = $companyService;
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
            'name',
            'url',
            'president',
            'address',
            'establish_date',
            'employee_number',
            'listing_class',
            'benefit',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['errors']) ) {
            return response()->badRequest( errors: $result['errors'] );
        }

        return $this->service->store($userId, $postedParams);
    }
}
