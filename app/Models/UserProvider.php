<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProvider extends Model
{
    //
    protected $table = 'user_providers';
    protected $fillable = [
        'user_id',
        'provider_name',
        'provider_id',
        'provider_email',
        'avatar_url',
        'nickname',
        'raw_profile'
    ];

    protected $casts = [
        'raw_profile' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
