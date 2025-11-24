<?php

namespace Src\Auth\User\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\ValidationException;
use Src\auth\user\domain\ValueObjects\UserEmail;
use Src\auth\user\domain\ValueObjects\UserName;
use Src\auth\user\domain\ValueObjects\UserPassword;

class RegisterUserRequests extends FormRequest
{
    public function authorize(): bool
    {
        // Permitir que cualquier usuario haga este request
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                function ($attribute, $value, $fail) {
                    try {
                        new UserName($value);
                    } catch (\InvalidArgumentException $e) {
                        $fail($e->getMessage());
                    }
                }
            ],
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    try {
                        new UserEmail($value);
                    } catch (\InvalidArgumentException $e) {
                        $fail($e->getMessage());
                    }
                },
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) {
                    try {
                        UserPassword::fromPlain($value);
                    } catch (\InvalidArgumentException $e) {
                        $fail($e->getMessage());
                    }
                }
            ],
        ];
    }

    /**
     * Retorna los datos validados como Value Objects
     */
    public function validatedVOs(): array
    {
        $data = $this->validated();

        return [
            'name' => new UserName($data['name']),
            'email' => new UserEmail($data['email']),
            'password' => UserPassword::fromPlain($data['password']),
        ];
    }
}
