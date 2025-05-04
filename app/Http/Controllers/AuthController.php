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
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $loginResult = $this->service->login($postedParams);
        if ( isset($loginResult['error_code']) ) {
            if ( $loginResult['error_code'] == config('api.response.code.unauthorized') ) {
                return $this->responseUnauthorized();
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( $loginResult );
    }

    public function refreshToken(Request $request): \Illuminate\Http\JsonResponse
    {
        $jwt = $request->bearerToken();

        if ($jwt === null) {
            return $this->responseUnauthorized();
        }

        $result = $this->service->refreshToken($jwt);
        if ( isset($result['error_code']) ) {
            if ( $result['error_code'] == config('api.response.code.invalid_refresh_token') ) {
                return $this->responseUnauthorized( config('api.response.code.invalid_refresh_token') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( $result );
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $jwt = $request->bearerToken();
        $jti = $request->jti;

        $result = $this->service->logout($jwt, $jti);
        if ( isset($result['error_code']) ) {
            return $this->responseInternalServerError();
        }

        return $this->responseSuccess([]);
    }
}
