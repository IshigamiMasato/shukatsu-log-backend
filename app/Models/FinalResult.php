<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinalResult extends Model
{
    use HasFactory;

    protected $primaryKey = 'final_result_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'apply_id',
        'status',
        'memo',
    ];
}
