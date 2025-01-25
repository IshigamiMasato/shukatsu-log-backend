<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show(int $userId): \Illuminate\Http\JsonResponse
    {
        $user = $this->userRepository->find($userId);

        if ($user === null) {
            return response()->notFound();
        }

        return response()->ok($user);
    }
}
