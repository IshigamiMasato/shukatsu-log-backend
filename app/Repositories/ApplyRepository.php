<?php

namespace App\Repositories;

use App\Models\Apply;

class ApplyRepository
{
    public function getBy(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return Apply::where($params)->get();
    }

    public function create(array $params): Apply
    {
        return Apply::create($params);
    }
}
