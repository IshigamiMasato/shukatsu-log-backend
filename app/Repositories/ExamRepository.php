<?php

namespace App\Repositories;

use App\Models\Exam;

class ExamRepository
{
    public function create(array $params): Exam
    {
        return Exam::create($params);
    }
}
