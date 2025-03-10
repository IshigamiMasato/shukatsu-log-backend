<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Company::class );
    }

    public function search(int $userId, array $params): Collection
    {
        return Company::query()
            ->where('user_id', $userId)
            ->when( isset($params['name']), function (Builder $query) use($params) {
                $query->where( 'name', 'LIKE', '%'.addcslashes($params['name'], '%_\\').'%' );
            })
            ->when( isset($params['url']), function (Builder $query) use($params) {
                $query->where( 'url', 'LIKE', '%'.addcslashes($params['url'], '%_\\').'%' );
            })
            ->when( isset($params['president']), function (Builder $query) use($params) {
                $query->where( 'president', 'LIKE', '%'.addcslashes($params['president'], '%_\\').'%' );
            })
            ->when( isset($params['address']), function (Builder $query) use($params) {
                $query->where( 'address', 'LIKE', '%'.addcslashes($params['address'], '%_\\').'%' );
            })
            ->when( isset($params['from_establish_date']), function (Builder $query) use($params) {
                $query->where( 'establish_date', '>=', $params['from_establish_date'] );
            })
            ->when( isset($params['to_establish_date']), function (Builder $query) use($params) {
                $query->where( 'establish_date', '<=', $params['to_establish_date'] );
            })
            ->when( isset($params['from_employee_number']), function (Builder $query) use($params) {
                $query->where( 'employee_number', '>=', $params['from_employee_number'] );
            })
            ->when( isset($params['to_employee_number']), function (Builder $query) use($params) {
                $query->where( 'employee_number', '<=', $params['to_employee_number'] );
            })
            ->when( isset($params['benefit']), function (Builder $query) use($params) {
                $query->where( 'benefit', 'LIKE', '%'.addcslashes($params['benefit'], '%_\\').'%' );
            })
            ->when( isset($params['memo']), function (Builder $query) use($params) {
                $query->where( 'memo', 'LIKE', '%'.addcslashes($params['memo'], '%_\\').'%' );
            })
            ->orderBy('updated_at', 'DESC')
            ->get();
    }
}
