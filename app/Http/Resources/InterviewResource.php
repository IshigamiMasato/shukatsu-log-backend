<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'interview_id' => $this->interview_id,
            'apply_id' => $this->apply_id,
            'interview_date' => $this->interview_date,
            'interviewer_info' => $this->interviewer_info,
            'memo' => $this->memo,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
