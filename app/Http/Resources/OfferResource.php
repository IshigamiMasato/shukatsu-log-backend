<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'offer_id' => $this->offer_id,
            'apply_id' => $this->apply_id,
            'offer_date' => $this->offer_date,
            'salary' => $this->salary,
            'condition' => $this->condition,
            'memo' => $this->memo,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
