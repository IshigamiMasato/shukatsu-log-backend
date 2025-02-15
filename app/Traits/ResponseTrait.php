<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    public function responseSuccess(mixed $resource): \Illuminate\Http\JsonResponse
    {
        return response()->json($resource, Response::HTTP_OK);
    }

    public function responseBadRequest(array $errors = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "code" => config('api.response.code.bad_request'),
            "errors" => $errors,
        ], Response::HTTP_BAD_REQUEST);
    }

    public function responseUnauthorized(?string $code = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "code" => $code ? $code : config('api.response.code.unauthorized'),
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function responseNotFound(?string $code = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "code" => $code ? $code : config('api.response.code.not_found'),
        ], Response::HTTP_NOT_FOUND);
    }

    public function responseInternalServerError(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "code" => config('api.response.code.internal_server_error')
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
