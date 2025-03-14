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

    public function search(int $userId, array $params): array
    {
        $query = Apply::query()
            ->where('user_id', $userId)
            ->when( isset($params['company_id']), function (Builder $query) use($params) {
                $query->where('company_id', $params['company_id']);
            })
            ->when( isset($params['status']), function (Builder $query) use($params) {
                $query->whereIn('status', $params['status']);
            })
            ->when( isset($params['occupation']), function (Builder $query) use($params) {
                $query->where( 'occupation', 'LIKE', '%'.addcslashes($params['occupation'], '%_\\').'%' );
            })
            ->when( isset($params['apply_route']), function (Builder $query) use($params) {
                $query->where( 'apply_route', 'LIKE', '%'.addcslashes($params['apply_route'], '%_\\').'%' );
            })
            ->when( isset($params['memo']), function (Builder $query) use($params) {
                $query->where( 'memo', 'LIKE', '%'.addcslashes($params['memo'], '%_\\').'%' );
            });

        $totalCount = $query->count();

        // offset、limitがなければ全件取得
        $applies = $query->orderBy('updated_at', 'DESC')
                        ->when( isset($params['offset']), function (Builder $query) use($params) {
                            $query->offset( $params['offset'] );
                        })
                        ->when( isset($params['limit']), function (Builder $query) use($params) {
                            $query->limit( $params['limit'] );
                        })
                        ->get();

        return [
            'total' => $totalCount,
            'applies' => $applies,
        ];
    }

    public function getStatusSummary(int $userId): Apply
    {
        return Apply::query()
            ->selectRaw("
                COALESCE( SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0 ) as unregistered_selection_process_summary,
                COALESCE( SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0 ) as document_selection_summary,
                COALESCE( SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0 ) as exam_selection_summary,
                COALESCE( SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0 ) as interview_selection_summary,
                COALESCE( SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0 ) as offer_summary,
                COALESCE( SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0 ) as final_summary
            ", [
                config('const.applies.status.unregistered_selection_process'),
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
