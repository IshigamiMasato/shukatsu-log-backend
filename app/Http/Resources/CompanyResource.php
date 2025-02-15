<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'url' => $this->url,
            'president' => $this->president,
            'address' => $this->address,
            'establish_date' => $this->establish_date,
            'employee_number' => $this->employee_number,
            'listing_class' => $this->listing_class,
            'benefit' => $this->benefit,
            'memo' => $this->memo,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
