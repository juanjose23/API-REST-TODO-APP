<?php

namespace App\Http\Middleware;

use Closure;
use Src\modules\auth\oauth\Domain\Contracts\Jwt\JwtTokenRepositoryInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnsureJwtNotRevoked
{
    protected JwtTokenRepositoryInterface $jwtRepo;

    public function __construct(JwtTokenRepositoryInterface $jwtRepo)
    {
        $this->jwtRepo = $jwtRepo;
    }

    public function handle($request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $jti = $payload->get('jti');
            // Usamos repositorio DDD: si no encuentra o está revocado/expirado
            $token = $this->jwtRepo->findByJti($jti);
            if (!$jti || !$token || $token->isRevokedOrExpired(new \DateTimeImmutable())) {
                return response()->json(['error' => 'Token revoked or invalid'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token revoked or invalid'], 401);
        }

        return $next($request);
    }
}
