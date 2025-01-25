<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /** @var \App\Services\AuthService */
    private $service;

    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $postedParams = $request->only( ['email', 'password'] );

        $result = $this->service->validateLogin($postedParams);

        if ( isset($result['errors']) ) {
            return response()->badRequest( errors: $result['errors'] );
        }

        return $this->service->login($postedParams);
    }
}
