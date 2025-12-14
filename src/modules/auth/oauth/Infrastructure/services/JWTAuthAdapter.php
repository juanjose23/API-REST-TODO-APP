<?php

namespace Src\modules\auth\oauth\Infrastructure\services;
use Src\modules\auth\oauth\Domain\Contracts\Auth\JwtManagerInterface;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthAdapter implements JwtManagerInterface
{
    public function getCurrentTokenJti(): ?string
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return null;
            }

            $payload = JWTAuth::getPayload($token);
            return $payload->get('jti');
        } catch (Throwable) {
            return null;
        }
    }

    public function invalidateCurrentToken(): bool
    {
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                JWTAuth::invalidate($token);
                return true;
            }
            return false;
        } catch (Throwable) {
            return false;
        }
    }
}
