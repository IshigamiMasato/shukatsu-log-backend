<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'document_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'apply_id',
        'submission_date',
        'memo'
    ];

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'document_id', 'document_id');
    }
}
