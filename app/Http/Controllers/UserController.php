<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /** @var \App\Services\UserService */
    private $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $user = $this->service->show($userId);

        if ( isset($user['error_code']) ) {
            if ( $user['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new UserResource($user) );
    }
}
