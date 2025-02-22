<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $primaryKey = 'company_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'name',
        'url',
        'president',
        'address',
        'establish_date',
        'employee_number',
        'listing_class',
        'benefit',
        'memo',
    ];
}
