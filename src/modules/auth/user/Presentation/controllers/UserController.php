<?php

namespace Src\modules\auth\user\Presentation\controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OAT;
use Src\modules\auth\user\Application\Commands\RegisterUser\RegisterUserCommand;
use Src\modules\auth\user\Application\Dtos\RegisterUserRequest;
use Src\modules\auth\user\Application\Handlers\GetAllUsersHandler;
use Src\modules\auth\user\Application\Handlers\RegisterUserHandler;
use Src\modules\auth\user\Application\Queries\GetAllUsersQuery;
use Src\modules\auth\user\Presentation\Requests\RegisterUserRequests;
use Src\shared\enums\HttpStatus;
use Src\Shared\helpers\ErrorHelper;
use Throwable;

#[OAT\Tag(name: "Auth", description: "Authentication operations")]
class UserController extends Controller
{
    #[OAT\Post(
        path: "/v2/auth/register",
        operationId: "registerUser",
        summary: "Register a user",
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
        description: "User successfully registered"
    )]
    #[OAT\Response(
        response: HttpStatus::BAD_REQUEST->value,
        description: "Validation error or invalid data"
    )]
    #[OAT\Response(
        response: HttpStatus::UNPROCESSABLE_ENTITY->value,
        description: "Domain error"
    )]
    #[OAT\Response(
        response: HttpStatus::INTERNAL_SERVER_ERROR->value,
        description: "Unexpected error"
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
                'message' => 'User successfully registered',
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
        summary: "Get all users (protected)",
        tags: ["Auth"]
    )]
    #[OAT\Parameter(
        name: "page",
        description: "Page number",
        in: "query",
        required: false,
        schema: new OAT\Schema(type: "integer", default: 1)
    )]
    #[OAT\Parameter(
        name: "per_page",
        description: "Users per page",
        in: "query",
        required: false,
        schema: new OAT\Schema(type: "integer", default: 10)
    )]
    #[OAT\Response(
        response: HttpStatus::OK->value,
        description: "Paginated users list"
    )]
    #[OAT\Response(
        response: HttpStatus::UNAUTHORIZED->value,
        description: "Unauthenticated user"
    )]
    #[OAT\Response(
        response: HttpStatus::INTERNAL_SERVER_ERROR->value,
        description: "Unexpected error"
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
