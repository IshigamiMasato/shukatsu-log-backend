<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interview extends Model
{
    use HasFactory;

    protected $primaryKey = 'interview_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'apply_id',
        'interview_date',
        'interviewer_info',
        'memo',
    ];
}
