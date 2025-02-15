<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
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

        $companies = $this->service->index($userId);
        if ( isset($companies['error_code']) ) {
            if ( $companies['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( CompanyResource::collection($companies) );
    }

    public function show(Request $request, int $companyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $company = $this->service->show($userId, $companyId);
        if ( isset($company['error_code']) ) {
            if ( $company['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $company['error_code'] == config('api.response.code.company_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.company_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new CompanyResource($company) );
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

    public function update(Request $request, int $companyId): \Illuminate\Http\JsonResponse
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

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['errors']) ) {
            return response()->badRequest( errors: $result['errors'] );
        }

        return $this->service->update($userId, $companyId, $postedParams);
    }

    public function delete(Request $request, int $companyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        return $this->service->delete($userId, $companyId);
    }
}
