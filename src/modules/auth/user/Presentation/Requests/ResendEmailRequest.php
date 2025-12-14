<?php

namespace Src\modules\auth\user\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
