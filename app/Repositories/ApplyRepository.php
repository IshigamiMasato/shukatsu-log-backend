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
                $query->whereIn('status', $params['status']);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();
    }

    public function getStatusSummary(int $userId): Apply
    {
        return Apply::query()
            ->selectRaw("
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as document_selection_summary,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as exam_selection_summary,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as interview_selection_summary,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as offer_summary,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as final_summary
            ", [
                config('const.applies.status.document_selection'),
                config('const.applies.status.exam_selection'),
                config('const.applies.status.interview_selection'),
                config('const.applies.status.offer'),
                config('const.applies.status.final'),
            ])
            ->where('user_id', $userId)
            ->first();
    }
}
