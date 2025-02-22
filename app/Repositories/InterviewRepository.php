<?php

namespace App\Repositories;

use App\Models\Interview;

class InterviewRepository
{
    public function create(array $params): Interview
    {
        return Interview::create($params);
    }
}
