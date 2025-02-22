<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $primaryKey = 'exam_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'apply_id',
        'exam_date',
        'content',
        'memo',
    ];
}
