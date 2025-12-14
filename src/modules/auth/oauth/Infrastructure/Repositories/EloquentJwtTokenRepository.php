<?php

namespace Src\modules\auth\oauth\Infrastructure\Repositories;

use App\Models\JwtToken as JwtTokenModel;
use Illuminate\Support\Carbon;
use Src\modules\auth\oauth\Domain\Contracts\Jwt\JwtTokenRepositoryInterface;
use Src\modules\auth\oauth\Domain\Entities\JwtToken;

class EloquentJwtTokenRepository implements JwtTokenRepositoryInterface
{
    public function store(JwtToken $token): JwtToken
    {
        JwtTokenModel::create([
            'jti' => $token->jti,
            'user_id' => $token->userId,
            'issued_at' => Carbon::instance($token->issuedAt),
            'expires_at' => Carbon::instance($token->expiresAt),
            'revoked_at' => $token->revokedAt ? Carbon::instance($token->revokedAt) : null,
        ]);
        return $token;
    }

    public function findByJti(string $jti): ?JwtToken
    {
        $row = JwtTokenModel::where('jti', $jti)->first();
        if (!$row)
            return null;
        return new JwtToken($row->jti, $row->user_id, $row->issued_at, $row->expires_at, $row->revoked_at);
    }

    public function revokeByJti(string $jti): bool
    {
        $row = JwtTokenModel::where('jti', $jti)->first();
        if (!$row)
            return false;
        $row->revoked_at = Carbon::now();
        return $row->save();
    }

    public function revokeAllForUser(int $userId): int
    {
        return JwtTokenModel::where('user_id', $userId)->whereNull('revoked_at')->update(['revoked_at' => Carbon::now()]);
    }
}
