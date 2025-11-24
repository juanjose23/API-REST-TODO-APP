<?php

namespace Src\shared\helpers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Src\Shared\Enums\HttpStatus;
use DomainException;
use InvalidArgumentException;

class ErrorHelper
{

    public static function getHttpCode(Throwable $e): int
    {
        return match (true) {
            $e instanceof InvalidArgumentException => HttpStatus::BAD_REQUEST->value,
            $e instanceof DomainException => HttpStatus::UNPROCESSABLE_ENTITY->value,
            $e instanceof AuthenticationException => HttpStatus::UNAUTHORIZED->value,
            $e instanceof AuthorizationException => HttpStatus::FORBIDDEN->value,
            $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => HttpStatus::NOT_FOUND->value,
            $e instanceof MethodNotAllowedHttpException => HttpStatus::METHOD_NOT_ALLOWED->value,
            default => HttpStatus::INTERNAL_SERVER_ERROR->value,
        };
    }


    public static function getMessage(Throwable $e): string
    {
        return match (true) {
            $e instanceof InvalidArgumentException,
                $e instanceof DomainException,
                $e instanceof AuthenticationException,
                $e instanceof AuthorizationException,
                $e instanceof ModelNotFoundException,
                $e instanceof MethodNotAllowedHttpException,
                $e instanceof NotFoundHttpException
            => $e->getMessage(),
            default => 'Internal server error',
        };
    }


    public static function jsonResponse(Throwable $e): jsonResponse
    {
        return response()->json([
            'error' => self::getMessage($e)
        ], self::getHttpCode($e));
    }
}
