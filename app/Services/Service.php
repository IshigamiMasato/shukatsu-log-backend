<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;

class Service
{
    public function errorBadRequest(array $errors = []): array
    {
        return [
            'error_code' => config('api.response.code.bad_request'),
            'errors' => $errors,
        ];
    }

    public function errorUnAuthorized()
    {
        return [
            'error_code' => config('api.response.code.unauthorized')
        ];
    }

    public function errorUserNotFound(): array
    {
        return [
            'error_code' => config('api.response.code.user_not_found')
        ];
    }

    public function errorEventNotFound(): array
    {
        return [
            'error_code' => config('api.response.code.event_not_found')
        ];
    }

    public function errorCompanyNotFound(): array
    {
        return [
            'error_code' => config('api.response.code.company_not_found')
        ];
    }

    public function errorApplyNotFound(): array
    {
        return [
            'error_code' => config('api.response.code.apply_not_found')
        ];
    }

    public function errorInternalServerError(): array
    {
        return [
            'error_code' => config('api.response.code.internal_server_error')
        ];
    }
}
