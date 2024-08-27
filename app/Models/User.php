<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reference_id',
        'name',
        'email',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    public function groups_created() : HasMany
    {
        return $this->hasMany(Group::class, 'owner_id', 'id',);
    }

    public function groups() : BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_member', 'member_id', 'group_id');
    }

    public function contributions() : HasMany
    {
        return $this->hasMany(Contributor::class, 'member_id', 'id');
    }

    public function participations() : HasMany
    {
        return $this->hasMany(Participant::class, 'member_id', 'id');
    }

    public function friends() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_friend', 'user_id', 'friend_id');
    }

    public function friends_of_mine() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_friend', 'friend_id', 'user_id');
    }

    public function sentFriendRequests() : HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedFriendRequests() : HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function sendFriendRequest(User $receiver)
    {
        return FriendRequest::create([
            'sender_id' => $this->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);
    }

    public function acceptFriendRequest(FriendRequest $request)
    {
        $request->status = 'accepted';
        $request->save();

        $this->friends()->syncWithoutDetaching([$request->sender_id]);
        $request->sender->friends()->syncWithoutDetaching([$this->id]);
    }

    public function declineFriendRequest(FriendRequest $request)
    {
        $request->update(['status' => 'declined']);
    }

    public function removeFriend(User $friend)
    {
        $this->friends()->detach($friend);
        $friend->friends()->detach($this->id);
    }

    public function debtsOwedToMe(): HasMany
    {
        return $this->hasMany(Debt::class, 'to_user_id');
    }

    public function debtsIOwe(): HasMany
    {
        return $this->hasMany(Debt::class, 'from_user_id');
    }
}
