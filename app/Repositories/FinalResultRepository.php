<?php

namespace App\Repositories;

use App\Models\FinalResult;

class FinalResultRepository
{
    public function findBy(array $params): FinalResult|null
    {
        return FinalResult::where($params)->first();
    }

    public function create(array $params): FinalResult
    {
        return FinalResult::create($params);
    }

    public function delete(FinalResult $finalResult): bool
    {
        return $finalResult->delete();
    }
}
