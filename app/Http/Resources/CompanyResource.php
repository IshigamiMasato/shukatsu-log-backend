<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'company_id' => $this->resource->company_id,
            'user_id' => $this->resource->user_id,
            'name' => $this->resource->name,
            'url' => $this->resource->url,
            'president' => $this->resource->president,
            'address' => $this->resource->address,
            'establish_date' => $this->resource->establish_date,
            'employee_number' => $this->resource->employee_number,
            'listing_class' => $this->resource->listing_class,
            'business_description' => $this->resource->business_description,
            'benefit' => $this->resource->benefit,
            'memo' => $this->resource->memo,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
