<?php

namespace App\Repositories;

use App\Models\FinalResult;

/**
 * @extends Repository<FinalResult>
 */
class FinalResultRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( FinalResult::class );
    }
}
