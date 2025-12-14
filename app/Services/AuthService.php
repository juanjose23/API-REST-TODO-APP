<?php

namespace App\Services;

use App\Interfaces\AuthInterface;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Interfaces\JwtTokenInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Contracts\User as SocialUser;

class AuthService
{
    protected AuthInterface $auth;
    protected JwtTokenInterface $jwtRepo;

    public function __construct(AuthInterface $auth, JwtTokenInterface $jwtRepo)
    {
        $this->auth = $auth;
        $this->jwtRepo = $jwtRepo;
    }

    public function getAllUsers()
    {
        return $this->auth->getAllUsers();
    }

    public function availableUsers($TeamId)
    {
        return $this->auth->availableUsers($TeamId);
    }

    public function register(array $data)
    {
        $user = $this->auth->register($data);

        $jti = (string) Str::uuid();
        $accessToken = JWTAuth::claims(['jti' => $jti])->fromUser($user);
        $payload = JWTAuth::setToken($accessToken)->getPayload();
        $issuedAt = Carbon::createFromTimestamp($payload->get('iat'));
        $expiresAt = Carbon::createFromTimestamp($payload->get('exp'));
        $this->jwtRepo->store($jti, $user->id, $issuedAt, $expiresAt);

        $refreshJti = (string) Str::uuid();
        $refreshToken = JWTAuth::customClaims(['typ' => 'refresh', 'jti' => $refreshJti])->fromUser($user);

        return [
            'user' => $user,
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];


    }

    public function login(array $credentials)
    {
        if (!$token =  JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        $user=$this->auth->login($credentials);
        $jti = (string) Str::uuid();
        $accessToken = JWTAuth::claims(['jti' => $jti])->fromUser($user);
        $payload = JWTAuth::setToken($accessToken)->getPayload();
        $issuedAt = Carbon::createFromTimestamp($payload->get('iat'));
        $expiresAt = Carbon::createFromTimestamp($payload->get('exp'));
        $this->jwtRepo->store($jti, $user->id, $issuedAt, $expiresAt);

        $refreshJti = (string) Str::uuid();
        $refreshToken = JWTAuth::customClaims(['typ' => 'refresh', 'jti' => $refreshJti])->fromUser($user);

        return [
            'user' => $user,
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    public function refresh()
    {
        $new = auth()->refresh();
        $payload = JWTAuth::setToken($new)->getPayload();
        $jti = $payload->get('jti') ?? (string) Str::uuid();
        $userId = auth()->user()->id;
        $issuedAt = Carbon::createFromTimestamp($payload->get('iat'));
        $expiresAt = Carbon::createFromTimestamp($payload->get('exp'));
        $this->jwtRepo->store($jti, $userId, $issuedAt, $expiresAt);
        return $this->respondWithToken($new);
    }

    public function logout(): array
    {
        $payload = JWTAuth::parseToken()->getPayload();
        $jti = $payload->get('jti');
        $this->jwtRepo->revokeByJti($jti);
        auth()->logout();
        return ['message' => 'Logged out'];
    }

    public function logoutAll(): array
    {
        $userId = auth()->id();
        $revoked = $this->jwtRepo->revokeAllForUser($userId);
        auth()->logout();
        return ['message' => 'Logged out from all devices', 'revoked' => $revoked];
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    public function loginWithSocial(SocialUser $socialUser, string $provider)
    {
        $user = $this->auth->findOrCreateSocialUser($socialUser, $provider);
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }



     /**
     * Enviar enlace de restablecimiento de contraseña.
     *
     * @param string $email
     * @return string
     */
    public function sendPasswordResetLink(string $email)
    {
        return $this->auth->sendPasswordResetLink($email);
    }

    /**
     * Restablecer la contraseña.
     *
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data)
    {
        return $this->auth->resetPassword($data);
    }
}
