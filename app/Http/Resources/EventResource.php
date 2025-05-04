<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'event_id' => $this->resource->event_id,
            'user_id' => $this->resource->user_id,
            'title' => $this->resource->title,
            'type' => $this->resource->type,
            'start_at' => $this->resource->start_at,
            'end_at' => $this->resource->end_at,
            'memo' => $this->resource->memo,
            'created_at' => $this->resource->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->resource->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
