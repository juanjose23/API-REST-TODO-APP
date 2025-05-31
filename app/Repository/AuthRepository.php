<?php
namespace App\Repository;

use App\Interfaces\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialUser;
use Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
class AuthRepository implements AuthInterface
{
    public function getAllUsers()
    {
        return User::select('id', 'name', 'email', 'avatar')->where('is_active', true)->get();
    }
public function availableUsers($teamId)
{
    $teamId = (int) $teamId; // asegurar que es int

    $users = User::select('id', 'name', 'email', 'avatar')
        ->where('is_active', true)
        ->whereDoesntHave('teams', function ($query) use ($teamId) {
            $query->where('teams.id', $teamId);
        })
        ->whereDoesntHave('teamsCreated', function ($query) use ($teamId) {
            $query->where('teams.id', $teamId);
        })
        ->get();
        \Log::info('Available users for team ID ' . $teamId, ['users' => $users]);

    return $users;
}



    public function register(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);
    }

    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        return Auth::user();
    }

    public function findOrCreateSocialUser(SocialUser $socialUser, string $provider)
    {
        $user = User::where('provider_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'email_verified_at' => now(),
            ]);
        }

        return $user;
    }

    /**
     * Actualizar la contraseña del usuario.
     *
     * @param \App\Models\User $user
     * @param string $password
     * @return bool
     */
    public function updateUserPassword($user, string $password)
    {
        $user->forceFill([
            'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        return $user->save();
    }

    /**
     * Enviar enlace de restablecimiento de contraseña.
     *
     * @param string $email
     * @return string
     */
    public function sendPasswordResetLink(string $email)
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent to your email.'])
            : response()->json(['error' => 'Unable to send reset link.'], 400);
    }

    /**
     * Restablecer la contraseña del usuario.
     *
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data)
    {
        // Asegúrate de que el array tiene los datos necesarios
        $validatedData = [
            'email' => $data['email'],
            'password' => $data['password'],
            'token' => $data['token'],
        ];

        // Llamada al método reset con los datos validados
        $status = Password::reset(
            $validatedData,
            function ($user, $password) {
                $this->updateUserPassword($user, $password);
                event(new PasswordReset($user));
            }
        );

        return $status;
    }

}
