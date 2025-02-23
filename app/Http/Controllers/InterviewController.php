<?php

namespace App\Http\Controllers;

use App\Http\Resources\InterviewResource;
use App\Services\InterviewService;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    /** @var \App\Services\InterviewService */
    private $service;

    public function __construct(InterviewService $interviewService)
    {
        $this->service = $interviewService;
    }

    public function show(Request $request, int $applyId, int $interviewId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $interview = $this->service->show($userId, $applyId, $interviewId);
        if ( isset($interview['error_code']) ) {
            if ( $interview['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $interview['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $interview['error_code'] == config('api.response.code.interview_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.interview_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new InterviewResource($interview) );
    }

    public function store(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only([
            'interview_date',
            'interviewer_info',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $interview = $this->service->store($userId, $applyId, $postedParams);
        if ( isset($interview['error_code']) ) {
            if ( $interview['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $interview['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new InterviewResource($interview) );
    }

    public function update(Request $request, int $applyId, int $interviewId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only(['interview_date', 'interviewer_info', 'memo']);

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $interview = $this->service->update($userId, $applyId, $interviewId, $postedParams);
        if ( isset($interview['error_code']) ) {
            if ( $interview['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $interview['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $interview['error_code'] == config('api.response.code.interview_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.interview_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new InterviewResource($interview) );
    }

    public function delete(Request $request, int $applyId, int $interviewId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $interview = $this->service->delete($userId, $applyId, $interviewId);
        if ( isset($interview['error_code']) ) {
            if ( $interview['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $interview['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $interview['error_code'] == config('api.response.code.interview_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.interview_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new InterviewResource($interview) );
    }
}
