<?php

namespace App\Repositories;

use App\Models\File;

class FileRepository
{
    public function create(array $params): File
    {
        return File::create($params);
    }
}
