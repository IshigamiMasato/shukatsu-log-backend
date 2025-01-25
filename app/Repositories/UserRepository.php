<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function find(int $userId): User|null
    {
        return User::find($userId);
    }

    public function findBy(array $params): User|null
    {
        return User::where($params)->first();
    }
}
