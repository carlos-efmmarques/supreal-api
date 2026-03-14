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
     * @group Cupom Digital
     * @queryParam nroempresa integer required Número da empresa. Example: 2
     * @queryParam nrocheckout integer required Número do checkout. Example: 11
     * @queryParam coo integer required Contador de Ordem de Operação. Example: 377249
     * @response 200 {"success": true, "message": "Cupom recuperado com sucesso", "data": {"seqdocto": 1120147, "itens": [...], "cliente": {...}}}
     * @response 404 {"success": false, "message": "Cupom não encontrado para os parâmetros informados", "data": null}
     * @response 422 {"success": false, "message": "Erro de validação dos parâmetros do cupom", "data": {...}}
     * @response 500 {"success": false, "message": "Erro ao consultar cupom", "data": null}
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
                SELECT seqdocto
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
                SELECT  yp.DESCRICAO,
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
                FROM consincomonitor.TB_DOCTOITEM td
                JOIN yandeh_produto yp
                  ON td.CODACESSO = yp.SKU
                 AND td.NROEMPRESA = yp.ID_LOJA
                WHERE td.NROEMPRESA = :nroempresa
                  AND td.NROCHECKOUT = :nrocheckout
                  AND td.SEQDOCTO = :seqdocto
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
