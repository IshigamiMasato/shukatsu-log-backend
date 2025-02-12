<?php

namespace App\Services;

use App\Repositories\ApplyRepository;
use App\Repositories\CompanyRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApplyService
{
    /** @var \App\Repositories\ApplyRepository */
    private $applyRepository;

    /** @var \App\Repositories\CompanyRepository */
    private $companyRepository;

    public function __construct(
        ApplyRepository $applyRepository,
        CompanyRepository $companyRepository,
    ) {
        $this->applyRepository = $applyRepository;
        $this->companyRepository = $companyRepository;
    }

    public function index(int $userId): \Illuminate\Http\JsonResponse
    {
        try {
            $applies = $this->applyRepository->getBy(['user_id' => $userId]);

            return response()->ok($applies);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }

    public function show(int $userId, int $applyId): \Illuminate\Http\JsonResponse
    {
        $apply = $this->applyRepository->findBy(['user_id' => $userId, 'apply_id' => $applyId]);

        if ( $apply === null ) {
            return response()->notFound();
        }

        return response()->ok($apply);
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'company_id'  => ['required', 'int', 'exists:companies,company_id'],
            'status'      => ['required', 'int', Rule::in(array_values(config('const.apply_status')))],
            'occupation'  => ['nullable', 'string'],
            'apply_route' => ['nullable', 'string'],
            'memo'        => ['nullable', 'string'],
        ]);

        $validator->setAttributeNames(['status' => '選考ステータス']);

        if ( $validator->fails() ) {
            return ['errors' => $validator->errors()->getMessages()];
        }

        return true;
    }

    public function store(int $userId, array $postedParams): \Illuminate\Http\JsonResponse
    {
        try {
            $companyId = $postedParams['company_id'];

            // 適切な企業か確認
            $company = $this->companyRepository->findBy(['user_id' => $userId, 'company_id' => $companyId]);

            if ( $company === null ) {
                Log::error( __METHOD__ . ": company not found. (company_id={$companyId}, user_id={$userId})");
                return response()->notFound();
            }

            $params = array_merge(['user_id' => $userId], $postedParams);

            $apply = $this->applyRepository->create($params);

            return response()->ok($apply->fresh());

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }
}
