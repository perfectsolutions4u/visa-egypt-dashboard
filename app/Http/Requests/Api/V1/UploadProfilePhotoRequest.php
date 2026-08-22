<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class UploadProfilePhotoRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'file', 'max:5120'],
        ];
    }
}
