<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository
{
    public function findBy(array $params): Event|null
    {
        return Event::where($params)->first();
    }

    public function getBy(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return Event::where($params)->get();
    }

    public function create(array $params): Event
    {
        return Event::create($params);
    }

    public function update(Event $event, array $postedParams): bool
    {
        return $event->fill($postedParams)->save();
    }
}
