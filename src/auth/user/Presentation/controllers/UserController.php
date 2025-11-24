<?php

namespace Src\auth\user\presentation\controllers;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use OpenApi\Attributes as OAT;

use Src\auth\user\application\commands\RegisterUser\RegisterUserCommand;
use Src\auth\user\application\Dtos\RegisterUserRequest;
use Src\auth\user\application\Handlers\GetAllUsersHandler;
use Src\auth\user\application\Handlers\RegisterUserHandler;
use Src\auth\user\application\Queries\GetAllUsersQuery;
use Src\auth\user\presentation\Requests\RegisterUserRequests;
use Src\Shared\helpers\ErrorHelper;
use Src\shared\enums\HttpStatus;
#[OAT\Tag(name: "Auth", description: "Operaciones de autenticación")]
class UserController extends Controller
{
    #[OAT\Post(
        path: "/v2/auth/register",
        operationId: "registerUser",
        summary: "Registrar un usuario",
        tags: ["Auth"]
    )]
    #[OAT\RequestBody(
        required: true,
        content: new OAT\JsonContent(
            properties: [
                new OAT\Property(property: "name", type: "string"),
                new OAT\Property(property: "email", type: "string", format: "email"),
                new OAT\Property(property: "password", type: "string")
            ],
            type: "object"
        )
    )]
    #[OAT\Response(
        response: HttpStatus::CREATED->value,
        description: "Usuario registrado correctamente"
    )]
    #[OAT\Response(
        response: HttpStatus::BAD_REQUEST->value,
        description: "Error de validación o datos incorrectos"
    )]
    #[OAT\Response(
        response: HttpStatus::UNPROCESSABLE_ENTITY->value,
        description: "Error de dominio"
    )]
    #[OAT\Response(
        response: HttpStatus::INTERNAL_SERVER_ERROR->value,
        description: "Error inesperado"
    )]

    public function register(RegisterUserRequests $request, RegisterUserHandler $handler): JsonResponse
    {
        try {
            $dto = new RegisterUserRequest(
                $request->name,
                $request->email,
                $request->password
            );

            $command = new RegisterUserCommand($dto);

            $response = $handler($command);

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'data' => [
                    'id' => $response->id,
                    'name' => $response->name,
                    'email' => $response->email,
                ]
            ], 201);

        } catch (Throwable $e) {
            Log::error($e);
            return ErrorHelper::jsonResponse($e);
        }
    }
    #[OAT\Get(
        path: "/v2/auth/",
        operationId: "getAllUsers",
        summary: "Obtener todos los usuarios (protegido)",
        tags: ["Auth"]
    )]
    #[OAT\Parameter(
        name: "page",
        description: "Número de página",
        in: "query",
        required: false,
        schema: new OAT\Schema(type: "integer", default: 1)
    )]
    #[OAT\Parameter(
        name: "per_page",
        description: "Cantidad de usuarios por página",
        in: "query",
        required: false,
        schema: new OAT\Schema(type: "integer", default: 10)
    )]
    #[OAT\Response(
        response: HttpStatus::OK->value,
        description: "Listado paginado de usuarios"
    )]
    #[OAT\Response(
        response: HttpStatus::UNAUTHORIZED->value,
        description: "Usuario no autenticado"
    )]
    #[OAT\Response(
        response: HttpStatus::INTERNAL_SERVER_ERROR->value,
        description: "Error inesperado"
    )]

    public function getAllUsers(Request $request, GetAllUsersHandler $handler): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);

        $query = new GetAllUsersQuery(perPage: $perPage, page: $page);

        $paginatedResult = $handler($query);

        return response()->json([
            'data' => $paginatedResult->items,
            'meta' => [
                'total' => $paginatedResult->total,
                'current_page' => $paginatedResult->currentPage(),
                'per_page' => $paginatedResult->perPage(),
                'last_page' => $paginatedResult->lastPage(),
            ],
        ]);
    }


}
