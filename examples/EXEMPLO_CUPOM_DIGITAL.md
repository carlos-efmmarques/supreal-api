# Cupom Digital - Consulta de Cupom Fiscal

## 1. Consultar Cupom

### Endpoint
`GET /api/v1/cupom`

### Headers
```
Authorization: Bearer SEU_TOKEN_AQUI
Accept: application/json
```

### Query Parameters

| Parâmetro     | Tipo    | Obrigatório | Descrição                          | Exemplo  |
|---------------|---------|-------------|------------------------------------|----------|
| `nroempresa`  | integer | Sim         | Número da empresa                  | 2        |
| `nrocheckout` | integer | Sim         | Número do checkout (caixa)         | 11       |
| `coo`         | integer | Sim         | Contador de Ordem de Operação      | 377249   |

### Exemplo de Requisição

```bash
curl -X GET "https://api.supreal.com.br/api/v1/cupom?nroempresa=2&nrocheckout=11&coo=377249" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Accept: application/json"
```

### Resposta de Sucesso (200)
```json
{
  "success": true,
  "message": "Cupom recuperado com sucesso",
  "data": {
    "seqdocto": 1120147,
    "nroempresa": 2,
    "nrocheckout": 11,
    "coo": 377249,
    "itens": [
      {
        "DESCRICAO": "ARROZ BRANCO TIPO 1 5KG",
        "SEQITEM": 1,
        "DTAHOREMISSAO": "2025-06-15 10:32:00",
        "SEQPRODUTO": 45678,
        "CODACESSO": "7891234567890",
        "QUANTIDADE": 2,
        "VLRUNITARIO": 24.90,
        "VLRDESCONTO": 0,
        "VLRTOTAL": 49.80,
        "NROTRIBUTACAO": 1,
        "STATUS": "A",
        "PROMOCAO": null,
        "INSERCAO": 1
      },
      {
        "DESCRICAO": "LEITE INTEGRAL 1L",
        "SEQITEM": 2,
        "DTAHOREMISSAO": "2025-06-15 10:32:15",
        "SEQPRODUTO": 12345,
        "CODACESSO": "7890987654321",
        "QUANTIDADE": 6,
        "VLRUNITARIO": 5.49,
        "VLRDESCONTO": 3.00,
        "VLRTOTAL": 29.94,
        "NROTRIBUTACAO": 1,
        "STATUS": "A",
        "PROMOCAO": "LEVE 6 PAGUE 5",
        "INSERCAO": 2
      }
    ],
    "cliente": {
      "CNPJCPF": "12345678901",
      "SEQPESSOA": 98765
    }
  }
}
```

### Resposta Sem Cliente Associado

Quando o cupom não possui cliente identificado, o campo `cliente` retorna `null`:

```json
{
  "success": true,
  "message": "Cupom recuperado com sucesso",
  "data": {
    "seqdocto": 1120147,
    "nroempresa": 2,
    "nrocheckout": 11,
    "coo": 377249,
    "itens": [ "..." ],
    "cliente": null
  }
}
```

---

## 2. Como Funciona Internamente

O endpoint executa 3 consultas sequenciais no Oracle:

### Passo 1 - Buscar o SEQDOCTO
Com os parâmetros `nroempresa`, `nrocheckout` e `coo`, localiza o documento na tabela `consincomonitor.TB_DOCTO`:

```sql
SELECT seqdocto
FROM consincomonitor.TB_DOCTO
WHERE NROEMPRESA = :nroempresa
  AND NROCHECKOUT = :nrocheckout
  AND COO = :coo;
```

### Passo 2 - Buscar os Itens do Cupom
Com o `seqdocto`, recupera todos os itens registrados no cupom com a descrição do produto (via JOIN com `yandeh_produto`):

```sql
SELECT  yp.DESCRICAO,
        td.SEQITEM, td.DTAHOREMISSAO, td.SEQPRODUTO,
        td.CODACESSO, td.QUANTIDADE, td.VLRUNITARIO,
        td.VLRDESCONTO, td.VLRTOTAL, td.NROTRIBUTACAO,
        td.STATUS, td.PROMOCAO, td.INSERCAO
FROM consincomonitor.TB_DOCTOITEM td
JOIN yandeh_produto yp
  ON td.CODACESSO = yp.SKU AND td.NROEMPRESA = yp.ID_LOJA
WHERE td.NROEMPRESA = :nroempresa
  AND td.NROCHECKOUT = :nrocheckout
  AND td.SEQDOCTO = :seqdocto;
```

### Passo 3 - Buscar o Cliente (se houver)
Recupera CPF/CNPJ e identificador da pessoa associada ao cupom:

```sql
SELECT CNPJCPF, SEQPESSOA
FROM consincomonitor.TB_DOCTOCUPOM
WHERE NROEMPRESA = :nroempresa
  AND NROCHECKOUT = :nrocheckout
  AND SEQDOCTO = :seqdocto;
```

---

## 3. Campos Retornados nos Itens

| Campo           | Descrição                                      |
|-----------------|-------------------------------------------------|
| `DESCRICAO`     | Nome/descrição do produto (da tabela yandeh)    |
| `SEQITEM`       | Sequência do item no cupom                      |
| `DTAHOREMISSAO` | Data e hora de emissão do item                   |
| `SEQPRODUTO`    | Código sequencial do produto no ERP              |
| `CODACESSO`     | Código de barras / SKU do produto                |
| `QUANTIDADE`    | Quantidade vendida                               |
| `VLRUNITARIO`   | Valor unitário do produto                        |
| `VLRDESCONTO`   | Valor de desconto aplicado                       |
| `VLRTOTAL`      | Valor total do item (quantidade * unitário - desconto) |
| `NROTRIBUTACAO` | Código de tributação aplicada                    |
| `STATUS`        | Status do item no cupom                          |
| `PROMOCAO`      | Descrição da promoção aplicada (se houver)       |
| `INSERCAO`      | Ordem de inserção do item no caixa               |

---

## 4. Códigos de Erro

| Código | Descrição                                                        |
|--------|------------------------------------------------------------------|
| **200** | Cupom recuperado com sucesso                                    |
| **401** | Token de autenticação inválido ou não fornecido                 |
| **404** | Cupom não encontrado para os parâmetros informados              |
| **422** | Erro de validação - parâmetros obrigatórios ausentes/inválidos  |
| **500** | Erro interno - falha na comunicação com Oracle                  |

### Exemplo de Erro 404
```json
{
  "success": false,
  "message": "Cupom não encontrado para os parâmetros informados",
  "data": null
}
```

### Exemplo de Erro 422
```json
{
  "success": false,
  "message": "Erro de validação dos parâmetros do cupom",
  "data": {
    "nroempresa": ["O número da empresa é obrigatório"],
    "coo": ["O COO deve ser um número inteiro"]
  }
}
```

---

## 5. Observações Importantes

- **Método HTTP**: GET (consulta somente leitura, sem alteração de dados)
- **Autenticação**: Requer Bearer Token válido no header `Authorization`
- **Tabelas Oracle**: As consultas acessam `consincomonitor.TB_DOCTO`, `consincomonitor.TB_DOCTOITEM`, `consincomonitor.TB_DOCTOCUPOM` e `yandeh_produto`
- **Cliente opcional**: Nem todo cupom possui cliente associado - o campo `cliente` será `null` quando não identificado
- **Logs**: Todas as consultas são logadas para auditoria
- **Uso principal**: Montar um cupom fiscal digital com todos os itens e dados do cliente
