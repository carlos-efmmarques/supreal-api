# Integração Oracle com PDO no Laravel - Site Mercado API

## Visão Geral

Este documento detalha a integração com Oracle Database usando PDO diretamente no Laravel para inserção de pedidos do site (supermercadosreal.delivery) no ERP Oracle através da API supreal-api. O fluxo completo envolve buscar pedidos do site, mapear os dados e enviar via API para as procedures Oracle `sp_inserePedidoSitemercado` e `sp_insereItensSitemercado`.

## Arquitetura do Fluxo

```
Site (supermercadosreal.delivery)
    │
    ▼ GET /api/pedido/{id}/v2/
OMS (supreal-oms) - Agente de Vendas Central
    │
    ▼ POST /api/v1/site-mercado/pedidos + /itens
Supreal API (supreal-api.test / Docker)
    │
    ▼ PDO direto (oci:dbname=...)
Oracle ERP (consinco)
    │
    ├── edi_pedvendacliente  (dados do pedido/cliente)
    ├── edi_pedvendaitem     (itens do pedido)
    │       ▼ (procedure interna do ERP)
    ├── mad_pedvenda         (pedido de venda gerado)
    └── mad_pedvendaitem     (itens do pedido de venda)
```

## Extensões PHP Necessárias

```bash
$ php -m | grep -i oci
oci8       # Extensão nativa Oracle
PDO_OCI    # Driver PDO para Oracle (CRÍTICO - deve estar habilitado)
```

**IMPORTANTE:** A extensão `pdo_oci` deve estar habilitada no php.ini. No Laragon (Windows), verificar em `C:/laragon/bin/php/php-X.X.X/php.ini`:
```ini
extension=pdo_oci   ; Descomentar esta linha (remover o ;)
```
Reiniciar o Apache após a alteração.

## Solução Implementada

### 1. Método Helper para Conexão PDO

**Arquivo:** `app/Http/Controllers/Api/V1/SiteMercadoController.php`

Conexão PDO centralizada em método reutilizável (ambos os endpoints usam):

```php
private function getOraclePdo(): \PDO
{
    $dsn = 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST='
        . env('ORACLE_HOST', '10.36.100.101')
        . ')(PORT=' . env('ORACLE_PORT', '1521')
        . '))(CONNECT_DATA=(SERVICE_NAME='
        . env('ORACLE_SERVICE_NAME', 'consinco') . ')))';
    $pdo = new \PDO($dsn, env('ORACLE_USERNAME', 'consinco'), env('ORACLE_PASSWORD', 'consinco'));
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
    return $pdo;
}
```

### 2. Inserção de Pedido (inserePedido)

Usa a procedure `sp_inserePedidoSitemercado` com parâmetros nomeados e tratamento de datas via `TO_DATE()`:

```php
$pdo = null;
try {
    $pdo = $this->getOraclePdo();
    $pdo->beginTransaction();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($bindParams);
    $pdo->commit();
} catch (\PDOException $e) {
    if ($pdo) { $pdo->rollback(); }
    throw new Exception('Erro PDO Oracle: ' . $e->getMessage());
}
```

### 3. Inserção de Itens (insereItens)

Usa a procedure `consinco.sp_insereItensSitemercado` com o mesmo padrão PDO direto:

```php
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
```

**IMPORTANTE:** O método `insereItens` originalmente usava `DB::connection('oracle')` que falhava com "Unsupported driver [oci8]". Foi migrado para PDO direto (mesmo padrão do `inserePedido`).

## Mapeamento de Dados: Site → API

### Pedido (POST /api/v1/site-mercado/pedidos)

| Campo API | Origem (site order) | Notas |
|---|---|---|
| `nropedidoafv` | `order['id']` (string) | ID do pedido no site |
| `nroempresa` | Número da loja destino | Ex: 3 = Noronha, 4 = outra loja |
| `nrocgccpf` | `order['user']['cpf']` | Somente dígitos, SEM os 2 últimos |
| `digcgccpf` | `order['user']['cpf']` | Últimos 2 dígitos |
| `nomerazao` | `order['user']['name']` | Nome completo |
| `fantasia` | `order['user']['name']` | **MAX 30 caracteres** - truncar! |
| `fisicajuridica` | `"F"` | F=Física (padrão site) |
| `sexo` | `order['user']['sexo']` | "F" ou "M" (primeira letra maiúscula) |
| `cidade` | `order['endereco_entrega']['cidade']` | Sem acentos recomendado |
| `uf` | Derivado da cidade | "RJ" para Niterói |
| `bairro` | `order['endereco_entrega']['bairro']` | |
| `logradouro` | `order['endereco_entrega']['rua']` | |
| `nrologradouro` | `order['endereco_entrega']['numero']` | |
| `cmpltologradouro` | `order['endereco_entrega']['complemento']` | |
| `cep` | `order['endereco_entrega']['cep']` | Somente dígitos, sem hífen |
| `foneddd1` | `order['user']['telefone']` | Primeiros 2 dígitos |
| `fonenro1` | `order['user']['telefone']` | Restante sem DDD |
| `email` | `order['user']['email']` | |
| `emailnfe` | `order['user']['email']` | Mesmo que email |
| `indentregaretira` | `"E"` | E=Entrega |
| `dtapedidoafv` | `order['data_agendamento']` | Data de agendamento (YYYY-MM-DD) |
| `vlrtotfrete` | `order['valor_frete']` | |
| `valor` | `order['valor_total_final']` | Valor total com frete |
| `nroformapagto` | `1` | Fixo na procedure |
| `usuinclusao` | `"OMS-SUPREAL"` | Fixo na procedure |
| `nroparcelas` | `1` | |
| `codoperadoracartao` | `1` | |

