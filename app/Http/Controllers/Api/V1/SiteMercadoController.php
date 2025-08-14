<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\SiteMercado\InserePedidoRequest;
use App\Http\Requests\SiteMercado\InsereItensRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SiteMercadoController extends BaseController
{
    /**
     * Insere um pedido no ERP Oracle usando a procedure sp_inserePedidoSitemercado
     * 
     * @group Site Mercado
     * @bodyParam nropedidoafv string required Número do pedido AFV. Example: PED123456
     * @bodyParam nroempresa integer required Número da empresa. Example: 1
     * @bodyParam nrocgccpf string required Número do CPF/CNPJ (sem dígito). Example: 12345678901
     * @bodyParam digcgccpf string required Dígito verificador do CPF/CNPJ. Example: 23
     * @bodyParam nomerazao string required Nome ou razão social. Example: João da Silva
     * @bodyParam fantasia string Nome fantasia. Example: João da Silva
     * @bodyParam fisicajuridica string required Tipo de pessoa (F=Física, J=Jurídica). Example: F
     * @bodyParam sexo string Sexo (M/F). Example: M
     * @bodyParam cidade string required Cidade. Example: São Paulo
     * @bodyParam uf string required UF. Example: SP
     * @bodyParam bairro string required Bairro. Example: Centro
     * @bodyParam logradouro string required Logradouro. Example: Rua das Flores
     * @bodyParam nrologradouro string required Número do logradouro. Example: 123
     * @bodyParam cmpltologradouro string Complemento. Example: Apto 101
     * @bodyParam cep string required CEP. Example: 01234567
     * @bodyParam foneddd1 string DDD do telefone 1. Example: 11
     * @bodyParam fonenro1 string Número do telefone 1. Example: 987654321
     * @bodyParam foneddd2 string DDD do telefone 2. Example: 11
     * @bodyParam fonenro2 string Número do telefone 2. Example: 123456789
     * @bodyParam inscricaorg string Inscrição RG. Example: 123456789
     * @bodyParam dtanascfund date Data de nascimento/fundação. Example: 1990-01-01
     * @bodyParam email string required Email. Example: joao@email.com
     * @bodyParam emailnfe string Email para NFe. Example: joao@email.com
     * @bodyParam indentregaretira string required Indicador entrega/retirada (E=Entrega, R=Retirada). Example: E
     * @bodyParam dtapedidoafv date required Data do pedido. Example: 2025-01-14
     * @bodyParam vlrtotfrete number Valor total do frete. Example: 15.50
     * @bodyParam valor number required Valor total do pedido. Example: 150.75
     * @bodyParam nroformapagto integer required Forma de pagamento. Example: 1
     * @bodyParam usuinclusao string required Usuário de inclusão. Example: API_SITEMERCADO
     * @bodyParam nroparcelas integer required Número de parcelas. Example: 1
     * @bodyParam codoperadoracartao integer Código da operadora do cartão. Example: 1
     * @bodyParam nrocartao string Número do cartão. Example: ****1234
     * @response 201 {"success": true, "message": "Pedido inserido com sucesso no ERP", "data": {"nropedidoafv": "PED123456"}}
     * @response 422 {"success": false, "message": "Erro de validação", "data": {...}}
     * @response 500 {"success": false, "message": "Erro ao inserir pedido no ERP", "data": {"error": "..."}}
     */
    public function inserePedido(InserePedidoRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Preparar os parâmetros para a procedure
            $params = [
                'p_nropedidoafv' => $data['nropedidoafv'],
                'p_nroempresa' => $data['nroempresa'],
                'p_nrocgccpf' => $data['nrocgccpf'],
                'p_digcgccpf' => $data['digcgccpf'],
                'p_nomerazao' => $data['nomerazao'],
                'p_fantasia' => $data['fantasia'] ?? $data['nomerazao'],
                'p_fisicajuridica' => $data['fisicajuridica'],
                'p_sexo' => $data['sexo'] ?? null,
                'p_cidade' => $data['cidade'],
                'p_uf' => $data['uf'],
                'p_bairro' => $data['bairro'],
                'p_logradouro' => $data['logradouro'],
                'p_nrologradouro' => $data['nrologradouro'],
                'p_cmpltologradouro' => $data['cmpltologradouro'] ?? null,
                'p_cep' => $data['cep'],
                'p_foneddd1' => $data['foneddd1'] ?? null,
                'p_fonenro1' => $data['fonenro1'] ?? null,
                'p_foneddd2' => $data['foneddd2'] ?? null,
                'p_fonenro2' => $data['fonenro2'] ?? null,
                'p_inscricaorg' => $data['inscricaorg'] ?? null,
                'p_dtanascfund' => $data['dtanascfund'] ?? null,
                'p_email' => $data['email'],
                'p_emailnfe' => $data['emailnfe'] ?? $data['email'],
                'p_indentregaretira' => $data['indentregaretira'],
                'p_dtapedidoafv' => $data['dtapedidoafv'],
                'p_vlrtotfrete' => $data['vlrtotfrete'] ?? 0,
                'p_valor' => $data['valor'],
                'p_nroformapagto' => $data['nroformapagto'],
                'p_usuinclusao' => $data['usuinclusao'],
                'p_nroparcelas' => $data['nroparcelas'],
                'p_codoperadoracartao' => $data['codoperadoracartao'] ?? null,
                'p_nrocartao' => $data['nrocartao'] ?? null,
            ];

            // Log da tentativa de inserção
            Log::info('SiteMercado: Tentativa de inserção de pedido', [
                'nropedidoafv' => $data['nropedidoafv'],
                'usuario' => $data['usuinclusao']
            ]);

            // Executar a procedure Oracle
            DB::connection('oracle')->statement(
                'CALL consinco.sp_inserePedidoSitemercado(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                array_values($params)
            );

            Log::info('SiteMercado: Pedido inserido com sucesso', [
                'nropedidoafv' => $data['nropedidoafv']
            ]);

            return $this->success([
                'nropedidoafv' => $data['nropedidoafv']
            ], 'Pedido inserido com sucesso no ERP', 201);

        } catch (Exception $e) {
            Log::error('SiteMercado: Erro ao inserir pedido', [
                'nropedidoafv' => $data['nropedidoafv'] ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverError('Erro ao inserir pedido no ERP: ' . $e->getMessage());
        }
    }

    /**
     * Insere itens de um pedido no ERP Oracle usando a procedure sp_insereItensSitemercado
     * 
     * @group Site Mercado
     * @bodyParam nropedidoafv string required Número do pedido AFV (usado como referência). Example: PED123456
     * @bodyParam seqpedvendaitem integer required Sequência do item no pedido. Example: 1
     * @bodyParam codacesso string required Código de acesso do produto. Example: COD12345
     * @bodyParam seqproduto integer required Sequência do produto. Example: 12345
     * @bodyParam qtdpedida number required Quantidade pedida. Example: 2.5
     * @bodyParam qtdembalagem number required Quantidade da embalagem. Example: 1.0
     * @bodyParam vlrembtabpreco number required Valor da embalagem tabela preço. Example: 15.90
     * @bodyParam vlrembinformado number required Valor da embalagem informado. Example: 15.90
     * @response 201 {"success": true, "message": "Item inserido com sucesso no ERP", "data": {"nropedidoafv": "PED123456", "seqpedvendaitem": 1}}
     * @response 422 {"success": false, "message": "Erro de validação", "data": {...}}
     * @response 500 {"success": false, "message": "Erro ao inserir item no ERP", "data": {"error": "..."}}
     */
    public function insereItens(InsereItensRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Preparar os parâmetros para a procedure
            $params = [
                'p_seqedipedvenda' => $data['nropedidoafv'], // A procedure usa nropedidoafv aqui
                'p_seqpedvendaitem' => $data['seqpedvendaitem'],
                'p_codacesso' => $data['codacesso'],
                'p_seqproduto' => $data['seqproduto'],
                'p_qtdpedida' => $data['qtdpedida'],
                'p_qtdembalagem' => $data['qtdembalagem'],
                'p_vlrembtabpreco' => $data['vlrembtabpreco'],
                'p_vlrembinformado' => $data['vlrembinformado'],
            ];

            // Log da tentativa de inserção
            Log::info('SiteMercado: Tentativa de inserção de item', [
                'nropedidoafv' => $data['nropedidoafv'],
                'seqpedvendaitem' => $data['seqpedvendaitem'],
                'codacesso' => $data['codacesso']
            ]);

            // Executar a procedure Oracle
            DB::connection('oracle')->statement(
                'CALL consinco.sp_insereItensSitemercado(?, ?, ?, ?, ?, ?, ?, ?)',
                array_values($params)
            );

            Log::info('SiteMercado: Item inserido com sucesso', [
                'nropedidoafv' => $data['nropedidoafv'],
                'seqpedvendaitem' => $data['seqpedvendaitem']
            ]);

            return $this->success([
                'nropedidoafv' => $data['nropedidoafv'],
                'seqpedvendaitem' => $data['seqpedvendaitem']
            ], 'Item inserido com sucesso no ERP', 201);

        } catch (Exception $e) {
            Log::error('SiteMercado: Erro ao inserir item', [
                'nropedidoafv' => $data['nropedidoafv'] ?? 'N/A',
                'seqpedvendaitem' => $data['seqpedvendaitem'] ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverError('Erro ao inserir item no ERP: ' . $e->getMessage());
        }
    }
}