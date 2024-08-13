<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function owner() : BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function members() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_member', 'id', 'member_id');
    }

    public function payments() : HasMany
    {
        return $this->hasMany(Payment::class, 'group_id', 'id');
    }
}
