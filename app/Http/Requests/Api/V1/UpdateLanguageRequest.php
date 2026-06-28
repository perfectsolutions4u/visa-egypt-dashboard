<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLanguageRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'language' => ['required', 'string', 'in:'.implode(',', config('translatable.locales'))],
        ];
    }
}
