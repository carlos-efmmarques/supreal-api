# Exemplos de Uso - Site Mercado APIs

## 1. Inserir Pedido

### Endpoint
`POST /api/v1/site-mercado/pedidos`

### Headers
```
Authorization: Bearer SEU_TOKEN_AQUI
Content-Type: application/json
Accept: application/json
```

### Exemplo de Payload
```json
{
  "nropedidoafv": "PED123456789",
  "nroempresa": 1,
  "nrocgccpf": "12345678901",
  "digcgccpf": "23",
  "nomerazao": "João da Silva Santos",
  "fantasia": "João Comércio",
  "fisicajuridica": "F",
  "sexo": "M",
  "cidade": "São Paulo",
  "uf": "SP",
  "bairro": "Centro",
  "logradouro": "Rua das Flores",
  "nrologradouro": "123",
  "cmpltologradouro": "Apto 101",
  "cep": "01234567",
  "foneddd1": "11",
  "fonenro1": "987654321",
  "foneddd2": "11",
  "fonenro2": "123456789",
  "inscricaorg": "123456789",
  "dtanascfund": "1990-01-15",
  "email": "joao.silva@email.com",
  "emailnfe": "joao.nfe@email.com",
  "indentregaretira": "E",
  "dtapedidoafv": "2025-01-14",
  "vlrtotfrete": 15.50,
  "valor": 250.75,
  "nroformapagto": 1,
  "usuinclusao": "API_SITEMERCADO",
  "nroparcelas": 1,
  "codoperadoracartao": 1,
  "nrocartao": "****1234"
}
```

### Resposta de Sucesso
```json
{
  "success": true,
  "message": "Pedido inserido com sucesso no ERP",
  "data": {
    "nropedidoafv": "PED123456789"
  }
}
```

---

## 2. Inserir Itens do Pedido

### Endpoint
`POST /api/v1/site-mercado/itens`

### Headers
```
Authorization: Bearer SEU_TOKEN_AQUI
Content-Type: application/json
Accept: application/json
```

### Exemplo de Payload (Item 1)
```json
{
  "nropedidoafv": "PED123456789",
  "seqpedvendaitem": 1,
  "codacesso": "PROD001",
  "seqproduto": 12345,
  "qtdpedida": 2.0,
  "qtdembalagem": 1.0,
  "vlrembtabpreco": 125.50,
  "vlrembinformado": 125.50
}
```

### Exemplo de Payload (Item 2)
```json
{
  "nropedidoafv": "PED123456789",
  "seqpedvendaitem": 2,
  "codacesso": "PROD002",
  "seqproduto": 67890,
  "qtdpedida": 1.5,
  "qtdembalagem": 1.0,
  "vlrembtabpreco": 99.75,
  "vlrembinformado": 99.75
}
```

### Resposta de Sucesso
```json
{
  "success": true,
  "message": "Item inserido com sucesso no ERP",
  "data": {
    "nropedidoafv": "PED123456789",
    "seqpedvendaitem": 1
  }
}
```

---

## 3. Fluxo Completo de Inserção

### Passo 1: Inserir o Pedido
Use o endpoint `/api/v1/site-mercado/pedidos` com os dados do cliente e pedido.

### Passo 2: Inserir os Itens
Para cada item do pedido, faça uma chamada para `/api/v1/site-mercado/itens` com:
- Mesmo `nropedidoafv` do pedido
- `seqpedvendaitem` incremental (1, 2, 3...)
- Dados específicos de cada produto

---

## 4. Campos Obrigatórios

### Para Pedidos:
- `nropedidoafv` - Identificador único do pedido
- `nroempresa` - Código da empresa
- `nrocgccpf`, `digcgccpf` - CPF/CNPJ do cliente
- `nomerazao` - Nome/Razão social
- `fisicajuridica` - F (Física) ou J (Jurídica)
- `cidade`, `uf`, `bairro`, `logradouro`, `nrologradouro`, `cep` - Endereço completo
- `email` - Email do cliente
- `indentregaretira` - E (Entrega) ou R (Retirada)
- `dtapedidoafv` - Data do pedido
- `valor` - Valor total do pedido
- `nroformapagto` - Forma de pagamento
- `usuinclusao` - Usuário que está inserindo
- `nroparcelas` - Número de parcelas

### Para Itens:
- `nropedidoafv` - Mesmo ID do pedido
- `seqpedvendaitem` - Sequência do item
- `codacesso` - Código do produto
- `seqproduto` - ID do produto no ERP
- `qtdpedida` - Quantidade pedida
- `qtdembalagem` - Quantidade por embalagem
- `vlrembtabpreco` - Valor da embalagem (tabela)
- `vlrembinformado` - Valor da embalagem (informado)

---

## 5. Observações Importantes

- **Ordem das chamadas**: Sempre inserir o pedido ANTES dos itens
- **IDs únicos**: O `nropedidoafv` deve ser único no sistema
- **Procedures Oracle**: As APIs chamam diretamente as procedures `sp_inserePedidoSitemercado` e `sp_insereItensSitemercado`
- **Logs**: Todas as operações são logadas para auditoria
- **Validações**: Campos obrigatórios são validados antes do envio ao Oracle
- **Transações**: As procedures têm controle próprio de transação com COMMIT

---

## 6. Códigos de Erro Comuns

- **401**: Token de autenticação inválido ou não fornecido
- **422**: Erro de validação - campos obrigatórios ausentes ou inválidos
- **500**: Erro interno - falha na comunicação com Oracle ou procedure