<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'event_id' => $this->event_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'type' => $this->type,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'memo' => $this->memo,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
