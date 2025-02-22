<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinalResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'final_result_id' => $this->final_result_id,
            'apply_id' => $this->apply_id,
            'status' => $this->status,
            'memo' => $this->memo,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
