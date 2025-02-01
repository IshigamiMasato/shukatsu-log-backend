<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository
{
    public function getBy(array $params): \Illuminate\Database\Eloquent\Collection
    {
        return Event::where($params)->get();
    }

    public function create(array $params): Event
    {
        return Event::create($params);
    }
}
