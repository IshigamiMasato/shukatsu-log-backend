<?php

namespace App\Repositories;

use App\Models\Interview;

class InterviewRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Interview::class );
    }
}
