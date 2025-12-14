<?php

namespace App\Models;

 use DateTimeImmutable;
 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Relations\BelongsToMany;
 use Illuminate\Database\Eloquent\Relations\HasMany;
 use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 use Tymon\JWTAuth\Contracts\JWTSubject;

 /**
  * @property string $verification_token
  * @property DateTimeImmutable $verification_token_created_at
  */
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
        'is_active',
        'remember_token',
        'verification_token',
        'verification_token_created_at',
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
    public function tasksCreated(): HasMany
    {
        return $this->hasMany(Task::class, 'creator_id');
    }

    public function tasksAssigned():HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to_id');
    }

    // public function teams()
    // {
    //     return $this->belongsToMany(Team::class);
    // }
  public function teams():BelongsToMany
{
    return $this->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id')
                ->withPivot('roles')
                ->withTimestamps();
}

    public function teamsCreated():HasMany
    {
        return $this->hasMany(Team::class, 'user_id');
    }
    public function invitations():HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function userProviders(): HasMany
    {
        return $this->hasMany(UserProvider::class);
    }
}
