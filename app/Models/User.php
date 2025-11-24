<?php

namespace App\Models;

 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\CanResetPassword;
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{

    use HasFactory, Notifiable;
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'id',
        'email_verified_at',
        'provider_id',
        'provider',
        'is_active',
        'remember_token',
        'verification_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    // relaciones
    public function tasksCreated()
    {
        return $this->hasMany(Task::class, 'creator_id');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assigned_to_id');
    }

    // public function teams()
    // {
    //     return $this->belongsToMany(Team::class);
    // }
  public function teams()
{
    return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id')
                ->withPivot('roles')
                ->withTimestamps();
}

    public function teamsCreated()
    {
        return $this->hasMany(Team::class, 'user_id');
    }
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }
}
