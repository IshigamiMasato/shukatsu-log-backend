<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'file_id' => $this->file_id,
            'document_id' => $this->document_id,
            'name' => $this->name,
            'path' => $this->path,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
