<?php

namespace Src\modules\auth\user\Presentation\controllers;

use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Random\RandomException;
use Src\modules\auth\user\Application\Commands\ResendVerificationTokenCommand;
use Src\modules\auth\user\Application\Commands\VerifyUserEmailCommand;
use Src\modules\auth\user\Application\Handlers\ResendVerificationTokenHandler;
use Src\modules\auth\user\Application\Handlers\VerifyUserEmailHandler;
use Src\modules\auth\user\Presentation\Requests\ResendEmailRequest;
use Src\shared\helpers\ErrorHelper;
use OpenApi\Attributes as OAT;

#[OAT\Tag(name: "Auth/verify", description: "Authentication verify operations")]
class VerifyEmailController extends Controller
{

    public function __construct(
        private readonly VerifyUserEmailHandler         $verifyHandler,
        private readonly ResendVerificationTokenHandler $resendHandler
    )
    {

    }

    #[OAT\Get(
        path: "/v2/auth/verify/{token}",
        operationId: "verifyEmail",
        summary: "Verify user email",
        tags: ["Auth"],
        parameters: [
            new OAT\Parameter(
                name: "token",
                in: "path",
                required: true,
                schema: new OAT\Schema(type: "string")
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: "Email verified",
                content: new OAT\MediaType(
                    mediaType: "application/json",
                    schema: new OAT\Schema(
                        properties: [
                            new OAT\Property(property: "message", type: "string")
                        ],
                        type: "object"
                    )
                )
            ),
            new OAT\Response(response: 400, description: "Bad Request"),
            new OAT\Response(response: 404, description: "Not Found"),
            new OAT\Response(response: 500, description: "Server error")
        ]
    )]
    public function verify(string $token): JsonResponse
    {
        try {
            $command = new VerifyUserEmailCommand($token);
            $response = ($this->verifyHandler)($command);

            return response()->json([
                'message' => $response->message
            ]);
        } catch (DomainException $e) {
            Log::warning("Email verification error: " . $e->getMessage(), ['token' => $token]);
            return ErrorHelper::jsonResponse($e);
        }
    }


    #[OAT\Get(
        path: "/v2/auth/resend-email",
        operationId: "resendVerification",
        summary: "Resend verification email",
        tags: ["Auth"],
        parameters: [
            new OAT\Parameter(
                name: "email",
                in: "query",
                required: true,
                schema: new OAT\Schema(type: "string", format: "email")
            )
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: "Verification email resent",
                content: new OAT\MediaType(
                    mediaType: "application/json",
                    schema: new OAT\Schema(
                        properties: [
                            new OAT\Property(property: "message", type: "string")
                        ],
                        type: "object"
                    )
                )
            ),
            new OAT\Response(response: 400, description: "Bad Request"),
            new OAT\Response(response: 404, description: "Not Found"),
            new OAT\Response(response: 500, description: "Server error")
        ]
    )]
    public function resend(ResendEmailRequest $request): JsonResponse
    {


        try {
            $command = new ResendVerificationTokenCommand($request->email);
            ($this->resendHandler)($command);

            return response()->json([
                'message' => 'Verification email resent.'
            ]);
        } catch (DomainException $e) {
            Log::warning("Token resend error: " . $e->getMessage(), ['email' => $request->email]);
            return ErrorHelper::jsonResponse($e);
        } catch (RandomException $e) {
            Log::error("New token generation error: " . $e->getMessage(), ['email' => $request->email]);
            return ErrorHelper::jsonResponse($e);
        }
    }
}
