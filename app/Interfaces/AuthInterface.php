<?php

namespace App\Interfaces;
use Laravel\Socialite\Contracts\User as SocialUser;
interface AuthInterface
{
    public function getAllUsers();
    public function availableUsers(int $TeamId);
    public function register(array $data);
    public function login(array $credentials);
    public function findOrCreateSocialUser(SocialUser $socialUser, string $provider);
    public function updateUserPassword($user, string $password);
    public function sendPasswordResetLink(string $email);
    public function resetPassword(array $data);
}
