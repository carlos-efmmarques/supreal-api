# Integração Oracle com PDO no Laravel - Site Mercado API

## Visão Geral

Este documento detalha a implementação da integração com Oracle Database usando PDO diretamente no Laravel para contornar limitações do driver `oci8` com o Query Builder do Laravel. A solução foi desenvolvida para a API do Site Mercado que insere pedidos no ERP Oracle através da procedure `sp_inserePedidoSitemercado`...

## Problema Identificado

### Erro Inicial
```
"Unsupported driver [oci8]"
```

### Causa Raiz
- O Laravel não conseguia reconhecer o driver `oci8` através do sistema de conexões padrão
- O `DB::connection('oracle')` falhava mesmo com as extensões PHP corretas instaladas
- Necessidade de controle manual de transações para garantir commit dos dados

### Extensões PHP Disponíveis
```bash
$ php -m | grep -i oci
oci8
PDO_OCI
```

## Solução Implementada

### 1. Configuração Oracle no Laravel

**Arquivo:** `config/database.php`

```php
'oracle' => [
    'driver' => 'oci8',
    'tns' => env('ORACLE_TNS', 'consinco'),
    'host' => env('ORACLE_HOST', '10.36.100.101'),
    'port' => env('ORACLE_PORT', '1521'),
    'database' => env('ORACLE_DATABASE', 'consinco'),
    'service_name' => env('ORACLE_SERVICE_NAME', 'consinco'),
    'username' => env('ORACLE_USERNAME', 'consinco'),
    'password' => env('ORACLE_PASSWORD', 'consinco'),
    'charset' => env('ORACLE_CHARSET', 'AL32UTF8'),
],
```

### 2. Implementação PDO Direta

**Arquivo:** `app/Http/Controllers/Api/V1/SiteMercadoController.php`

```php
// Usar PDO diretamente para contornar problemas de configuração do Laravel
try {
    $dsn = 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=' . env('ORACLE_HOST', '10.36.100.101') . ')(PORT=' . env('ORACLE_PORT', '1521') . '))(CONNECT_DATA=(SERVICE_NAME=' . env('ORACLE_SERVICE_NAME', 'consinco') . ')))';
    $pdo = new \PDO($dsn, env('ORACLE_USERNAME', 'consinco'), env('ORACLE_PASSWORD', 'consinco'));
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false); // Desabilitar autocommit
    
    $pdo->beginTransaction(); // Iniciar transação
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($bindParams);
    
    $pdo->commit(); // Commit explícito
    
    Log::info('SiteMercado: Transação commitada com sucesso', [
        'nropedidoafv' => $data['nropedidoafv']
    ]);
    
} catch (\PDOException $e) {
    if ($pdo) {
        $pdo->rollback(); // Rollback em caso de erro
    }
    throw new Exception('Erro PDO Oracle: ' . $e->getMessage());
}
```

### 3. Construção do SQL com Parâmetros Nomeados

```php
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
```

### 4. Tratamento de Datas Oracle

```php
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
```

## Exemplo de Uso da API

### Endpoint
```
POST /api/v1/site-mercado/pedidos
```

### Headers de Autenticação
```http
Content-Type: application/json
X-Master-Key: mk_v63ElDoJR1JJ8r3Ei6yxdba4skBVYEigeKekniBNzrYmZ1XA3VK72nkn3vDf
Authorization: Bearer dev-token-37db0e7e3067c3c4fe5742287314d1ce17ed2cca
```

### Payload de Exemplo
```json
{
    "nropedidoafv": "1002",
    "nroempresa": 1,
    "nrocgccpf": "12345678901",
    "digcgccpf": "00",
    "nomerazao": "José da Silva",
    "fantasia": "José Silva",
    "fisicajuridica": "F",
    "sexo": "M",
    "cidade": "Campinas",
    "uf": "SP",
    "bairro": "Centro",
    "logradouro": "Rua das Flores",
    "nrologradouro": "123",
    "cmpltologradouro": "Apto 12",
    "cep": "13010000",
    "foneddd1": "19",
    "fonenro1": "999999999",
    "dtanascfund": "1985-03-25",
    "email": "jose.silva@email.com",
    "emailnfe": "nfe@email.com",
    "indentregaretira": "E",
    "dtapedidoafv": "2025-08-15",
    "vlrtotfrete": 15.75,
    "valor": 250.90,
    "nroparcelas": 2,
    "codoperadoracartao": 101
}
```

### Resposta de Sucesso
```json
{
    "success": true,
    "message": "Pedido inserido com sucesso no ERP",
    "data": {
        "nropedidoafv": "1002"
    }
}
```

## Comando cURL Completo

