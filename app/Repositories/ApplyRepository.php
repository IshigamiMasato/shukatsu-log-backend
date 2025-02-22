<?php

namespace App\Repositories;

use App\Models\Apply;

class ApplyRepository
{
    public function findBy(array $params): Apply|null
    {
        return Apply::where($params)->first();
    }

    public function findWithProcessBy(array $params): Apply|null
    {
        return Apply::query()
                    ->with([
                        'documents.files',
                        'exams',
                        'interviews',
                        'offers',
                        'finalResults',
                    ])
                    ->where($params)
                    ->first();
    }

    public function getBy(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return Apply::where($params)->get();
    }

    public function create(array $params): Apply
    {
        return Apply::create($params);
    }

    public function update(Apply $apply, array $postedParams): bool
    {
        return $apply->fill($postedParams)->save();
    }

    public function delete(Apply $apply): bool
    {
        return $apply->delete();
    }
}
