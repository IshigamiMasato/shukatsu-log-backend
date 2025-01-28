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

    public function refreshToken(Request $request): \Illuminate\Http\JsonResponse
    {
        $jwt = $request->bearerToken();

        if ($jwt === null) {
            return response()->unauthorized();
        }

        return $this->service->refreshToken($jwt);
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $jwt = $request->bearerToken();
        $jti = $request->jti;

        return $this->service->logout($jwt, $jti);
    }

    public function checkAuth(Request $request): \Illuminate\Http\JsonResponse
    {
        // 認証middlewareに引っ掛からなければ200のみ返却
        return response()->ok();
    }
}
