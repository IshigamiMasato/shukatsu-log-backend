<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinalResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'final_result_id' => $this->resource->final_result_id,
            'apply_id' => $this->resource->apply_id,
            'status' => $this->resource->status,
            'memo' => $this->resource->memo,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
