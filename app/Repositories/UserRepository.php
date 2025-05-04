<?php

namespace App\Repositories;

use App\Models\User;

/**
 * @extends Repository<User>
 */
class UserRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( User::class );
    }
}
