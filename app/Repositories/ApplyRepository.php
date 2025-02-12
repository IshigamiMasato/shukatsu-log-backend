<?php

namespace App\Repositories;

use App\Models\Apply;

class ApplyRepository
{
    public function create(array $params): Apply
    {
        return Apply::create($params);
    }
}
