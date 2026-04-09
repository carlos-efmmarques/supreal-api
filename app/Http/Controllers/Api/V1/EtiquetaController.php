<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class EtiquetaController extends BaseController
{
    private function getOraclePdo(): \PDO
    {
        $dsn = 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=' . env('ORACLE_HOST', '10.36.100.101') . ')(PORT=' . env('ORACLE_PORT', '1521') . '))(CONNECT_DATA=(SERVICE_NAME=' . env('ORACLE_SERVICE_NAME', 'consinco') . ')))';
        $pdo = new \PDO($dsn, env('ORACLE_USERNAME', 'consinco'), env('ORACLE_PASSWORD', 'consinco'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
        return $pdo;
    }

    /**
     * Envia etiquetas para impressão a partir de uma lista de códigos de produto.
     * Insere na fila MRLX_BASEETIQUETAPROD do Consinco para processamento automático.
     *
     * @group Etiquetas
     * @bodyParam nroempresa integer required Número da empresa/filial. Example: 104
     * @bodyParam codigos array required Lista de códigos de barras dos produtos. Example: ["7896045110667", "7891234567890"]
     */
    public function imprimirDivergencias(Request $request): JsonResponse
    {
        $request->validate([
            'nroempresa' => 'required|integer',
            'codigos'    => 'required|array|min:1|max:200',
            'codigos.*'  => 'required|string',
        ]);

        $nroempresa = $request->nroempresa;
        $codigos = $request->codigos;

        Log::info('[Etiqueta] Solicitação de impressão', [
            'nroempresa' => $nroempresa,
            'qtd_codigos' => count($codigos),
            'token' => $request->api_token->name ?? 'unknown',
        ]);

        try {
            $pdo = $this->getOraclePdo();
            $pdo->beginTransaction();

            // 1. Buscar NROSEGMENTO da empresa
            $stmtEmp = $pdo->prepare("
                SELECT NROEMPRESA, NROSEGMENTOPRINC, NRODIVISAO
                FROM MAX_EMPRESA
                WHERE NROEMPRESA = :nroempresa
            ");
            $stmtEmp->execute(['nroempresa' => $nroempresa]);
            $empresa = $stmtEmp->fetch(\PDO::FETCH_ASSOC);

            if (!$empresa) {
                return $this->notFound("Empresa $nroempresa não encontrada.");
            }

            $nrosegmento = $empresa['NROSEGMENTOPRINC'];

            // 2. Buscar SOFTPDV da empresa (impressora de etiqueta de gôndola)
            $stmtSoft = $pdo->prepare("
                SELECT A.SOFTPDV, A.NOMEVIEW, A.DIRETEXPORTARQUIVO,
                       A.NOMEQRPETIQUETA, A.QTDCOLUNASETIQ, A.TIPOEXPORTACAO
                FROM MRL_EMPSOFTPDV A
                WHERE A.NROEMPRESA = :nroempresa
                  AND A.TIPOSOFT IN ('G', 'R', 'L', 'A')
                  AND A.STATUS = 'A'
                  AND NVL(A.INDETIQWEB, 'N') != 'S'
                ORDER BY DECODE((SELECT PADRAOETIQGONDOLA FROM MAX_EMPRESA WHERE NROEMPRESA = :nroempresa2), A.SOFTPDV, 0, 1)
            ");
            $stmtSoft->execute([
                'nroempresa'  => $nroempresa,
                'nroempresa2' => $nroempresa,
            ]);
            $softpdv = $stmtSoft->fetch(\PDO::FETCH_ASSOC);

            if (!$softpdv) {
                return $this->notFound("Nenhuma impressora de etiqueta configurada para empresa $nroempresa.");
            }

            Log::info('[Etiqueta] Impressora encontrada', [
                'softpdv' => $softpdv['SOFTPDV'],
                'nomeview' => $softpdv['NOMEVIEW'],
                'diretorio' => $softpdv['DIRETEXPORTARQUIVO'],
            ]);

            // 3. Preparar statement de busca do produto
            $stmtProd = $pdo->prepare("
                SELECT A.SEQFAMILIA, A.DESCREDUZIDA, A.SEQPRODUTO,
                       A.NROEMPRESA, A.DESCCOMPLETA, A.NROGONDOLA,
                       NVL(QTDETIQUETA, 1) AS QTDETIQUETA, B.QTDEMBALAGEM,
                       E.EMBALAGEM || ' ' || E.QTDEMBALAGEM AS DESCEMBALAGEM,
                       TO_CHAR(B.CODACESSO) AS CODACESSO
                FROM MRLV_BASEETIQUETAPRODCOD A, MAP_PRODCODIGO B, MAP_FAMEMBALAGEM E
                WHERE A.SEQPRODUTO = B.SEQPRODUTO
                  AND A.QTDEMBALAGEM = B.QTDEMBALAGEM
                  AND E.QTDEMBALAGEM = A.QTDEMBALAGEM
                  AND E.SEQFAMILIA = A.SEQFAMILIA
                  AND B.TIPCODIGO IN ('E', 'D', 'B')
                  AND A.STATUSVENDA = 'A'
                  AND A.NROSEGMENTO = :nrosegmento
                  AND A.NROEMPRESA = :nroempresa
                  AND B.CODACESSO = :codacesso
                  AND A.QTDEMBALAGEM = (
                      SELECT C.QTDEMBALAGEM
                      FROM MRL_PRODEMPSEG C
                      WHERE C.SEQPRODUTO = A.SEQPRODUTO
                        AND C.NROEMPRESA = :nroempresa2
                        AND C.NROSEGMENTO = :nrosegmento2
                        AND C.STATUSVENDA = 'A'
                        AND C.QTDEMBALAGEM = A.QTDEMBALAGEM
                  )
            ");

            // 4. Preparar statement de INSERT na fila
            $stmtInsert = $pdo->prepare("
                INSERT INTO MRLX_BASEETIQUETAPROD
                    (NROEMPRESA, SEQPRODUTO, TIPOETIQUETA,
                     CODACESSO, TIPOPRECO, QTDETIQUETA,
                     TIPOCODIGO, QTDEMBCODACESSO,
                     NROETQEMITIDA, NROLINHA, NROSEGMENTO,
                     LOCALENTRADA, NROEMPPEDSELINVERSA, DESCETIQUETAPROMOC,
                     DTABASEPRECO, DTAEMBALAMENTO, DIGITOVERIFICADOR,
                     SIGNETIQELETRONICA, SOFTPDV, INDEMIETIQUETA,
                     SEQPROMOCPDV, SEQPROMOCESPECIAL, CODAPLICACAO, QTDEMBALAGEMAPLICACAO)
                VALUES
                    (:nroempresa, :seqproduto, 'G',
                     :codacesso, 'G', :qtdetiqueta,
                     NULL, NULL,
                     0, 1, :nrosegmento,
                     NULL, NULL, NULL,
                     NULL, NULL, NULL,
                     NULL, :softpdv, 'N',
                     NULL, NULL, 'MAX0588A', :qtdembalagem)
            ");

            $inseridos = [];
            $erros = [];

            // 5. Processar cada código
            foreach ($codigos as $codigo) {
                try {
                    $codigoTrimmed = ltrim(trim($codigo), '0') ?: '0';

                    $stmtProd->execute([
                        'nrosegmento'  => $nrosegmento,
                        'nroempresa'   => $nroempresa,
                        'codacesso'    => $codigoTrimmed,
                        'nroempresa2'  => $nroempresa,
                        'nrosegmento2' => $nrosegmento,
                    ]);

                    $produto = $stmtProd->fetch(\PDO::FETCH_ASSOC);

                    if (!$produto) {
                        $erros[] = ['codigo' => $codigo, 'erro' => 'Produto não encontrado'];
                        continue;
                    }

                    $stmtInsert->execute([
                        'nroempresa'   => $nroempresa,
                        'seqproduto'   => $produto['SEQPRODUTO'],
                        'codacesso'    => $produto['CODACESSO'],
                        'qtdetiqueta'  => $produto['QTDETIQUETA'],
                        'nrosegmento'  => $nrosegmento,
                        'softpdv'      => $softpdv['SOFTPDV'],
                        'qtdembalagem' => $produto['QTDEMBALAGEM'],
                    ]);

                    $inseridos[] = [
                        'codigo'    => $codigo,
                        'produto'   => $produto['DESCREDUZIDA'],
                        'seqproduto' => $produto['SEQPRODUTO'],
                    ];

                } catch (Exception $e) {
                    $erros[] = [
                        'codigo' => $codigo,
                        'erro'   => $e->getMessage(),
                    ];
                    Log::warning('[Etiqueta] Erro ao processar produto', [
                        'codigo' => $codigo,
                        'error'  => $e->getMessage(),
                    ]);
                }
            }

            $pdo->commit();

            Log::info('[Etiqueta] Impressão concluída', [
                'nroempresa' => $nroempresa,
                'inseridos'  => count($inseridos),
                'erros'      => count($erros),
            ]);

            return $this->success([
                'inseridos'  => count($inseridos),
                'erros'      => count($erros),
                'detalhes'   => $inseridos,
                'falhas'     => $erros,
                'impressora' => [
                    'softpdv'    => $softpdv['SOFTPDV'],
                    'view'       => $softpdv['NOMEVIEW'],
                    'diretorio'  => $softpdv['DIRETEXPORTARQUIVO'],
                ],
            ], 'Etiquetas enviadas para impressão.');

        } catch (Exception $e) {
            Log::error('[Etiqueta] Erro geral na impressão', [
                'nroempresa' => $nroempresa,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return $this->serverError('Erro ao processar impressão de etiquetas: ' . $e->getMessage());
        }
    }

    /**
     * Consulta impressoras de etiqueta configuradas para uma empresa.
     *
     * @group Etiquetas
     * @queryParam nroempresa integer required Número da empresa. Example: 104
     */
    public function consultaImpressoras(Request $request): JsonResponse
    {
        $request->validate([
            'nroempresa' => 'required|integer',
        ]);

        $nroempresa = $request->nroempresa;

        try {
            $pdo = $this->getOraclePdo();

            $stmt = $pdo->prepare("
                SELECT A.SOFTPDV, A.NOMEVIEW, A.DIRETEXPORTARQUIVO,
                       A.TIPOSOFT, A.STATUS,
                       DECODE(A.TIPOSOFT, 'R', 'P', A.TIPOSOFT) AS TIPO,
                       A.NOMEQRPETIQUETA, A.QTDCOLUNASETIQ, A.TIPOEXPORTACAO
                FROM MRL_EMPSOFTPDV A
                WHERE A.NROEMPRESA = :nroempresa
                  AND A.TIPOSOFT IN ('G', 'R', 'L', 'A')
                  AND A.STATUS = 'A'
                  AND NVL(A.INDETIQWEB, 'N') != 'S'
            ");
            $stmt->execute(['nroempresa' => $nroempresa]);
            $impressoras = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $this->success($impressoras, 'Impressoras encontradas.');

        } catch (Exception $e) {
            Log::error('[Etiqueta] Erro ao consultar impressoras', [
                'nroempresa' => $nroempresa,
                'error'      => $e->getMessage(),
            ]);

            return $this->serverError('Erro ao consultar impressoras: ' . $e->getMessage());
        }
    }
}
