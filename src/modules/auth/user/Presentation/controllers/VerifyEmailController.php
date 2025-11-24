<?php

namespace Src\modules\auth\user\Presentation\controllers;

use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Random\RandomException;
use Src\modules\auth\user\Application\Commands\ResendVerificationTokenCommand;
use Src\modules\auth\user\Application\Commands\VerifyUserEmailCommand;
use Src\modules\auth\user\Application\Handlers\ResendVerificationTokenHandler;
use Src\modules\auth\user\Application\Handlers\VerifyUserEmailHandler;
use Src\shared\helpers\ErrorHelper;

class VerifyEmailController extends Controller
{

    public function __construct(
        private readonly VerifyUserEmailHandler         $verifyHandler,
        private readonly ResendVerificationTokenHandler $resendHandler
    )
    {

    }

    public function verify(string $token): JsonResponse
    {
        try {
            $command = new VerifyUserEmailCommand($token);
            $response = ($this->verifyHandler)($command);

            return response()->json([
                'message' => $response->message
            ]);
        } catch (DomainException $e) {
            Log::warning("Error al verificar correo: ".$e->getMessage(), ['token' => $token]);
            return ErrorHelper::jsonResponse($e);
        }
    }


    public function resend(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        try {
            $command = new ResendVerificationTokenCommand($request->email);
            ($this->resendHandler)($command);

            return response()->json([
                'message' => 'Correo de verificación reenviado.'
            ]);
        } catch (DomainException $e) {
            Log::warning("Error al reenviar token: ".$e->getMessage(), ['email' => $request->email]);
            return ErrorHelper::jsonResponse($e);
        } catch (RandomException $e) {
            Log::error("Error al generar nuevo token: ".$e->getMessage(), ['email' => $request->email]);
            return ErrorHelper::jsonResponse($e);
        }
    }
}
