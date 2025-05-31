<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Notifications\ResetPassword;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{
    //
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(env('FRONT_URL') . '/auth/reset-password/' . $token . '?email=' . urlencode($user->email));
        });

    }
    public function users()
    {
        // Logic to get all users
        $users = $this->authService->getAllUsers();
        return response()->json($users, 200);
    }

    
    public function availableUsers($TeamId)
    {
        // Logic to get all users
        $teamIdInt = (int) $TeamId;
        $users = $this->authService->availableUsers($teamIdInt);
        \Log::info('Available Users: ', ['users' => $users]);
        return response()->json($users, 200);
    }
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $result = $this->authService->register($validatedData);

            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($credentials);

        if (!$result) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json($result, 201);
    }


    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out'], 200);
    }


    public function sendPasswordResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => __($status)
                ], 200);
            }

            return response()->json([
                'message' => __($status)
            ], 422);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $refreshToken = $request->bearerToken();

            // Validar y autenticar con el token
            $payload = JWTAuth::setToken($refreshToken)->getPayload();

            // Validar que sea un token de tipo "refresh"
            if ($payload->get('typ') !== 'refresh') {
                return response()->json(['error' => 'Invalid token type'], 401);
            }

            // Obtener el usuario desde el token
            $user = JWTAuth::setToken($refreshToken)->toUser();

            // Generar nuevo access token
            $newAccessToken = JWTAuth::fromUser($user);

            return response()->json([
                'access_token' => $newAccessToken,
            ]);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalid or expired'], 401);
        }
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Aquí pasas la solicitud al servicio para restablecer la contraseña
        $status = $this->authService->resetPassword($request->only('email', 'password', 'token'));

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? response()->json(['status' => __($status)])
            : response()->json(['error' => __($status)], 400);
    }

}
