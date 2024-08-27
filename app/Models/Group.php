<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'owner_id',
        'name',
        'total_expenses',
        'is_resolved'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function owner() : BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function members() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_member', 'group_id', 'member_id');
    }

    public function payments() : HasMany
    {
        return $this->hasMany(Payment::class, 'group_id', 'id');
    }

    public function debts() : HasMany
    {
        return $this->hasMany(Debt::class, 'group_id', 'id');
    }
}
