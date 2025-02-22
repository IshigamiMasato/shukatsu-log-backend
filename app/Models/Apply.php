<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apply extends Model
{
    use HasFactory;

    protected $primaryKey = 'apply_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'status',
        'occupation',
        'apply_route',
        'memo'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'apply_id', 'apply_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'apply_id', 'apply_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'apply_id', 'apply_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'apply_id', 'apply_id');
    }

    public function finalResults(): HasMany
    {
        return $this->hasMany(FinalResult::class, 'apply_id', 'apply_id');
    }
}
