<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyCollection;
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
        $postedParams = $request->all();

        $result = $this->service->index($userId, $postedParams);
        if ( isset($result['error_code']) ) {
            if ( $result['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new CompanyCollection($result['companies'], $result['total']) );
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
            'business_description',
            'benefit',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $company = $this->service->store($userId, $postedParams);
        if ( isset($company['error_code']) ) {
            if ( $company['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new CompanyResource($company) );
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
            'business_description',
            'benefit',
            'memo'
        ]);

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $company = $this->service->update($userId, $companyId, $postedParams);
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

    public function delete(Request $request, int $companyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $company = $this->service->delete($userId, $companyId);
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
}
