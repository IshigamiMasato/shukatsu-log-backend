<?php

namespace App\Repositories;

use App\Models\Document;

class DocumentRepository
{
    public function findBy(array $params): Document|null
    {
        return Document::where($params)->first();
    }

    public function findWithFilesBy(array $params): Document|null
    {
        return Document::query()
                        ->with(['files'])
                        ->where($params)
                        ->first();
    }

    public function create(array $params): Document
    {
        return Document::create($params);
    }

    public function delete(Document $document): bool
    {
        return $document->delete();
    }
}
