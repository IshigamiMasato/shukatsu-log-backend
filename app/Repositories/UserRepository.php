<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findBy(array $params): User|null
    {
        return User::where($params)->first();
    }
}
