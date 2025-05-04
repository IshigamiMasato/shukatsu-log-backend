<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'apply_id' => $this->resource->apply_id,
            'user_id' => $this->resource->user_id,
            'company_id' => $this->resource->company_id,
            'status' => $this->resource->status,
            'occupation' => $this->resource->occupation,
            'apply_route' => $this->resource->apply_route,
            'memo' => $this->resource->memo,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
            'company' => $this->resource->company,
        ];
    }
}
