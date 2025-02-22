<?php

namespace App\Repositories;

use App\Models\FinalResult;

class FinalResultRepository
{
    public function create(array $params): FinalResult
    {
        return FinalResult::create($params);
    }
}
