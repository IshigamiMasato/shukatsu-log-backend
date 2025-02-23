<?php

namespace App\Repositories;

use App\Models\Apply;

class ApplyRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Apply::class );
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
}
