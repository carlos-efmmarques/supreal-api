<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\SiteMercado\InserePedidoRequest;
use App\Http\Requests\SiteMercado\InsereItensRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class SiteMercadoController extends BaseController
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
     * Insere um pedido no ERP Oracle usando a procedure SP_INSEREPEDIDOSITEMERCADO
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
            
            // Preparar os parâmetros para a procedure (33 parâmetros conforme assinatura)
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
                'p_nroformapagto' => 1, // Valor fixo conforme exemplo fornecido
                'p_usuinclusao' => 'OMS-SUPREAL', // Valor fixo conforme especificado
                'p_nroparcelas' => $data['nroparcelas'],
                'p_codoperadoracartao' => $data['codoperadoracartao'] ?? null,
                'p_nrocartao' => $data['nrocartao'] ?? null,
            ];

            // Log da tentativa de inserção
            Log::info('SiteMercado: Tentativa de inserção de pedido', [
                'nropedidoafv' => $data['nropedidoafv'],
                'usuario' => 'OMS-SUPREAL'
            ]);

            // Executar a procedure Oracle usando o formato do exemplo fornecido
            $sql = "BEGIN
                sp_inserePedidoSitemercado(
                    p_nropedidoafv       => :p_nropedidoafv,
                    p_nroempresa         => :p_nroempresa,
                    p_nrocgccpf          => :p_nrocgccpf,
                    p_digcgccpf          => :p_digcgccpf,
                    p_nomerazao          => :p_nomerazao,
                    p_fantasia           => :p_fantasia,
                    p_fisicajuridica     => :p_fisicajuridica,
                    p_sexo               => :p_sexo,
                    p_cidade             => :p_cidade,
                    p_uf                 => :p_uf,
                    p_bairro             => :p_bairro,
                    p_logradouro         => :p_logradouro,
                    p_nrologradouro      => :p_nrologradouro,
                    p_cmpltologradouro   => :p_cmpltologradouro,
                    p_cep                => :p_cep,
                    p_foneddd1           => :p_foneddd1,
                    p_fonenro1           => :p_fonenro1,
                    p_foneddd2           => :p_foneddd2,
                    p_fonenro2           => :p_fonenro2,
                    p_inscricaorg        => :p_inscricaorg,
                    p_dtanascfund        => :p_dtanascfund,
                    p_email              => :p_email,
                    p_emailnfe           => :p_emailnfe,
                    p_indentregaretira   => :p_indentregaretira,
                    p_dtapedidoafv       => :p_dtapedidoafv,
                    p_vlrtotfrete        => :p_vlrtotfrete,
                    p_valor              => :p_valor,
                    p_nroformapagto      => :p_nroformapagto,
                    p_usuinclusao        => :p_usuinclusao,
                    p_nroparcelas        => :p_nroparcelas,
                    p_codoperadoracartao => :p_codoperadoracartao,
                    p_nrocartao          => :p_nrocartao
                );
            END;";

            // Preparar datas no formato Oracle
            $bindParams = $params;
            if (!empty($bindParams['p_dtanascfund'])) {
                $bindParams['p_dtanascfund'] = date('Y-m-d', strtotime($bindParams['p_dtanascfund']));
            }
            if (!empty($bindParams['p_dtapedidoafv'])) {
                $bindParams['p_dtapedidoafv'] = date('Y-m-d', strtotime($bindParams['p_dtapedidoafv']));
            }

            // Modificar SQL para usar TO_DATE nas datas
            $sql = str_replace(
                [':p_dtanascfund', ':p_dtapedidoafv'],
                [
                    !empty($bindParams['p_dtanascfund']) ? "TO_DATE('" . $bindParams['p_dtanascfund'] . "','YYYY-MM-DD')" : 'NULL',
                    !empty($bindParams['p_dtapedidoafv']) ? "TO_DATE('" . $bindParams['p_dtapedidoafv'] . "','YYYY-MM-DD')" : 'NULL'
                ],
                $sql
            );

            // Remover parâmetros de data do array de bind (já foram inseridos diretamente no SQL)
            unset($bindParams['p_dtanascfund'], $bindParams['p_dtapedidoafv']);

            // Usar PDO diretamente para contornar problemas de configuração do Laravel
            $pdo = null;
            try {
                $pdo = $this->getOraclePdo();
                $pdo->beginTransaction();

                $stmt = $pdo->prepare($sql);
                $stmt->execute($bindParams);

                $pdo->commit();

                Log::info('SiteMercado: Transação commitada com sucesso', [
                    'nropedidoafv' => $data['nropedidoafv']
                ]);

            } catch (\PDOException $e) {
                if ($pdo) {
                    $pdo->rollback();
                }
                throw new Exception('Erro PDO Oracle: ' . $e->getMessage());
            }

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

            // Executar a procedure Oracle via PDO direto
            $sql = "BEGIN
                consinco.sp_insereItensSitemercado(
                    p_seqedipedvenda   => :p_seqedipedvenda,
                    p_seqpedvendaitem  => :p_seqpedvendaitem,
                    p_codacesso        => :p_codacesso,
                    p_seqproduto       => :p_seqproduto,
                    p_qtdpedida        => :p_qtdpedida,
                    p_qtdembalagem     => :p_qtdembalagem,
                    p_vlrembtabpreco   => :p_vlrembtabpreco,
                    p_vlrembinformado  => :p_vlrembinformado
                );
            END;";

            $pdo = null;
            try {
                $pdo = $this->getOraclePdo();
                $pdo->beginTransaction();

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $pdo->commit();
            } catch (\PDOException $e) {
                if ($pdo) {
                    $pdo->rollback();
                }
                throw new Exception('Erro PDO Oracle: ' . $e->getMessage());
            }

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

    /**
     * Insere múltiplos itens de um pedido no ERP em uma única transação
     *
     * @group Site Mercado
     * @bodyParam nropedidoafv string required Número do pedido AFV. Example: 5389
     * @bodyParam itens array required Array de itens do pedido.
     * @bodyParam itens.*.seqpedvendaitem integer required Sequência do item. Example: 1
     * @bodyParam itens.*.codacesso string required Código de acesso. Example: 28820
     * @bodyParam itens.*.seqproduto integer required Sequência do produto. Example: 28820
     * @bodyParam itens.*.qtdpedida number required Quantidade pedida. Example: 2
     * @bodyParam itens.*.qtdembalagem number required Quantidade da embalagem. Example: 1
     * @bodyParam itens.*.vlrembtabpreco number required Preço unitário. Example: 6.99
     * @bodyParam itens.*.vlrembinformado number required Preço informado. Example: 6.99
     * @response 201 {"success": true, "message": "48 itens inseridos com sucesso", "data": {"nropedidoafv": "5389", "total_itens": 48, "inseridos": 48, "erros": 0}}
     */
    public function insereItensBatch(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nropedidoafv' => 'required|string|max:20',
                'itens' => 'required|array|min:1',
                'itens.*.seqpedvendaitem' => 'required|integer|min:1',
                'itens.*.codacesso' => 'required|string|max:50',
                'itens.*.seqproduto' => 'required|integer|min:1',
                'itens.*.qtdpedida' => 'required|numeric|min:0.01',
                'itens.*.qtdembalagem' => 'required|numeric|min:0.01',
                'itens.*.vlrembtabpreco' => 'required|numeric|min:0',
                'itens.*.vlrembinformado' => 'required|numeric|min:0',
            ]);

            $nropedidoafv = $request->nropedidoafv;
            $itens = $request->itens;

            Log::info('SiteMercado: Inserção batch de itens', [
                'nropedidoafv' => $nropedidoafv,
                'total_itens' => count($itens)
            ]);

            $sql = "BEGIN
                consinco.sp_insereItensSitemercado(
                    p_seqedipedvenda   => :p_seqedipedvenda,
                    p_seqpedvendaitem  => :p_seqpedvendaitem,
                    p_codacesso        => :p_codacesso,
                    p_seqproduto       => :p_seqproduto,
                    p_qtdpedida        => :p_qtdpedida,
                    p_qtdembalagem     => :p_qtdembalagem,
                    p_vlrembtabpreco   => :p_vlrembtabpreco,
                    p_vlrembinformado  => :p_vlrembinformado
                );
            END;";

            $pdo = null;
            $inseridos = 0;
            $erros = [];

            try {
                $pdo = $this->getOraclePdo();
                $pdo->beginTransaction();

                $stmt = $pdo->prepare($sql);

                foreach ($itens as $item) {
                    try {
                        $stmt->execute([
                            'p_seqedipedvenda' => $nropedidoafv,
                            'p_seqpedvendaitem' => $item['seqpedvendaitem'],
                            'p_codacesso' => $item['codacesso'],
                            'p_seqproduto' => $item['seqproduto'],
                            'p_qtdpedida' => $item['qtdpedida'],
                            'p_qtdembalagem' => $item['qtdembalagem'],
                            'p_vlrembtabpreco' => $item['vlrembtabpreco'],
                            'p_vlrembinformado' => $item['vlrembinformado'],
                        ]);
                        $inseridos++;
                    } catch (\PDOException $e) {
                        $erros[] = [
                            'seqpedvendaitem' => $item['seqpedvendaitem'],
                            'seqproduto' => $item['seqproduto'],
                            'error' => $e->getMessage()
                        ];
                        Log::warning('SiteMercado: Erro ao inserir item no batch', [
                            'nropedidoafv' => $nropedidoafv,
                            'seqpedvendaitem' => $item['seqpedvendaitem'],
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Commit único para todos os itens
                $pdo->commit();

                Log::info('SiteMercado: Batch de itens finalizado', [
                    'nropedidoafv' => $nropedidoafv,
                    'inseridos' => $inseridos,
                    'erros' => count($erros)
                ]);

            } catch (\PDOException $e) {
                if ($pdo) {
                    $pdo->rollback();
                }
                throw new Exception('Erro PDO Oracle no batch: ' . $e->getMessage());
            }

            $message = "{$inseridos} itens inseridos com sucesso no ERP";
            if (!empty($erros)) {
                $message .= " ({$erros} com erro)";
            }

            return $this->success([
                'nropedidoafv' => $nropedidoafv,
                'total_itens' => count($itens),
                'inseridos' => $inseridos,
                'erros' => count($erros),
                'detalhes_erros' => $erros,
            ], $message, 201);

        } catch (Exception $e) {
            Log::error('SiteMercado: Erro no batch de itens', [
                'nropedidoafv' => $request->nropedidoafv ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverError('Erro ao inserir itens no ERP: ' . $e->getMessage());
        }
    }

    /**
     * Consulta o número do pedido de venda gerado pelo ERP a partir do nropedidoafv
     *
     * @group Site Mercado
     * @urlParam nropedidoafv string required Número do pedido AFV. Example: 5319
     * @response 200 {"success": true, "data": {"nropedidoafv": "5319", "nropedvenda": 234234, "seqedipedvenda": 12345}}
     * @response 404 {"success": false, "message": "Pedido não encontrado no ERP"}
     */
    public function consultaPedido(string $nropedidoafv): JsonResponse
    {
        try {
            $pdo = $this->getOraclePdo();

            // Buscar na tabela edi_pedvenda o nropedvenda gerado pelo ERP
            $sql = "SELECT ep.nropedidoafv, ep.seqedipedvenda, ep.nropedvenda
                    FROM consinco.edi_pedvenda ep
                    WHERE ep.nropedidoafv = :nropedidoafv";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['nropedidoafv' => $nropedidoafv]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$result) {
                return $this->notFound('Pedido não encontrado no ERP');
            }

            return $this->success([
                'nropedidoafv' => $result['NROPEDIDOAFV'],
                'nropedvenda' => (int) $result['NROPEDVENDA'],
                'seqedipedvenda' => (int) $result['SEQEDIPEDVENDA'],
            ], 'Pedido encontrado');

        } catch (Exception $e) {
            Log::error('SiteMercado: Erro ao consultar pedido', [
                'nropedidoafv' => $nropedidoafv,
                'error' => $e->getMessage()
            ]);

            return $this->serverError('Erro ao consultar pedido no ERP: ' . $e->getMessage());
        }
    }

    /**
     * Consulta categorias de produtos pelo codigo_interno
     *
     * @group Site Mercado
     * @bodyParam codigos array required Lista de codigos internos. Example: [25973, 28820, 950]
     * @bodyParam id_loja integer required ID da loja. Example: 2
     * @response 200 {"success": true, "data": {"25973": {"departamento": "CONSERVAS", "categoria": "CONDIMENTOS", "subcategoria": "CALDOS"}}}
     */
    public function consultaCategorias(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $request->validate([
                'codigos' => 'required|array|min:1',
                'codigos.*' => 'required|integer',
                'id_loja' => 'required|integer',
            ]);

            $codigos = $request->codigos;
            $idLoja = $request->id_loja;

            $placeholders = implode(',', array_fill(0, count($codigos), '?'));

            $sql = "SELECT yp.codigo_interno,
                c1.DESCRICAO as subcategoria,
                c2.DESCRICAO as categoria,
                c3.DESCRICAO as departamento
            FROM consinco.YANDEH_PRODUTO yp
            LEFT JOIN consinco.YANDEH_CATEGORIA c1 ON c1.ID = yp.categorias AND c1.ID_LOJA = yp.id_loja
            LEFT JOIN consinco.YANDEH_CATEGORIA c2 ON c2.ID = c1.ID_CATEGORIA_PAI AND c2.ID_LOJA = yp.id_loja
            LEFT JOIN consinco.YANDEH_CATEGORIA c3 ON c3.ID = c2.ID_CATEGORIA_PAI AND c3.ID_LOJA = yp.id_loja
            WHERE yp.codigo_interno IN ($placeholders)
            AND yp.id_loja = ?";

            $bindings = array_merge($codigos, [$idLoja]);

            $pdo = $this->getOraclePdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($bindings);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $result = [];
            foreach ($rows as $row) {
                $result[$row['CODIGO_INTERNO']] = [
                    'departamento' => $row['DEPARTAMENTO'] ?? 'OUTROS',
                    'categoria' => $row['CATEGORIA'] ?? null,
                    'subcategoria' => $row['SUBCATEGORIA'] ?? null,
                ];
            }

            return $this->success($result, count($result) . ' produtos com categoria encontrados');

        } catch (Exception $e) {
            Log::error('SiteMercado: Erro ao consultar categorias', [
                'error' => $e->getMessage()
            ]);

            return $this->serverError('Erro ao consultar categorias: ' . $e->getMessage());
        }
    }
}