```bash
curl -X POST http://localhost:8000/api/v1/site-mercado/pedidos \
-H "Content-Type: application/json" \
-H "X-Master-Key: mk_v63ElDoJR1JJ8r3Ei6yxdba4skBVYEigeKekniBNzrYmZ1XA3VK72nkn3vDf" \
-H "Authorization: Bearer dev-token-37db0e7e3067c3c4fe5742287314d1ce17ed2cca" \
-d '{
    "nropedidoafv": "1002",
    "nroempresa": 1,
    "nrocgccpf": "12345678901",
    "digcgccpf": "00",
    "nomerazao": "José da Silva",
    "fantasia": "José Silva",
    "fisicajuridica": "F",
    "sexo": "M",
    "cidade": "Campinas",
    "uf": "SP",
    "bairro": "Centro",
    "logradouro": "Rua das Flores",
    "nrologradouro": "123",
    "cmpltologradouro": "Apto 12",
    "cep": "13010000",
    "foneddd1": "19",
    "fonenro1": "999999999",
    "dtanascfund": "1985-03-25",
    "email": "jose.silva@email.com",
    "emailnfe": "nfe@email.com",
    "indentregaretira": "E",
    "dtapedidoafv": "2025-08-15",
    "vlrtotfrete": 15.75,
    "valor": 250.90,
    "nroparcelas": 2,
    "codoperadoracartao": 101
}'
```

## Verificação no Banco de Dados

### Consultas para Verificar Inserção

```sql
-- Consulta por nome (com schema explícito)
SELECT * FROM consinco.edi_pedvendacliente WHERE nomerazao = 'José da Silva';

-- Consulta por número do pedido
SELECT * FROM consinco.edi_pedvendacliente WHERE nropedidoafv IN ('1001', '1002');

-- Consulta dos registros mais recentes
SELECT * FROM consinco.edi_pedvendacliente ORDER BY rowid DESC FETCH FIRST 10 ROWS ONLY;
```

## Componentes da Solução

### 1. Request Validation
- **Arquivo:** `app/Http/Requests/SiteMercado/InserePedidoRequest.php`
- Validação completa de todos os 32 parâmetros da procedure
- Mensagens de erro customizadas em português
- Validação de tipos, formatos e regras de negócio

### 2. Controller
- **Arquivo:** `app/Http/Controllers/Api/V1/SiteMercadoController.php`
- Método: `inserePedido(InserePedidoRequest $request)`
- Implementação PDO direta com controle de transação
- Logs detalhados para auditoria

### 3. Middleware de Autenticação
- Autenticação via Master Key (X-Master-Key header)
- Autenticação via Developer Token (Authorization Bearer header)
- Sistema de segurança em duas camadas

## Pontos Críticos da Implementação

### 1. Controle de Transação
```php
$pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, false); // CRÍTICO: Desabilitar autocommit
$pdo->beginTransaction(); // CRÍTICO: Iniciar transação explicitamente
// ... execução da procedure ...
$pdo->commit(); // CRÍTICO: Commit explícito para persistir dados
```

### 2. Tratamento de Datas
- Oracle requer formato específico com `TO_DATE()`
- Conversão de datas antes da inserção no SQL
- Remoção dos parâmetros de data do array de bind após conversão

### 3. Parâmetros da Procedure
- Total de 32 parâmetros conforme assinatura da procedure
- Alguns parâmetros opcionais tratados com valores padrão
- Parâmetros fixos: `p_nroformapagto = 1` e `p_usuinclusao = 'OMS-SUPREAL'`

## Troubleshooting

### Problema 1: "Unsupported driver [oci8]"
**Solução:** Usar PDO diretamente em vez do Query Builder do Laravel

### Problema 2: Dados não aparecem no banco após inserção
**Solução:** Implementar controle manual de transação com commit explícito

### Problema 3: Erro de formato de data
**Solução:** Usar `TO_DATE()` do Oracle com formato 'YYYY-MM-DD'

### Problema 4: Erro de parâmetros da procedure
**Solução:** Verificar se todos os 32 parâmetros estão sendo passados na ordem correta

## Logs e Monitoramento

### Logs de Sucesso
```
SiteMercado: Tentativa de inserção de pedido
SiteMercado: Transação commitada com sucesso  
SiteMercado: Pedido inserido com sucesso
```

### Logs de Erro
```
SiteMercado: Erro ao inserir pedido
- nropedidoafv
- error (mensagem do erro)
- trace (stack trace completo)
```

## Considerações de Performance

1. **Conexão PDO:** Nova conexão criada a cada requisição
2. **Transação:** Controle explícito evita locks desnecessários
3. **Prepared Statements:** Proteção contra SQL injection
4. **Logs:** Monitoramento detalhado sem impacto significativo

## Segurança

1. **Autenticação dupla:** Master Key + Developer Token
2. **Validação rigorosa:** Todos os parâmetros validados
3. **Prepared Statements:** Prevenção de SQL injection
4. **Logs de auditoria:** Rastreabilidade completa das operações

## Conclusão

Esta implementação resolve com sucesso os problemas de compatibilidade entre Laravel e Oracle, fornecendo uma solução robusta e segura para inserção de pedidos no ERP. O controle manual de transações garante a consistência dos dados, enquanto a validação rigorosa assegura a qualidade das informações inseridas.

A documentação serve como guia para futuras implementações similares e troubleshooting de problemas relacionados à integração Oracle/Laravel.
