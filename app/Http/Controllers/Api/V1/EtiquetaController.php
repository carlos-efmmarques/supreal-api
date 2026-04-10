<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class EtiquetaController extends BaseController
{
    private const SMB_USER = 'supreal\print.user.real';
    private const SMB_PASS = 'C87hjk030$%';

    private function getOracleConnection()
    {
        $host = env('ORACLE_HOST', '10.36.100.101');
        $port = env('ORACLE_PORT', '1521');
        $service = env('ORACLE_SERVICE_NAME', 'consinco');
        $user = env('ORACLE_USERNAME', 'consinco');
        $pass = env('ORACLE_PASSWORD', 'consinco');

        $tns = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$host})(PORT={$port}))(CONNECT_DATA=(SERVICE_NAME={$service})))";
        $conn = oci_connect($user, $pass, $tns, 'AL32UTF8');

        if (!$conn) {
            $e = oci_error();
            throw new Exception('Erro ao conectar Oracle: ' . $e['message']);
        }

        return $conn;
    }

    /**
     * Envia etiquetas para impressão a partir de uma lista de códigos de produto.
     * Na mesma sessão OCI: insere na tabela temporária, lê a view de layout e envia via SMB.
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

        $conn = null;

        try {
            $conn = $this->getOracleConnection();

            // 1. Buscar NROSEGMENTO da empresa
            $sql = "SELECT NROEMPRESA, NROSEGMENTOPRINC FROM MAX_EMPRESA WHERE NROEMPRESA = :nroempresa";
            $stmt = oci_parse($conn, $sql);
            $nroemp = $nroempresa;
            oci_bind_by_name($stmt, ':nroempresa', $nroemp);
            oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            $empresa = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);

            if (!$empresa) {
                oci_close($conn);
                return $this->notFound("Empresa $nroempresa não encontrada.");
            }

            $nrosegmento = $empresa['NROSEGMENTOPRINC'];

            // 2. Buscar SOFTPDV e diretório da impressora de gôndola
            $sql = "SELECT A.SOFTPDV, A.NOMEVIEW, A.DIRETEXPORTARQUIVO, A.TIPOEXPORTACAO
                    FROM MRL_EMPSOFTPDV A
                    WHERE A.NROEMPRESA = :nroempresa
                      AND A.TIPOSOFT = 'G'
                      AND A.STATUS = 'A'
                      AND NVL(A.INDETIQWEB, 'N') != 'S'";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':nroempresa', $nroemp);
            oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            $softpdv = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);

            if (!$softpdv) {
                oci_close($conn);
                return $this->notFound("Nenhuma impressora de gôndola configurada para empresa $nroempresa.");
            }

            $nomeView = $softpdv['NOMEVIEW'];
            $diretorio = $softpdv['DIRETEXPORTARQUIVO'];

            Log::info('[Etiqueta] Impressora encontrada', [
                'softpdv' => $softpdv['SOFTPDV'],
                'nomeview' => $nomeView,
                'diretorio' => $diretorio,
            ]);

            $inseridos = [];
            $erros = [];

            // 3. Processar cada código
            foreach ($codigos as $codigo) {
                try {
                    $codigoTrimmed = ltrim(trim($codigo), '0') ?: '0';

                    // Buscar produto
                    $sqlProd = "SELECT A.SEQFAMILIA, A.DESCREDUZIDA, A.SEQPRODUTO,
                                       NVL(QTDETIQUETA, 1) AS QTDETIQUETA, B.QTDEMBALAGEM,
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
                                  )";
                    $stmtProd = oci_parse($conn, $sqlProd);
                    $seg = $nrosegmento;
                    $emp = $nroempresa;
                    $cod = $codigoTrimmed;
                    $emp2 = $nroempresa;
                    $seg2 = $nrosegmento;
                    oci_bind_by_name($stmtProd, ':nrosegmento', $seg);
                    oci_bind_by_name($stmtProd, ':nroempresa', $emp);
                    oci_bind_by_name($stmtProd, ':codacesso', $cod);
                    oci_bind_by_name($stmtProd, ':nroempresa2', $emp2);
                    oci_bind_by_name($stmtProd, ':nrosegmento2', $seg2);
                    oci_execute($stmtProd, OCI_NO_AUTO_COMMIT);
                    $produto = oci_fetch_assoc($stmtProd);
                    oci_free_statement($stmtProd);

                    if (!$produto) {
                        $erros[] = ['codigo' => $codigo, 'erro' => 'Produto não encontrado'];
                        continue;
                    }

                    // INSERT na tabela temporária (mesma sessão)
                    $sqlIns = "INSERT INTO MRLX_BASEETIQUETAPROD
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
                         1, 1, :nrosegmento,
                         NULL, NULL, NULL,
                         NULL, NULL, NULL,
                         NULL, :softpdv, 'E',
                         NULL, NULL, 'MAX0588A', :qtdembalagem)";

                    $stmtIns = oci_parse($conn, $sqlIns);
                    $insEmp = $nroempresa;
                    $insSeq = $produto['SEQPRODUTO'];
                    $insCod = $produto['CODACESSO'];
                    $insQtd = $produto['QTDETIQUETA'];
                    $insSeg = $nrosegmento;
                    $insSoft = $softpdv['SOFTPDV'];
                    $insEmb = $produto['QTDEMBALAGEM'];
                    oci_bind_by_name($stmtIns, ':nroempresa', $insEmp);
                    oci_bind_by_name($stmtIns, ':seqproduto', $insSeq);
                    oci_bind_by_name($stmtIns, ':codacesso', $insCod);
                    oci_bind_by_name($stmtIns, ':qtdetiqueta', $insQtd);
                    oci_bind_by_name($stmtIns, ':nrosegmento', $insSeg);
                    oci_bind_by_name($stmtIns, ':softpdv', $insSoft);
                    oci_bind_by_name($stmtIns, ':qtdembalagem', $insEmb);
                    $result = oci_execute($stmtIns, OCI_NO_AUTO_COMMIT);
                    oci_free_statement($stmtIns);

                    if (!$result) {
                        $e = oci_error($conn);
                        throw new Exception($e['message'] ?? 'Erro no INSERT');
                    }

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

            if (empty($inseridos)) {
                oci_close($conn);
                return $this->success([
                    'inseridos' => 0,
                    'erros' => count($erros),
                    'detalhes' => [],
                    'falhas' => $erros,
                ], 'Nenhum produto encontrado para impressão.');
            }

            // 4. Ler conteúdo das etiquetas da view (mesma sessão — vê a tabela temporária)
            $sqlView = "SELECT FC_CONCATENA_ESPACO(LINHA, 250, 4000) AS CONTEUDO
                        FROM {$nomeView}
                        WHERE NROEMPRESA = :nroempresa";
            $stmtView = oci_parse($conn, $sqlView);
            $viewEmp = $nroempresa;
            oci_bind_by_name($stmtView, ':nroempresa', $viewEmp);
            oci_execute($stmtView, OCI_NO_AUTO_COMMIT);

            $conteudoEtiqueta = '';
            while ($row = oci_fetch_assoc($stmtView)) {
                $conteudoEtiqueta .= rtrim($row['CONTEUDO']) . "\n";
            }
            oci_free_statement($stmtView);

            // Commit e fechar sessão Oracle
            oci_commit($conn);
            oci_close($conn);
            $conn = null;

            if (empty($conteudoEtiqueta)) {
                Log::warning('[Etiqueta] View retornou conteúdo vazio', [
                    'nroempresa' => $nroempresa,
                    'view' => $nomeView,
                ]);
                return $this->error('A view de etiquetas não retornou conteúdo. Verifique a configuração.');
            }

            // 5. Enviar para impressora via SMB
            $tmpFile = tempnam(sys_get_temp_dir(), 'etiq_') . '.raw';
            file_put_contents($tmpFile, $conteudoEtiqueta);

            // Converter path Windows para SMB: \\10.36.4.202\elgin → //10.36.4.202/elgin
            $smbPath = str_replace('\\', '/', $diretorio);
            $smbPath = ltrim($smbPath, '/');
            $smbPath = '//' . $smbPath;

            $smbUser = self::SMB_USER;
            $smbPass = self::SMB_PASS;

            $cmd = sprintf(
                "smbclient %s -U %s -c 'print %s' 2>&1",
                escapeshellarg($smbPath),
                escapeshellarg("{$smbUser}%{$smbPass}"),
                escapeshellarg($tmpFile)
            );

            $output = shell_exec($cmd);
            unlink($tmpFile);

            $impressaoOk = strpos($output, 'putting file') !== false || strpos($output, 'kb/s') !== false;

            if (!$impressaoOk) {
                Log::error('[Etiqueta] Erro ao enviar para impressora', [
                    'diretorio' => $diretorio,
                    'smb_output' => $output,
                ]);
                return $this->error('Etiquetas geradas mas erro ao enviar para impressora: ' . trim($output));
            }

            Log::info('[Etiqueta] Impressão concluída com sucesso', [
                'nroempresa' => $nroempresa,
                'inseridos'  => count($inseridos),
                'erros'      => count($erros),
                'smb_output' => trim($output),
            ]);

            return $this->success([
                'inseridos'  => count($inseridos),
                'erros'      => count($erros),
                'detalhes'   => $inseridos,
                'falhas'     => $erros,
                'impressora' => [
                    'softpdv'    => $softpdv['SOFTPDV'],
                    'view'       => $nomeView,
                    'diretorio'  => $diretorio,
                ],
            ], 'Etiquetas enviadas para impressão.');

        } catch (Exception $e) {
            Log::error('[Etiqueta] Erro geral na impressão', [
                'nroempresa' => $nroempresa,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            if ($conn) {
                oci_close($conn);
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
        $conn = null;

        try {
            $conn = $this->getOracleConnection();

            $sql = "SELECT A.SOFTPDV, A.NOMEVIEW, A.DIRETEXPORTARQUIVO,
                           A.TIPOSOFT, A.STATUS,
                           DECODE(A.TIPOSOFT, 'R', 'P', A.TIPOSOFT) AS TIPO,
                           A.NOMEQRPETIQUETA, A.QTDCOLUNASETIQ, A.TIPOEXPORTACAO
                    FROM MRL_EMPSOFTPDV A
                    WHERE A.NROEMPRESA = :nroempresa
                      AND A.TIPOSOFT IN ('G', 'R', 'L', 'A')
                      AND A.STATUS = 'A'
                      AND NVL(A.INDETIQWEB, 'N') != 'S'";
            $stmt = oci_parse($conn, $sql);
            $emp = $nroempresa;
            oci_bind_by_name($stmt, ':nroempresa', $emp);
            oci_execute($stmt, OCI_DEFAULT);

            $impressoras = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $impressoras[] = $row;
            }
            oci_free_statement($stmt);
            oci_close($conn);

            return $this->success($impressoras, 'Impressoras encontradas.');

        } catch (Exception $e) {
            Log::error('[Etiqueta] Erro ao consultar impressoras', [
                'nroempresa' => $nroempresa,
                'error'      => $e->getMessage(),
            ]);

            if ($conn) {
                oci_close($conn);
            }

            return $this->serverError('Erro ao consultar impressoras: ' . $e->getMessage());
        }
    }
}
