<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class UserService
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show(int $userId): \App\Models\User|array
    {
        try {
            $user = $this->userRepository->find($userId);

            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return ['error_code' => config('api.response.code.user_not_found')];
            }

            return $user;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return ['error_code' => config('api.response.code.internal_server_error')];
        }
    }
}
