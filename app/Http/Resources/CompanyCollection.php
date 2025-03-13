<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCollection extends ResourceCollection
{
    private $total;

    public function __construct($resource, $total)
    {
        parent::__construct($resource);
        $this->total = $total;
    }

    public function toArray(Request $request): array
    {
        return [
            'data'  => $this->collection,
            'total' => $this->total,
        ];
    }
}
