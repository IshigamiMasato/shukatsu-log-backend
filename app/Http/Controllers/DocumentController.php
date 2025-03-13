<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use App\Http\Resources\FileResource;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /** @var \App\Services\DocumentService */
    private $service;

    public function __construct(DocumentService $documentService)
    {
        $this->service = $documentService;
    }

    public function show(Request $request, int $applyId, int $documentId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $document = $this->service->show($userId, $applyId, $documentId);
        if ( isset($document['error_code']) ) {
            if ( $document['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.document_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.document_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new DocumentResource($document) );
    }

    public function store(Request $request, int $applyId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only([
            'submission_date',
            'files',
            'memo'
        ]);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $document = $this->service->store($userId, $applyId, $postedParams);
        if ( isset($document['error_code']) ) {
            if ( $document['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new DocumentResource($document) );
    }

    public function update(Request $request, int $applyId, int $documentId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only(['submission_date', 'files', 'memo']);

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $document = $this->service->update($userId, $applyId, $documentId, $postedParams);
        if ( isset($document['error_code']) ) {
            if ( $document['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.document_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.document_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new DocumentResource($document) );
    }

    public function delete(Request $request,int $applyId, int $documentId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $document = $this->service->delete($userId, $applyId, $documentId);
        if ( isset($document['error_code']) ) {
            if ( $document['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $document['error_code'] == config('api.response.code.document_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.document_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new DocumentResource($document) );
    }

    public function downloadFile(Request $reqeust, int $applyId, int $documentId, int $fileId): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
    {
        $userId = $reqeust->user_id;

        $result = $this->service->downloadFile($userId, $applyId, $documentId, $fileId);
        if ( is_array($result) && isset($result['error_code']) ) {
            if ( $result['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $result['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $result['error_code'] == config('api.response.code.document_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.document_not_found') );
            }

            if ( $result['error_code'] == config('api.response.code.file_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.file_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $result;
    }

    public function deleteFile(Request $reqeust, int $applyId, int $documentId, int $fileId): \Illuminate\Http\JsonResponse
    {
        $userId = $reqeust->user_id;

        $file = $this->service->deleteFile($userId, $applyId, $documentId, $fileId);
        if ( isset($file['error_code']) ) {
            if ( $file['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $file['error_code'] == config('api.response.code.apply_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.apply_not_found') );
            }

            if ( $file['error_code'] == config('api.response.code.document_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.document_not_found') );
            }

            if ( $file['error_code'] == config('api.response.code.file_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.file_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new FileResource($file) );
    }
}
