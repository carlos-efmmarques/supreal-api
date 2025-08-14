<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'abilities' => 'sometimes|array',
            'abilities.*' => 'string',
            'expires_at' => 'nullable|date|after:now',
            'ip_restriction' => 'nullable|ip',
            'rate_limit' => 'nullable|integer|min:1|max:1000',
            'metadata' => 'nullable|array'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do token é obrigatório',
            'name.string' => 'O nome do token deve ser uma string',
            'name.max' => 'O nome do token não pode ter mais de 255 caracteres',
            'abilities.array' => 'As habilidades devem ser um array',
            'abilities.*.string' => 'Cada habilidade deve ser uma string',
            'expires_at.date' => 'A data de expiração deve ser uma data válida',
            'expires_at.after' => 'A data de expiração deve ser no futuro',
            'ip_restriction.ip' => 'O IP de restrição deve ser um endereço IP válido',
            'rate_limit.integer' => 'O limite de taxa deve ser um número inteiro',
            'rate_limit.min' => 'O limite de taxa deve ser pelo menos 1',
            'rate_limit.max' => 'O limite de taxa não pode exceder 1000',
            'metadata.array' => 'Os metadados devem ser um array'
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