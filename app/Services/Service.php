<?php

namespace App\Services;

class Service
{
    public function errorUserNotFound(): array
    {
        return [
            'error_code' => config('api.response.code.user_not_found')
        ];
    }

    public function errorInternalServerError(): array
    {
        return [
            'error_code' => config('api.response.code.internal_server_error')
        ];
    }
}
