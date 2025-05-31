<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    //
    protected $table = 'teams';
   protected $appends = ['totalUsersCount'];
   protected $hidden = ['owner_count', 'members_count'];
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'is_active',
    ];


    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')
            ->withPivot('roles')
            ->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }


    /**
     * Summary of getTotalUsersCountAttribute
     */
    public function getTotalUsersCountAttribute()
    {
        return ($this->owner_count ?? 0) + ($this->members_count ?? 0);
    }

}
