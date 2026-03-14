<?php

namespace App\Http\Requests\Cupom;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConsultaCupomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nroempresa' => 'required|integer',
            'nrocheckout' => 'required|integer',
            'coo' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'nroempresa.required' => 'O número da empresa é obrigatório',
            'nroempresa.integer' => 'O número da empresa deve ser um número inteiro',
            'nrocheckout.required' => 'O número do checkout é obrigatório',
            'nrocheckout.integer' => 'O número do checkout deve ser um número inteiro',
            'coo.required' => 'O COO é obrigatório',
            'coo.integer' => 'O COO deve ser um número inteiro',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação dos parâmetros do cupom',
            'data' => $validator->errors(),
            'errors' => $validator->errors()
        ], 422));
    }
}
