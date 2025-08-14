<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExampleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'status' => 'sometimes|string|in:active,inactive'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O campo nome deve ser uma string',
            'name.max' => 'O campo nome não pode ter mais de 255 caracteres',
            'description.string' => 'O campo descrição deve ser uma string',
            'description.max' => 'O campo descrição não pode ter mais de 1000 caracteres',
            'status.string' => 'O campo status deve ser uma string',
            'status.in' => 'O campo status deve ser active ou inactive'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'data' => $validator->errors()
        ], 422));
    }
}