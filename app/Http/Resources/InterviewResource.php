<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'interview_id' => $this->resource->interview_id,
            'apply_id' => $this->resource->apply_id,
            'interview_date' => $this->resource->interview_date,
            'interviewer_info' => $this->resource->interviewer_info,
            'memo' => $this->resource->memo,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
