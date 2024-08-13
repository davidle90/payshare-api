<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'member_id',
        'amount'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function payment() : BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function member() : BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id', 'id');
    }
}
