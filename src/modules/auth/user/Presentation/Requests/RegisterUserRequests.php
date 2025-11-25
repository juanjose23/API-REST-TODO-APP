<?php

namespace Src\modules\auth\user\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use InvalidArgumentException;
use Src\modules\auth\user\Domain\ValuesObjects\UserEmail;
use Src\modules\auth\user\Domain\ValuesObjects\UserName;
use Src\modules\auth\user\Domain\ValuesObjects\UserPassword;

class RegisterUserRequests extends FormRequest
{
    public function authorize(): bool
    {
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
                    } catch (InvalidArgumentException $e) {
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
                    } catch (InvalidArgumentException $e) {
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
                    } catch (InvalidArgumentException $e) {
                        $fail($e->getMessage());
                    }
                }
            ],
        ];
    }
}
