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
            // Defense in depth: reject obvious traversal/absolute-path attempts
            // here, in addition to the realpath() canonicalization done in
            // FileTreeBuilder::load().
            'root' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (str_starts_with($value, '/') || str_contains($value, '..') || str_contains($value, "\0")) {
                        $fail('The root path is invalid.');
                    }
                },
            ],
            'format' => [
                'required',
                'string',
                Rule::enum(FormatEnum::class)
            ],
        ];
    }
}
