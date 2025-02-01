<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository
{
    public function create(array $params): Event
    {
        return Event::create($params);
    }
}
