<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EventRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Event::class );
    }

    public function search(int $userId, array $params): Collection
    {
        return Event::query()
            ->where('user_id', $userId)
            ->when( isset($params['start_at']), function (Builder $query) use ($params) {
                $query->where('start_at', '>=', $params['start_at']);
            })
            ->when( isset($params['end_at']), function (Builder $query) use ($params) {
                $query->where('end_at', '<=', $params['end_at']);
            })
            ->get();
    }
}
