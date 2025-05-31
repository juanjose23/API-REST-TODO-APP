<?php

namespace App\Services;

use App\Interfaces\AuthInterface;

use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Contracts\User as SocialUser;

class AuthService
{
    protected AuthInterface $auth;

    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
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
       
        $accessToken = JWTAuth::fromUser($user); // corto
        $refreshToken = JWTAuth::customClaims(['typ' => 'refresh'])->fromUser($user); // simulado
    
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

        $accessToken = JWTAuth::fromUser($user); // corto
        $refreshToken = JWTAuth::customClaims(['typ' => 'refresh'])->fromUser($user); // simulado
    
        return [
            'user' => $user,
            'token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
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
