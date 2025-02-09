<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository
{
    public function create(array $params): Company
    {
        return Company::create($params);
    }
}
