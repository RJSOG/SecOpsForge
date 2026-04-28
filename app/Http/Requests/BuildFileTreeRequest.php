<?php

namespace App\Http\Requests;

use App\Enum\FormatEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuildFileTreeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'root' => 'string|nullable',
            'format' => [
                'required',
                'string',
                Rule::enum(FormatEnum::class)
            ],
        ];
    }
}
