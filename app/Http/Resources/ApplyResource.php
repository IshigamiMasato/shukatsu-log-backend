<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'apply_id' => $this->apply_id,
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'status' => $this->status,
            'occupation' => $this->occupation,
            'apply_route' => $this->apply_route,
            'memo' => $this->memo,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'company' => $this->company,
        ];
    }
}
