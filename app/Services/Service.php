<?php

namespace App\Services;

class Service
{
    public function errorBadRequest(array $errors = []): array
    {
        return [
            'error_code' => config('api.response.code.bad_request'),
            'errors' => $errors,
        ];
    }

    public function errorUnAuthorized(?string $code = null): array
    {
        return [
            'error_code' => $code ? $code : config('api.response.code.unauthorized')
        ];
    }

    public function errorNotFound(?string $code = null): array
    {
        return [
            'error_code' => $code ? $code : config('api.response.code.not_found')
        ];
    }

    public function errorInternalServerError(): array
    {
        return [
            'error_code' => config('api.response.code.internal_server_error')
        ];
    }
}
