<?php

namespace App\Repositories;

use App\Models\FinalResult;

class FinalResultRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( FinalResult::class );
    }
}
