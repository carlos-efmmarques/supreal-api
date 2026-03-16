<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Cupom\ConsultaCupomRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class CupomController extends BaseController
{
    private function getOraclePdo(): \PDO
    {
        $dsn = 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=' . env('ORACLE_HOST', '10.36.100.101') . ')(PORT=' . env('ORACLE_PORT', '1521') . '))(CONNECT_DATA=(SERVICE_NAME=' . env('ORACLE_SERVICE_NAME', 'consinco') . ')))';
        $pdo = new \PDO($dsn, env('ORACLE_USERNAME', 'consinco'), env('ORACLE_PASSWORD', 'consinco'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    /**
     * Consulta os dados completos de um cupom fiscal (itens e cliente)
     *
     * Retorna todos os itens registrados no cupom com descrição do produto, valores, descontos e promoções,
     * além dos dados do cliente associado (CPF/CNPJ) quando identificado.
     * Ideal para montar a visualização de um cupom fiscal digital.
     *
     * O fluxo interno executa 3 consultas:
     * 1. Busca o `seqdocto` na tabela `TB_DOCTO` usando empresa + checkout + COO
     * 2. Recupera os itens do cupom em `TB_DOCTOITEM` com JOIN em `yandeh_produto` para descrição
     * 3. Busca o cliente em `TB_DOCTOCUPOM` (opcional, pode não existir)
     *
     * @group Cupom Digital
     * @queryParam nroempresa integer required Número da empresa no ERP. Example: 2
     * @queryParam nrocheckout integer required Número do checkout (caixa) que emitiu o cupom. Example: 11
     * @queryParam coo integer required COO - Contador de Ordem de Operação do cupom fiscal. Example: 377249
     *
     * @response 200 scenario="Cupom com cliente identificado" {"success":true,"message":"Cupom recuperado com sucesso","data":{"seqdocto":1120147,"nroempresa":2,"nrocheckout":11,"coo":377249,"itens":[{"DESCRICAO":"ARROZ BRANCO TIPO 1 5KG","SEQITEM":1,"DTAHOREMISSAO":"2025-06-15 10:32:00","SEQPRODUTO":45678,"CODACESSO":"7891234567890","QUANTIDADE":2,"VLRUNITARIO":24.90,"VLRDESCONTO":0,"VLRTOTAL":49.80,"NROTRIBUTACAO":1,"STATUS":"A","PROMOCAO":null,"INSERCAO":1},{"DESCRICAO":"LEITE INTEGRAL 1L","SEQITEM":2,"DTAHOREMISSAO":"2025-06-15 10:32:15","SEQPRODUTO":12345,"CODACESSO":"7890987654321","QUANTIDADE":6,"VLRUNITARIO":5.49,"VLRDESCONTO":3.00,"VLRTOTAL":29.94,"NROTRIBUTACAO":1,"STATUS":"A","PROMOCAO":"LEVE 6 PAGUE 5","INSERCAO":2},{"DESCRICAO":"DETERGENTE NEUTRO 500ML","SEQITEM":3,"DTAHOREMISSAO":"2025-06-15 10:32:30","SEQPRODUTO":78901,"CODACESSO":"7894561237890","QUANTIDADE":1,"VLRUNITARIO":2.99,"VLRDESCONTO":0,"VLRTOTAL":2.99,"NROTRIBUTACAO":2,"STATUS":"A","PROMOCAO":null,"INSERCAO":3}],"cliente":{"CNPJCPF":"12345678901","SEQPESSOA":98765}}}
     * @response 200 scenario="Cupom sem cliente identificado" {"success":true,"message":"Cupom recuperado com sucesso","data":{"seqdocto":1120200,"nroempresa":2,"nrocheckout":11,"coo":377300,"itens":[{"DESCRICAO":"CAFE TORRADO MOIDO 500G","SEQITEM":1,"DTAHOREMISSAO":"2025-06-15 14:05:00","SEQPRODUTO":33210,"CODACESSO":"7891112223334","QUANTIDADE":1,"VLRUNITARIO":18.90,"VLRDESCONTO":0,"VLRTOTAL":18.90,"NROTRIBUTACAO":1,"STATUS":"A","PROMOCAO":null,"INSERCAO":1}],"cliente":null}}
     * @response 404 scenario="Cupom não encontrado" {"success":false,"message":"Cupom não encontrado para os parâmetros informados","data":null}
     * @response 422 scenario="Parâmetros inválidos" {"success":false,"message":"Erro de validação dos parâmetros do cupom","data":{"nroempresa":["O número da empresa é obrigatório"],"coo":["O COO deve ser um número inteiro"]},"errors":{"nroempresa":["O número da empresa é obrigatório"],"coo":["O COO deve ser um número inteiro"]}}
     * @response 500 scenario="Erro de conexão com Oracle" {"success":false,"message":"Erro ao consultar cupom: could not connect to Oracle","data":null}
     */
    public function show(ConsultaCupomRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $nroempresa = $data['nroempresa'];
            $nrocheckout = $data['nrocheckout'];
            $coo = $data['coo'];

            Log::info('Cupom: Consulta de cupom', [
                'nroempresa' => $nroempresa,
                'nrocheckout' => $nrocheckout,
                'coo' => $coo,
            ]);

            $pdo = $this->getOraclePdo();

            // 1. Buscar o seqdocto
            $stmtDocto = $pdo->prepare("
                SELECT max(seqdocto) as seqdocto
                FROM consincomonitor.TB_DOCTO
                WHERE NROEMPRESA = :nroempresa
                  AND NROCHECKOUT = :nrocheckout
                  AND COO = :coo
            ");
            $stmtDocto->execute([
                'nroempresa' => $nroempresa,
                'nrocheckout' => $nrocheckout,
                'coo' => $coo,
            ]);

            $docto = $stmtDocto->fetch(\PDO::FETCH_ASSOC);

            if (!$docto) {
                return $this->notFound('Cupom não encontrado para os parâmetros informados');
            }

            $seqdocto = $docto['SEQDOCTO'];

            // 2. Buscar os itens do cupom
            $stmtItens = $pdo->prepare("
                SELECT  yp.NOME AS DESCRICAO,
                        td.SEQITEM,
                        td.DTAHOREMISSAO,
                        td.SEQPRODUTO,
                        td.CODACESSO,
                        td.QUANTIDADE,
                        td.VLRUNITARIO,
                        td.VLRDESCONTO,
                        td.VLRTOTAL,
                        td.NROTRIBUTACAO,
                        td.STATUS,
                        td.PROMOCAO,
                        td.INSERCAO
                FROM consincomonitor.TB_DOCTOITEM td, yandeh_produto yp
                WHERE td.SEQPRODUTO = yp.CODIGO_INTERNO
                  AND td.NROEMPRESA = yp.ID_LOJA
                  AND td.NROEMPRESA = :nroempresa
                  AND td.NROCHECKOUT = :nrocheckout
                  AND td.SEQDOCTO = :seqdocto
                  AND td.STATUS = 'V'
            ");
            $stmtItens->execute([
                'nroempresa' => $nroempresa,
                'nrocheckout' => $nrocheckout,
                'seqdocto' => $seqdocto,
            ]);

            $itens = $stmtItens->fetchAll(\PDO::FETCH_ASSOC);

            // 3. Buscar dados do cliente associado ao cupom (se houver)
            $stmtCliente = $pdo->prepare("
                SELECT CNPJCPF, SEQPESSOA
                FROM consincomonitor.TB_DOCTOCUPOM
                WHERE NROEMPRESA = :nroempresa
                  AND NROCHECKOUT = :nrocheckout
                  AND SEQDOCTO = :seqdocto
            ");
            $stmtCliente->execute([
                'nroempresa' => $nroempresa,
                'nrocheckout' => $nrocheckout,
                'seqdocto' => $seqdocto,
            ]);

            $cliente = $stmtCliente->fetch(\PDO::FETCH_ASSOC);

            Log::info('Cupom: Cupom recuperado com sucesso', [
                'seqdocto' => $seqdocto,
                'total_itens' => count($itens),
                'cliente_encontrado' => !empty($cliente),
            ]);

            return $this->success([
                'seqdocto' => $seqdocto,
                'nroempresa' => $nroempresa,
                'nrocheckout' => $nrocheckout,
                'coo' => $coo,
                'itens' => $itens,
                'cliente' => $cliente ?: null,
            ], 'Cupom recuperado com sucesso');

        } catch (Exception $e) {
            Log::error('Cupom: Erro ao consultar cupom', [
                'nroempresa' => $data['nroempresa'] ?? 'N/A',
                'nrocheckout' => $data['nrocheckout'] ?? 'N/A',
                'coo' => $data['coo'] ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverError('Erro ao consultar cupom: ' . $e->getMessage());
        }
    }
}