### Itens (POST /api/v1/site-mercado/itens)

| Campo API | Origem (site order item) | Notas |
|---|---|---|
| `nropedidoafv` | Mesmo ID do pedido | |
| `seqpedvendaitem` | Índice do item (1, 2, 3...) | Sequencial começando em 1 |
| `codacesso` | `item['codigo']` | Código do produto no site |
| `seqproduto` | `item['codigo']` (int) | Mesmo código como inteiro |
| `qtdpedida` | `item['quantidade']` | Pode ser decimal (ex: 0.5 para 500g) |
| `qtdembalagem` | `1` | Fixo |
| `vlrembtabpreco` | `item['subtotal']` | Valor com desconto de fração |
| `vlrembinformado` | `item['subtotal']` | Mesmo que tabela |

### Exemplo: Tratamento do CPF

CPF do site: `"145.847.877-78"` ou `"010.238.807-50"`

```
Limpar: "14584787778"
nrocgccpf: "145847877"    (todos menos últimos 2)
digcgccpf: "78"           (últimos 2 dígitos)
```

### Exemplo: Tratamento do Telefone

Telefone do site: `"21997590470"`

```
foneddd1: "21"         (primeiros 2 dígitos)
fonenro1: "997590470"  (restante)
```

## Endpoints da API

### Autenticação

```http
X-Master-Key: {master_key}
Authorization: Bearer {token}
```

Para gerar um novo token:
```bash
curl -sk https://supreal-api.test/api/tokens \
  -X POST \
  -H "Content-Type: application/json" \
  -H "X-Master-Key: {master_key}" \
  -d '{"name": "Token OMS", "abilities": ["*"], "rate_limit": 1000}'
```

### Inserir Pedido

```bash
curl -sk https://supreal-api.test/api/v1/site-mercado/pedidos \
  -X POST \
  -H "Content-Type: application/json" \
  -H "X-Master-Key: {master_key}" \
  -H "Authorization: Bearer {token}" \
  -d @pedido.json
```

### Inserir Item

```bash
curl -sk https://supreal-api.test/api/v1/site-mercado/itens \
  -X POST \
  -H "Content-Type: application/json" \
  -H "X-Master-Key: {master_key}" \
  -H "Authorization: Bearer {token}" \
  -d @item.json
```

## Verificação no Banco de Dados

### Consultas para Verificar Inserção

```sql
-- Verificar pedido na tabela EDI (dados brutos recebidos pela API)
SELECT * FROM consinco.edi_pedvendacliente WHERE nropedidoafv = '5369';

-- Verificar itens na tabela EDI
SELECT * FROM consinco.edi_pedvendaitem ei WHERE ei.seqedipedvenda = '{seqedipedvenda}';

-- Verificar pedido de venda gerado pelo ERP (após processamento EDI)
SELECT * FROM consinco.mad_pedvenda mp WHERE mp.nropedvenda = {nropedvenda};

-- Verificar itens do pedido de venda
SELECT * FROM consinco.mad_pedvendaitem mp WHERE mp.nropedvenda = {nropedvenda};

-- Encontrar o nropedvenda a partir do nropedidoafv
SELECT * FROM consinco.edi_pedvenda ep WHERE ep.nropedidoafv = '5369';
```

### Diagnóstico: Itens na EDI que Não Foram para o Pedido de Venda

```sql
-- Itens inseridos via API que o ERP descartou ao processar
SELECT ei.seqpedvendaitem, ei.seqproduto, ei.codacesso
FROM consinco.edi_pedvendaitem ei
WHERE ei.seqedipedvenda = '{seqedipedvenda}'
AND ei.seqproduto NOT IN (
    SELECT mp.seqproduto FROM consinco.mad_pedvendaitem mp WHERE mp.nropedvenda = {nropedvenda}
);

-- Verificar se produtos existem no cadastro
SELECT p.seqproduto, p.descricao
FROM consinco.map_produto p
WHERE p.seqproduto IN ({lista_de_codigos});
```

## Problemas Conhecidos e Soluções

### 1. "Unsupported driver [oci8]"
**Causa:** `DB::connection('oracle')` não funciona sem o pacote `yajra/laravel-oci8` corretamente configurado.
**Solução:** Usar PDO direto via `getOraclePdo()` em vez do Query Builder do Laravel.

### 2. "could not find driver"
**Causa:** Extensão `pdo_oci` não habilitada no PHP do servidor web.
**Solução:** Descomentar `extension=pdo_oci` no php.ini e reiniciar o servidor.

