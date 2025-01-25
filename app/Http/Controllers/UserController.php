<?php

namespace App\Http\Controllers;

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

        return $this->service->show($userId);
    }
}
