<?php

namespace App\Repositories;

use App\Models\Document;

/**
 * @extends Repository<Document>
 */
class DocumentRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Document::class );
    }

    public function findWithFilesBy(array $params): Document|null
    {
        return Document::query()
                        ->with(['files'])
                        ->where($params)
                        ->first();
    }
}
