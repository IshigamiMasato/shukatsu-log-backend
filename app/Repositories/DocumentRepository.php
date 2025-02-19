<?php

namespace App\Repositories;

use App\Models\Document;

class DocumentRepository
{
    public function create(array $params): Document
    {
        return Document::create($params);
    }
}
