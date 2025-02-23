<?php

namespace App\Repositories;

use App\Models\Exam;

class ExamRepository
{
    public function findBy(array $params): Exam|null
    {
        return Exam::where($params)->first();
    }

    public function create(array $params): Exam
    {
        return Exam::create($params);
    }

    public function update(Exam $exam, array $postedParams): bool
    {
        return $exam->fill($postedParams)->save();
    }

    public function delete(Exam $exam): bool
    {
        return $exam->delete();
    }
}
