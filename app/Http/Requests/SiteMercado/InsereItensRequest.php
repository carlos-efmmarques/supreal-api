<?php

namespace App\Http\Requests\SiteMercado;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InsereItensRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nropedidoafv' => 'required|string|max:20',
            'seqpedvendaitem' => 'required|integer|min:1',
            'codacesso' => 'required|string|max:50',
            'seqproduto' => 'required|integer|min:1',
            'qtdpedida' => 'required|numeric|min:0.01',
            'qtdembalagem' => 'required|numeric|min:0.01',
            'vlrembtabpreco' => 'required|numeric|min:0',
            'vlrembinformado' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nropedidoafv.required' => 'O número do pedido AFV é obrigatório',
            'nropedidoafv.string' => 'O número do pedido AFV deve ser uma string',
            'nropedidoafv.max' => 'O número do pedido AFV não pode ter mais de 20 caracteres',
            
            'seqpedvendaitem.required' => 'A sequência do item no pedido é obrigatória',
            'seqpedvendaitem.integer' => 'A sequência do item deve ser um número inteiro',
            'seqpedvendaitem.min' => 'A sequência do item deve ser pelo menos 1',
            
            'codacesso.required' => 'O código de acesso do produto é obrigatório',
            'codacesso.string' => 'O código de acesso deve ser uma string',
            'codacesso.max' => 'O código de acesso não pode ter mais de 50 caracteres',
            
            'seqproduto.required' => 'A sequência do produto é obrigatória',
            'seqproduto.integer' => 'A sequência do produto deve ser um número inteiro',
            'seqproduto.min' => 'A sequência do produto deve ser pelo menos 1',
            
            'qtdpedida.required' => 'A quantidade pedida é obrigatória',
            'qtdpedida.numeric' => 'A quantidade pedida deve ser um número',
            'qtdpedida.min' => 'A quantidade pedida deve ser maior que 0',
            
            'qtdembalagem.required' => 'A quantidade da embalagem é obrigatória',
            'qtdembalagem.numeric' => 'A quantidade da embalagem deve ser um número',
            'qtdembalagem.min' => 'A quantidade da embalagem deve ser maior que 0',
            
            'vlrembtabpreco.required' => 'O valor da embalagem (tabela preço) é obrigatório',
            'vlrembtabpreco.numeric' => 'O valor da embalagem (tabela preço) deve ser um número',
            'vlrembtabpreco.min' => 'O valor da embalagem (tabela preço) não pode ser negativo',
            
            'vlrembinformado.required' => 'O valor da embalagem informado é obrigatório',
            'vlrembinformado.numeric' => 'O valor da embalagem informado deve ser um número',
            'vlrembinformado.min' => 'O valor da embalagem informado não pode ser negativo',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação dos dados do item',
            'data' => $validator->errors(),
            'errors' => $validator->errors() // Formato esperado pelos testes Laravel
        ], 422));
    }
}