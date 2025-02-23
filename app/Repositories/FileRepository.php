<?php

namespace App\Repositories;

use App\Models\File;

class FileRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( File::class );
    }
}