### 3. "Undefined variable $pdo" no catch
**Causa:** Se a conexão PDO falhar na criação, a variável `$pdo` não existe no bloco catch.
**Solução:** Inicializar `$pdo = null;` antes do bloco try.

### 4. Dados não aparecem no banco após inserção
**Causa:** Oracle requer commit explícito.
**Solução:** `$pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false)` + `$pdo->beginTransaction()` + `$pdo->commit()`.

### 5. Campo FANTASIA excede limite
**Causa:** Coluna `FANTASIA` na tabela `EDI_PEDVENDACLIENTE` tem máximo de 30 caracteres.
**Solução:** Truncar o campo `fantasia` para 30 caracteres antes de enviar: `substr($nome, 0, 30)`.

### 6. Itens inseridos na EDI mas não aparecem no pedido de venda
**Causa:** O ERP processa os itens da `edi_pedvendaitem` para `mad_pedvendaitem` e pode descartar itens cujo `seqproduto` não corresponde a um produto válido/ativo no cadastro daquela empresa, ou por regras internas de segmento/tabela de preço.
**Diagnóstico:** Comparar contagem de itens entre `edi_pedvendaitem` e `mad_pedvendaitem`. Os itens descartados foram inseridos com sucesso na EDI (API retorna sucesso) mas o ERP os filtra silenciosamente durante o processamento.
**Nota:** Este é um comportamento do ERP, não da API. A API insere corretamente em `edi_pedvendaitem`.

### 7. Segmento do pedido
**Causa:** O segmento do pedido de venda é definido internamente pela procedure `sp_inserePedidoSitemercado`, não é um parâmetro da API.
**Solução:** Alterar diretamente na procedure Oracle no banco consinco.

## Testes Realizados (2026-03-13)

### Pedido 5371 (7 itens - pedido simples)
- **Cliente:** Ana Carolina Conceição Neves (CPF 145.847.877-78)
- **Empresa:** 4 (Niterói)
- **Resultado:** Pedido + 7 itens inseridos com sucesso
- **Valor:** R$ 56,93

### Pedido 5369 (58 itens - pedido grande)
- **Cliente:** Teresa Cristina Lopes de Amorim (CPF 010.238.807-50)
- **Empresa:** 3 (Noronha)
- **Resultado API:** 58/58 itens inseridos com sucesso na EDI
- **Resultado ERP:** 45/58 itens no pedido de venda (13 descartados pelo ERP)
- **Valor:** R$ 678,16
- **Problema encontrado:** Campo `fantasia` excedia 30 caracteres (31 chars no nome) - resolvido com truncamento
- **Investigação:** Itens descartados existem na `edi_pedvendaitem` e na `map_produto`, mas o ERP não os transferiu para `mad_pedvendaitem` - comportamento interno do processamento EDI

## Docker

A API é containerizada com Docker para rodar internamente. Estrutura:

```
supreal-api/
├── Dockerfile                    # PHP 8.2-FPM + Oracle (oci8 + pdo_oci)
├── .gitlab-ci.yml                # Pipeline: build → deploy
├── docker/
│   ├── oracle/                   # Instant Client 21 (basic + sdk + oci8.tgz)
│   ├── nginx/                    # Configuração nginx
│   ├── supervisor/               # PHP-FPM + Nginx + Queue worker
│   ├── start.sh                  # Script de inicialização
│   └── docker-compose.production.yml
```

**CRÍTICO para Docker:** O Dockerfile instala tanto `oci8` quanto `pdo_oci`:
```dockerfile
# OCI8 (via pecl)
RUN echo "instantclient,$ORACLE_HOME" | pecl install /tmp/oracle/oci8-3.3.0.tgz

# PDO_OCI (via docker-php-ext)
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,$ORACLE_HOME \
    && docker-php-ext-install pdo_oci
```

## Configuração Oracle no Laravel

**Arquivo:** `config/database.php`

```php
'oracle' => [
    'driver' => 'oci8',
    'host' => env('ORACLE_HOST', '10.36.100.101'),
    'port' => env('ORACLE_PORT', '1521'),
    'database' => env('ORACLE_DATABASE', 'consinco'),
    'service_name' => env('ORACLE_SERVICE_NAME', 'consinco'),
    'username' => env('ORACLE_USERNAME', 'consinco'),
    'password' => env('ORACLE_PASSWORD', 'consinco'),
    'charset' => env('ORACLE_CHARSET', 'AL32UTF8'),
    'prefix_schema' => env('ORACLE_PREFIX_SCHEMA', 'consinco'),
],
```

## Variáveis de Ambiente (Oracle)

```env
ORACLE_HOST=10.36.100.101
ORACLE_PORT=1521
ORACLE_DATABASE=consinco
ORACLE_SERVICE_NAME=consinco
ORACLE_USERNAME=consinco
ORACLE_PASSWORD=consinco
ORACLE_CHARSET=AL32UTF8
ORACLE_PREFIX_SCHEMA=consinco
```
