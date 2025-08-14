<?php

namespace App\Http\Requests\SiteMercado;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InserePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Dados básicos do pedido
            'nropedidoafv' => 'required|string|max:20',
            'nroempresa' => 'required|integer',
            
            // Dados do cliente
            'nrocgccpf' => 'required|string|max:14',
            'digcgccpf' => 'required|string|max:2',
            'nomerazao' => 'required|string|max:200',
            'fantasia' => 'nullable|string|max:200',
            'fisicajuridica' => 'required|string|in:F,J',
            'sexo' => 'nullable|string|in:M,F',
            
            // Endereço
            'cidade' => 'required|string|max:100',
            'uf' => 'required|string|size:2',
            'bairro' => 'required|string|max:100',
            'logradouro' => 'required|string|max:200',
            'nrologradouro' => 'required|string|max:20',
            'cmpltologradouro' => 'nullable|string|max:100',
            'cep' => 'required|string|max:10',
            
            // Contatos
            'foneddd1' => 'nullable|string|max:3',
            'fonenro1' => 'nullable|string|max:15',
            'foneddd2' => 'nullable|string|max:3',
            'fonenro2' => 'nullable|string|max:15',
            'inscricaorg' => 'nullable|string|max:20',
            'dtanascfund' => 'nullable|date',
            'email' => 'required|email|max:200',
            'emailnfe' => 'nullable|email|max:200',
            
            // Dados do pedido
            'indentregaretira' => 'required|string|in:E,R',
            'dtapedidoafv' => 'required|date',
            'vlrtotfrete' => 'nullable|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'nroformapagto' => 'required|integer',
            'usuinclusao' => 'required|string|max:50',
            'nroparcelas' => 'required|integer|min:1',
            'codoperadoracartao' => 'nullable|integer',
            'nrocartao' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            // Dados básicos
            'nropedidoafv.required' => 'O número do pedido AFV é obrigatório',
            'nropedidoafv.max' => 'O número do pedido AFV não pode ter mais de 20 caracteres',
            'nroempresa.required' => 'O número da empresa é obrigatório',
            'nroempresa.integer' => 'O número da empresa deve ser um número inteiro',
            
            // Dados do cliente
            'nrocgccpf.required' => 'O CPF/CNPJ é obrigatório',
            'nrocgccpf.max' => 'O CPF/CNPJ não pode ter mais de 14 caracteres',
            'digcgccpf.required' => 'O dígito verificador do CPF/CNPJ é obrigatório',
            'digcgccpf.max' => 'O dígito verificador não pode ter mais de 2 caracteres',
            'nomerazao.required' => 'O nome/razão social é obrigatório',
            'nomerazao.max' => 'O nome/razão social não pode ter mais de 200 caracteres',
            'fantasia.max' => 'O nome fantasia não pode ter mais de 200 caracteres',
            'fisicajuridica.required' => 'O tipo de pessoa (F/J) é obrigatório',
            'fisicajuridica.in' => 'O tipo de pessoa deve ser F (Física) ou J (Jurídica)',
            'sexo.in' => 'O sexo deve ser M (Masculino) ou F (Feminino)',
            
            // Endereço
            'cidade.required' => 'A cidade é obrigatória',
            'cidade.max' => 'A cidade não pode ter mais de 100 caracteres',
            'uf.required' => 'A UF é obrigatória',
            'uf.size' => 'A UF deve ter exatamente 2 caracteres',
            'bairro.required' => 'O bairro é obrigatório',
            'bairro.max' => 'O bairro não pode ter mais de 100 caracteres',
            'logradouro.required' => 'O logradouro é obrigatório',
            'logradouro.max' => 'O logradouro não pode ter mais de 200 caracteres',
            'nrologradouro.required' => 'O número do logradouro é obrigatório',
            'nrologradouro.max' => 'O número do logradouro não pode ter mais de 20 caracteres',
            'cmpltologradouro.max' => 'O complemento não pode ter mais de 100 caracteres',
            'cep.required' => 'O CEP é obrigatório',
            'cep.max' => 'O CEP não pode ter mais de 10 caracteres',
            
            // Contatos
            'foneddd1.max' => 'O DDD do telefone 1 não pode ter mais de 3 caracteres',
            'fonenro1.max' => 'O telefone 1 não pode ter mais de 15 caracteres',
            'foneddd2.max' => 'O DDD do telefone 2 não pode ter mais de 3 caracteres',
            'fonenro2.max' => 'O telefone 2 não pode ter mais de 15 caracteres',
            'inscricaorg.max' => 'A inscrição RG não pode ter mais de 20 caracteres',
            'dtanascfund.date' => 'A data de nascimento/fundação deve ser uma data válida',
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ter um formato válido',
            'email.max' => 'O email não pode ter mais de 200 caracteres',
            'emailnfe.email' => 'O email para NFe deve ter um formato válido',
            'emailnfe.max' => 'O email para NFe não pode ter mais de 200 caracteres',
            
            // Dados do pedido
            'indentregaretira.required' => 'O indicador de entrega/retirada é obrigatório',
            'indentregaretira.in' => 'O indicador deve ser E (Entrega) ou R (Retirada)',
            'dtapedidoafv.required' => 'A data do pedido é obrigatória',
            'dtapedidoafv.date' => 'A data do pedido deve ser uma data válida',
            'vlrtotfrete.numeric' => 'O valor do frete deve ser um número',
            'vlrtotfrete.min' => 'O valor do frete não pode ser negativo',
            'valor.required' => 'O valor total do pedido é obrigatório',
            'valor.numeric' => 'O valor total deve ser um número',
            'valor.min' => 'O valor total não pode ser negativo',
            'nroformapagto.required' => 'A forma de pagamento é obrigatória',
            'nroformapagto.integer' => 'A forma de pagamento deve ser um número inteiro',
            'usuinclusao.required' => 'O usuário de inclusão é obrigatório',
            'usuinclusao.max' => 'O usuário de inclusão não pode ter mais de 50 caracteres',
            'nroparcelas.required' => 'O número de parcelas é obrigatório',
            'nroparcelas.integer' => 'O número de parcelas deve ser um número inteiro',
            'nroparcelas.min' => 'O número de parcelas deve ser pelo menos 1',
            'codoperadoracartao.integer' => 'O código da operadora deve ser um número inteiro',
            'nrocartao.max' => 'O número do cartão não pode ter mais de 20 caracteres',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação dos dados do pedido',
            'data' => $validator->errors(),
            'errors' => $validator->errors() // Formato esperado pelos testes Laravel
        ], 422));
    }
}