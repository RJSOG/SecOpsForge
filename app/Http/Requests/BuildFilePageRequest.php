<?php

namespace App\Http\Requests;

use App\Enum\FormatEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BuildFilePageRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $path = request('path');
        $format = Str::afterLast($path, '.');
        $source = request('source', '');

        $this->merge([
            'path' => $path,
            'format' => $format,
            'source' => $source,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'path' => 'required|string',
            'format' => [
                'required',
                'string',
                Rule::enum(FormatEnum::class)
            ],
            'source' => 'required|string',
        ];
    }
}
