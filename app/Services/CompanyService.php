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

    public function index(int $userId, array $postedParams): array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $result = $this->companyRepository->search($userId, $postedParams);

            return $result;

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
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);
            if ( $company === null ) {
                Log::error( __METHOD__ . ": Company not found. (user_id={$userId}, company_id={$companyId})" );
                return $this->errorNotFound( config('api.response.code.company_not_found') );
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
                return $this->errorNotFound( config('api.response.code.user_not_found') );
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
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function update(int $userId, int $companyId, array $postedParams): \App\Models\Company|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);
            if ( $company === null ) {
                Log::error( __METHOD__ . ": Company not found. (user_id={$userId}, company_id={$companyId})" );
                return $this->errorNotFound( config('api.response.code.company_not_found') );
            }

            $isSuccess = $this->companyRepository->update($company, $postedParams);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed update company. (company_id={$companyId}, user_id={$userId}, posted_params=" . json_encode($postedParams, JSON_UNESCAPED_UNICODE) . ")");
            }

            return $company->fresh();

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function delete(int $userId, int $companyId): \App\Models\Company|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorNotFound( config('api.response.code.user_not_found') );
            }

            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);
            if ( $company === null ) {
                Log::error( __METHOD__ . ": Company not found. (user_id={$userId}, company_id={$companyId})" );
                return $this->errorNotFound( config('api.response.code.company_not_found') );
            }

            $isSuccess = $this->companyRepository->delete($company);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed delete company. (user_id={$userId}, company_id={$companyId})");
            }

            return $company;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }
}
