<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'label',
        'total',
        'created_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function group() : BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function contributors() : HasMany
    {
        return $this->hasMany(Contributor::class, 'payment_id', 'id');
    }

    public function participants() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payment_participant', 'payment_id', 'member_id');
    }
}
