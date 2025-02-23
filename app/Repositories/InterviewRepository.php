<?php

namespace App\Repositories;

use App\Models\Interview;

class InterviewRepository
{
    public function findBy(array $params): Interview|null
    {
        return Interview::where($params)->first();
    }

    public function create(array $params): Interview
    {
        return Interview::create($params);
    }

    public function update(Interview $interview, array $postedParams): bool
    {
        return $interview->fill($postedParams)->save();
    }

    public function delete(Interview $interview): bool
    {
        return $interview->delete();
    }
}
