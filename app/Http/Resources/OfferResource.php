<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'offer_id' => $this->resource->offer_id,
            'apply_id' => $this->resource->apply_id,
            'offer_date' => $this->resource->offer_date,
            'salary' => $this->resource->salary,
            'condition' => $this->resource->condition,
            'memo' => $this->resource->memo,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
