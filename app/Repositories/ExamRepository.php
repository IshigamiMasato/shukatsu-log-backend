<?php

namespace App\Repositories;

use App\Models\Exam;

class ExamRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Exam::class );
    }
}
