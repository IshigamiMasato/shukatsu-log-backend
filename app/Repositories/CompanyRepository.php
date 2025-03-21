<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;

class CompanyRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Company::class );
    }

    public function search(int $userId, array $params): array
    {
        $query = Company::query()
            ->where('user_id', $userId)
            ->when( isset($params['keyword']), function (Builder $query) use($params) {
                $query->where(function (Builder $query) use ($params) {
                    $query->where( 'name',                 'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' )
                        ->orWhere( 'url',                  'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' )
                        ->orWhere( 'president',            'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' )
                        ->orWhere( 'address',              'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' )
                        ->orWhere( 'listing_class',        'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' )
                        ->orWhere( 'business_description', 'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' )
                        ->orWhere( 'benefit',              'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' )
                        ->orWhere( 'memo',                 'LIKE', '%'.addcslashes($params['keyword'], '%_\\').'%' );
                });
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
            });

            $totalCount = $query->count();

            // offset、limitがなければ全件取得
            $companies = $query->orderBy('updated_at', 'DESC')
                                ->when( isset($params['offset']), function (Builder $query) use($params) {
                                    $query->offset( $params['offset'] );
                                })
                                ->when( isset($params['limit']), function (Builder $query) use($params) {
                                    $query->limit( $params['limit'] );
                                })
                                ->get();

            return [
                'total' => $totalCount,
                'companies' => $companies,
            ];
    }
}
