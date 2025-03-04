<?php

namespace App\Repositories;

use App\Models\Apply;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ApplyRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Apply::class );
    }

    public function findWithProcessBy(array $params): Apply|null
    {
        return Apply::query()
                    ->with([
                        'documents.files',
                        'exams',
                        'interviews',
                        'offers',
                        'finalResults',
                    ])
                    ->where($params)
                    ->first();
    }

    public function search(int $userId, array $params): Collection
    {
        return Apply::query()
            ->where('user_id', $userId)
            ->when( isset($params['status']), function (Builder $query) use($params) {
                $query->where('status', $params['status']);
            })
            ->get();
    }
}
