<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompanyService
{
    /** @var \App\Repositories\CompanyRepository */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function index(int $userId): \Illuminate\Http\JsonResponse
    {
        try {
            $companies = $this->companyRepository->getBy(['user_id' => $userId]);

            return response()->ok($companies);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }

    public function show(int $userId, int $companyId): \Illuminate\Http\JsonResponse
    {
        $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);

        if ($company === null) {
            return response()->notFound();
        }

        return response()->ok($company);
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'name'            => ['required', 'string'],
            'url'             => ['nullable', 'string', 'url'],
            'president'       => ['nullable', 'string'],
            'address'         => ['nullable', 'string'],
            'establish_date'  => ['nullable', 'date'],
            'employee_number' => ['nullable', 'int'],
            'listing_class'   => ['nullable', 'string'],
            'benefit'         => ['nullable', 'string'],
            'memo'            => ['nullable', 'string'],
        ]);

        $validator->setAttributeNames(['name' => '企業名']);

        if ( $validator->fails() ) {
            return ['errors' => $validator->errors()->getMessages()];
        }

        return true;
    }

    public function store(int $userId, array $postedParams): \Illuminate\Http\JsonResponse
    {
        try {
            $params = array_merge(['user_id' => $userId], $postedParams);

            $company = $this->companyRepository->create($params);

            return response()->ok($company->fresh());

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }

    public function validateUpdate(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'name'            => ['required', 'string'],
            'url'             => ['nullable', 'string', 'url'],
            'president'       => ['nullable', 'string'],
            'address'         => ['nullable', 'string'],
            'establish_date'  => ['nullable', 'date'],
            'employee_number' => ['nullable', 'int'],
            'listing_class'   => ['nullable', 'string'],
            'benefit'         => ['nullable', 'string'],
            'memo'            => ['nullable', 'string'],
        ]);

        $validator->setAttributeNames(['name' => '企業名']);

        if ( $validator->fails() ) {
            return ['errors' => $validator->errors()->getMessages()];
        }

        return true;
    }

    public function update(int $userId, int $companyId, array $postedParams): \Illuminate\Http\JsonResponse
    {
        try {
            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);

            if ( $company === null ) {
                return response()->notFound();
            }

            $isSuccess = $this->companyRepository->update($company, $postedParams);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed update company. (company_id={$companyId})");
            }

            return response()->ok($company->fresh());

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }
}
