<?php

namespace App\Repositories;

use App\Models\Company;

class CompanyRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Company::class );
    }
}
