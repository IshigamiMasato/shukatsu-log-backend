<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $primaryKey = 'offer_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'apply_id',
        'offer_date',
        'salary',
        'condition',
        'memo',
    ];
}
