<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    public function findBy(array $params): Company|null
    {
        return Company::where($params)->first();
    }

    public function getBy(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return Company::where($params)->get();
    }

    public function create(array $params): Company
    {
        return Company::create($params);
    }
}
