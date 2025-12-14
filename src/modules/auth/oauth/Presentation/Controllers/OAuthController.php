<?php

namespace Src\modules\auth\oauth\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Laravel\Socialite\Socialite;
use Src\modules\auth\oauth\Application\Commands\Auth\LoginCommand;
use Src\modules\auth\oauth\Application\Commands\Auth\ProviderLoginCommand;
use Src\modules\auth\oauth\Application\Handlers\Auth\LoginHandler;
use Src\modules\auth\oauth\Application\Handlers\Auth\LogoutAllTokensHandler;
use Src\modules\auth\oauth\Application\Handlers\Auth\LogoutUserHandler;
use Src\modules\auth\oauth\Application\Handlers\Auth\RefreshAccessTokenHandler;
use Src\modules\auth\oauth\Application\Handlers\Oauth\OAuthLoginHandler;
use Src\modules\auth\oauth\Domain\ValuesObjects\ProviderName;
use Src\modules\auth\oauth\Presentation\Requests\LoginRequest;
use Src\shared\helpers\ErrorHelper;
use Throwable;
use OpenApi\Attributes as OAT;

class OAuthController extends Controller
{
    public function __construct()
    {
    }
    #[OAT\Post(
        path: "/v2/auth/oauth/login",
        operationId: "loginUser",
        description: "Authenticates a user using email and password and returns JWT tokens",
        summary: "Login with email and password",
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\MediaType(
                mediaType: "application/json",
                schema: new OAT\Schema(
                    required: ["email", "password"],
                    properties: [
                        new OAT\Property(
                            property: "email",
                            type: "string",
                            format: "email",
                            example: "user@example.com"
                        ),
                        new OAT\Property(
                            property: "password",
                            type: "string",
                            format: "password",
                            example: "secret123"
                        )
                    ],
                    type: "object"
                )
            )
        ),
        tags: ["Auth"],
        parameters: [
            new OAT\Parameter(
                name: "X-Fingerprint",
                description: "Device fingerprint for token binding",
                in: "header",
                required: false,
                schema: new OAT\Schema(type: "string")
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: "Login successful",
                content: new OAT\MediaType(
                    mediaType: "application/json",
                    schema: new OAT\Schema(
                        properties: [
                            new OAT\Property(property: "access_token", type: "string"),
                            new OAT\Property(property: "refresh_token", type: "string"),
                            new OAT\Property(property: "expires_in", type: "integer"),
                            new OAT\Property(property: "token_type", type: "string", example: "Bearer")
                        ],
                        type: "object"
                    )
                )
            ),
            new OAT\Response(response: 401, description: "Invalid credentials"),
            new OAT\Response(response: 422, description: "Validation error"),
            new OAT\Response(response: 500, description: "Server error")
        ]
    )]
    public function login(LoginRequest $request, LoginHandler $handler): JsonResponse
    {

        $command = new LoginCommand(
            email: $request->input('email'),
            password:$request->input('password'),
            fingerprint: $request->header('X-Fingerprint')
        );

        try {
            $result = $handler($command);
            return response()->json($result);
        } catch (Throwable $e) {
            return ErrorHelper::jsonResponse($e);
        }
    }
    #[OAT\Get(
        path: "/v2/auth/oauth/{provider}",
        operationId: "redirectToOAuthProvider",
        description: "Redirects the user to the selected OAuth provider authentication page",
        summary: "Redirect to OAuth provider",
        tags: ["Auth", "OAuth"],
        parameters: [
            new OAT\Parameter(
                name: "provider",
                in: "path",
                required: true,
                schema: new OAT\Schema(type: "string"),
                example: "github"
            )
        ],
        responses: [
            new OAT\Response(
                response: 302,
                description: "Redirect to OAuth provider"
            ),
            new OAT\Response(
                response: 400,
                description: "Invalid provider"
            ),
            new OAT\Response(
                response: 500,
                description: "Server error"
            )
        ]
    )]
    public function redirectToProvider(string $provider): RedirectResponse
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (InvalidArgumentException ) {
            abort(400, 'Invalid provider: ' . $provider);
        } catch (Throwable $e) {
            abort(500, $e->getMessage());
        }
    }

    #[OAT\Get(
        path: "/v2/auth/oauth/{provider}/callback",
        operationId: "oauthProviderCallback",
        description: "Handles OAuth callback, logs in or registers the user and returns JWT tokens",
        summary: "OAuth provider callback",
        tags: ["Auth", "OAuth"],
        parameters: [
            new OAT\Parameter(
                name: "provider",
                in: "path",
                required: true,
                schema: new OAT\Schema(type: "string"),
                example: "github"
            ),
            new OAT\Parameter(
                name: "X-Fingerprint",
                description: "Device fingerprint for token binding",
                in: "header",
                required: false,
                schema: new OAT\Schema(type: "string")
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: "OAuth login successful",
                content: new OAT\MediaType(
                    mediaType: "application/json",
                    schema: new OAT\Schema(
                        properties: [
                            new OAT\Property(property: "access_token", type: "string"),
                            new OAT\Property(property: "refresh_token", type: "string"),
                            new OAT\Property(property: "expires_in", type: "integer"),
                            new OAT\Property(property: "token_type", type: "string", example: "Bearer")
                        ],
                        type: "object"
                    )
                )
            ),
            new OAT\Response(
                response: 400,
                description: "OAuth error"
            ),
            new OAT\Response(
                response: 500,
                description: "Server error"
            )
        ]
    )]
    public function providerCallback(string $provider, Request $request, OAuthLoginHandler $handler): JsonResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $command = new ProviderLoginCommand(
                socialUser: $socialUser,
                providerName: new ProviderName($provider),
                fingerprint: $request->header('X-Fingerprint')
            );

            $result = $handler($command);

            return response()->json($result);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    #[OAT\Post(
        path: "/v2/auth/refresh",
        operationId: "refreshAccessToken",
        summary: "Refresh access token using refresh token",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Auth"],
        responses: [
            new OAT\Response(
                response: 200,
                description: "Token refreshed",
                content: new OAT\MediaType(
                    mediaType: "application/json",
                    schema: new OAT\Schema(
                        properties: [
                            new OAT\Property(property: "access_token", type: "string"),
                            new OAT\Property(property: "refresh_token", type: "string"),
                            new OAT\Property(property: "expires_in", type: "integer", example: 900),
                        ],
                        type: "object"
                    )
                )
            ),
            new OAT\Response(response: 401, description: "Invalid refresh token"),
            new OAT\Response(response: 500, description: "Server error")
        ]
    )]
    public function refresh(Request $request, RefreshAccessTokenHandler $handler):jsonResponse
    {
        $refreshToken = $request->bearerToken();
        if (!$refreshToken) {
            return response()->json(['error' => 'Refresh token missing'], 401);
        }
        try {
            $dto = $handler($refreshToken);
            return response()->json([
                'access_token' => $dto->accessToken,
                'refresh_token' => $dto->refreshToken,
                'expires_in' => $dto->expiresIn
            ]);
        } catch (Throwable $e) {
            Log::error($e);
            return ErrorHelper::jsonResponse($e);
        }
    }

    #[OAT\Post(
        path: "/v2/auth/logout",
        operationId: "logoutUser",
        description: "Revokes the current access token and logs out the user",
        summary: "Logout current user session",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Auth"],
        responses: [
            new OAT\Response(
                response: 200,
                description: "User logged out successfully",
                content: new OAT\MediaType(
                    mediaType: "application/json",
                    schema: new OAT\Schema(
                        properties: [
                            new OAT\Property(
                                property: "message",
                                type: "string",
                                example: "Logged out"
                            )
                        ],
                        type: "object"
                    )
                )
            ),
            new OAT\Response(
                response: 401,
                description: "User not authenticated"
            ),
            new OAT\Response(
                response: 500,
                description: "Server error"
            )
        ]
    )]
    public function logout(LogoutUserHandler $handler):JsonResponse
    {
        try {
            $handler();
            return response()->json(['message' => 'Logged out']);
        } catch (Throwable $e) {
            return ErrorHelper::jsonResponse($e);
        }
    }

    #[OAT\Post(
        path: "/v2/auth/logout-all",
        operationId: "logoutAllUserSessions",
        description: "Revokes all active tokens for the authenticated user",
        summary: "Logout all user sessions",
        security: [
            ["bearerAuth" => []]
        ],
        tags: ["Auth"],
        responses: [
            new OAT\Response(
                response: 200,
                description: "All sessions logged out",
                content: new OAT\MediaType(
                    mediaType: "application/json",
                    schema: new OAT\Schema(
                        properties: [
                            new OAT\Property(
                                property: "message",
                                type: "string",
                                example: "Logged out all"
                            ),
                            new OAT\Property(
                                property: "revoked",
                                description: "Number of revoked tokens",
                                type: "integer",
                                example: 3
                            )
                        ],
                        type: "object"
                    )
                )
            ),
            new OAT\Response(
                response: 401,
                description: "User not authenticated"
            ),
            new OAT\Response(
                response: 500,
                description: "Server error"
            )
        ]
    )]
    public function logoutAll(LogoutAllTokensHandler $handler):JsonResponse
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        try {
            $revoked = $handler($userId);
            Auth::logout();
            return response()->json(['message' => 'Logged out all', 'revoked' => $revoked]);
        } catch (Throwable $e) {
            return ErrorHelper::jsonResponse($e);
        }
    }
}
