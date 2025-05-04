<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'type',
        'start_at',
        'end_at',
        'memo'
    ];
}
