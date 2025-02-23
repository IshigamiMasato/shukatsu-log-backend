<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Event::class );
    }
}
