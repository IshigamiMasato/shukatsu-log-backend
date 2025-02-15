<?php

namespace App\Services;

use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompanyService extends Service
{
    /** @var \App\Repositories\CompanyRepository */
    private $companyRepository;

    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    public function __construct(
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
    ) {
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
    }

    public function index(int $userId): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorUserNotFound();
            }

            $companies = $this->companyRepository->getBy(['user_id' => $userId]);

            return $companies;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function show(int $userId, int $companyId): \App\Models\Company|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorUserNotFound();
            }

            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);
            if ( $company === null ) {
                Log::error( __METHOD__ . ": Company not found. (user_id={$userId}, company_id={$companyId})" );
                return $this->errorCompanyNotFound();
            }

            return $company;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
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
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, array $postedParams): \App\Models\Company|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorUserNotFound();
            }

            $params = array_merge(['user_id' => $userId], $postedParams);

            $company = $this->companyRepository->create($params);

            return $company;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
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

    public function delete(int $userId, int $companyId): \Illuminate\Http\JsonResponse
    {
        try {
            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);

            if ( $company === null ) {
                return response()->notFound();
            }

            $isSuccess = $this->companyRepository->delete($company);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed delete company. (company_id={$companyId})");
            }

            return response()->ok($company);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }
}
