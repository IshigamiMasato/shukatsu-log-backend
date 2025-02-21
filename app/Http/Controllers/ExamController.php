<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Services\ExamService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /** @var \App\Services\ExamService */
    private $service;

    public function __construct(ExamService $examService)
    {
        $this->service = $examService;
    }

    public function store(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only([
            'exam_date',
            'content',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $exam = $this->service->store($userId, $applyId, $postedParams);
        if ( isset($document['error_code']) ) {
            if ( $document['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new ExamResource($exam) );
    }
}
