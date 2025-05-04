<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'file_id' => $this->resource->file_id,
            'document_id' => $this->resource->document_id,
            'name' => $this->resource->name,
            'path' => $this->resource->path,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
