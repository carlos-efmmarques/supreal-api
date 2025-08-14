# Supreal API - API Interna para Comunicação entre Sistemas

## 📋 Descrição

API REST interna desenvolvida em Laravel 11 para comunicação entre diversos sistemas da empresa. Implementa autenticação via Bearer Token, documentação interativa, versionamento de API e padrões de resposta consistentes.

## 🚀 Características

- **Laravel 11** - Framework PHP moderno e robusto
- **Autenticação Bearer Token** - Sistema customizado de tokens com controle de permissões
- **Documentação Automática** - Gerada com Scribe, acessível em `/docs`
- **Versionamento de API** - Estrutura preparada para múltiplas versões (`/api/v1/...`)
- **Rate Limiting** - Controle de requisições por token
- **CORS Configurado** - Para domínios internos confiáveis
- **Logs Detalhados** - Registro de todas as requisições e respostas
- **Testes Automatizados** - Suite completa de testes unitários e de integração
- **Padrão de Resposta** - Formato JSON consistente para todas as respostas

## 📦 Requisitos

- PHP >= 8.2
- Composer
- MySQL >= 5.7
- Node.js & NPM (para assets)

## 🔧 Instalação

### 1. Clone o repositório
```bash
git clone [URL_DO_REPOSITORIO] supreal-api
cd supreal-api
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados

O projeto está configurado para usar SQLite por padrão (mais simples para desenvolvimento).

Para **desenvolvimento/teste com SQLite** (recomendado):
```env
DB_CONNECTION=sqlite
```

Para **produção com MySQL**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supreal_api
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 4.1. Configure o banco Oracle (ERP)

Para as APIs do Site Mercado que se conectam ao ERP Oracle:
```env
ORACLE_HOST=10.36.100.101
ORACLE_PORT=1521
ORACLE_DATABASE=XE
ORACLE_USERNAME=seu_usuario_oracle
ORACLE_PASSWORD=sua_senha_oracle
ORACLE_CHARSET=AL32UTF8
ORACLE_PREFIX_SCHEMA=CONSINCO
```

### 5. Execute as migrations
```bash
php artisan migrate
```

### 6. (Opcional) Execute os seeders para dados de teste
```bash
php artisan db:seed
```
Isso criará tokens de teste que serão exibidos no terminal. **Guarde-os com segurança!**

### 7. Gere a documentação
```bash
php artisan scribe:generate
```

### 8. Inicie o servidor
```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

## 🔑 Autenticação

Todas as requisições para endpoints protegidos devem incluir um token Bearer no header:

```http
Authorization: Bearer SEU_TOKEN_AQUI
```

### Gerenciamento de Tokens

Os tokens podem ser gerenciados através dos endpoints em `/api/tokens`:

- **GET /api/tokens** - Lista todos os tokens
- **POST /api/tokens** - Cria um novo token
- **GET /api/tokens/{id}** - Exibe detalhes de um token
- **PUT /api/tokens/{id}** - Atualiza um token
- **DELETE /api/tokens/{id}** - Remove um token
- **POST /api/tokens/{id}/revoke** - Revoga um token
- **POST /api/tokens/{id}/activate** - Ativa um token

### Criando um Token

```bash
curl -X POST http://localhost:8000/api/tokens \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Sistema X",
    "abilities": ["*"],
    "rate_limit": 100,
    "expires_at": "2025-12-31 23:59:59"
  }'
```

### Testando a API

```bash
# Health check (público)
curl -H "Accept: application/json" http://localhost:8000/api/health

# Listar exemplos (protegido - requer token)
curl -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     -H "Accept: application/json" \
     http://localhost:8000/api/v1/examples

# Criar exemplo
curl -X POST http://localhost:8000/api/v1/examples \
     -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "name": "Novo Item",
       "description": "Descrição do item",
       "status": "active"
     }'
```

## 🏪 APIs do Site Mercado

### Inserir Pedido
```bash
curl -X POST http://localhost:8000/api/v1/site-mercado/pedidos \
     -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "nropedidoafv": "PED123456",
       "nroempresa": 1,
       "nrocgccpf": "12345678901",
       "digcgccpf": "23",
       "nomerazao": "João da Silva",
       "fisicajuridica": "F",
       "cidade": "São Paulo",
       "uf": "SP",
       "bairro": "Centro",
       "logradouro": "Rua das Flores",
       "nrologradouro": "123",
       "cep": "01234567",
       "email": "joao@email.com",
       "indentregaretira": "E",
       "dtapedidoafv": "2025-01-14",
       "valor": 150.75,
       "nroformapagto": 1,
       "usuinclusao": "API_SITEMERCADO",
       "nroparcelas": 1
     }'
```

### Inserir Itens do Pedido
```bash
curl -X POST http://localhost:8000/api/v1/site-mercado/itens \
     -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "nropedidoafv": "PED123456",
       "seqpedvendaitem": 1,
       "codacesso": "COD12345",
       "seqproduto": 12345,
       "qtdpedida": 2.5,
       "qtdembalagem": 1.0,
       "vlrembtabpreco": 15.90,
       "vlrembinformado": 15.90
     }'
```

## 📚 Documentação da API

A documentação interativa está disponível em:
- **HTML**: `http://localhost:8000/docs`
- **Postman Collection**: `http://localhost:8000/docs.postman`
- **OpenAPI Spec**: `http://localhost:8000/docs.openapi`

## 🏗️ Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── BaseController.php      # Controller base com ApiResponse trait
│   │       ├── TokenController.php     # Gerenciamento de tokens
│   │       └── V1/
│   │           ├── ExampleController.php # Controller de exemplo
│   │           └── SiteMercadoController.php # APIs para Site Mercado (Oracle ERP)
│   ├── Middleware/
│   │   ├── AuthenticateApi.php        # Middleware de autenticação
│   │   └── LogApiRequests.php         # Middleware de logging
│   └── Requests/
│       ├── CreateTokenRequest.php     # Validação para criação de token
│       ├── ExampleStoreRequest.php    # Validação de exemplo (POST)
│       ├── ExampleUpdateRequest.php   # Validação de exemplo (PUT)
│       └── SiteMercado/
│           ├── InserePedidoRequest.php  # Validação para inserção de pedidos
│           └── InsereItensRequest.php   # Validação para inserção de itens
├── Models/
│   └── ApiToken.php                   # Model de tokens da API
└── Traits/
    └── ApiResponse.php                 # Trait para respostas padronizadas
```

## 📝 Padrão de Resposta

Todas as respostas da API seguem o padrão:

### Sucesso
```json
{
    "success": true,
    "message": "Operação realizada com sucesso",
    "data": {
        // dados retornados
    }
}
```

### Erro
```json
{
    "success": false,
    "message": "Descrição do erro",
    "data": {
        // detalhes do erro (se aplicável)
    }
}
```

### Paginação
```json
{
    "success": true,
    "message": "Dados recuperados com sucesso",
    "data": {
        "items": [...],
        "pagination": {
            "total": 100,
            "per_page": 20,
            "current_page": 1,
            "last_page": 5,
            "from": 1,
            "to": 20,
            "next_page_url": "...",
            "prev_page_url": null
        }
    }
}
```

## 🆕 Adicionando Novos Endpoints

### 1. Crie o Controller

```bash
php artisan make:controller Api/V1/NovoController
```

Estenda o `BaseController` para ter acesso aos métodos de resposta:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;

class NovoController extends BaseController
{
    public function index()
    {
        return $this->success($data, 'Mensagem de sucesso');
    }
}
```

### 2. Crie o Form Request (para validação)

```bash
php artisan make:request NovoRequest
```

### 3. Adicione as rotas

Em `routes/api.php`:

```php
Route::prefix('v1')->middleware(['auth.api'])->group(function () {
    Route::apiResource('novo-recurso', NovoController::class);
});
```

### 4. Atualize a documentação

```bash
php artisan scribe:generate
```

## 🧪 Testes

### Executar todos os testes
```bash
php artisan test
```

### Executar testes específicos
```bash
php artisan test --filter=ExampleApiTest
```

### Executar com coverage
```bash
php artisan test --coverage
```

## 🔐 Segurança

### CORS
Configure os domínios permitidos em `config/cors.php`:

```php
'allowed_origins' => [
    'https://app.suaempresa.com',
    'https://admin.suaempresa.com',
],
```

### Rate Limiting
- Padrão: 60 requisições por minuto por token
- Configurável por token individualmente
- Configuração global em `bootstrap/app.php`

### Logs
- Todas as requisições são registradas em `storage/logs/`
- Dados sensíveis são automaticamente ocultados nos logs
- Canal de log configurável em `.env`

## 📊 Monitoramento

### Health Check
```bash
curl http://localhost:8000/api/health
```

### Logs de Requisições
Os logs incluem:
- Método HTTP e URL
- IP e User Agent
- Tempo de resposta
- Status code
- Tamanho da resposta
- Token utilizado (ID e nome)

## 🚀 Deploy

### Produção

1. Configure as variáveis de ambiente
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.suaempresa.com
```

2. Otimize a aplicação
```bash
composer install --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

3. Configure o servidor web (Nginx/Apache)
4. Configure SSL/TLS
5. Configure o supervisor para queues (se necessário)

## 📄 Licença

Proprietary - Todos os direitos reservados

## 👥 Suporte

Para suporte interno, entre em contato com a equipe de desenvolvimento.

---

**Desenvolvido com Laravel 11** | **Documentação atualizada em**: Janeiro 2025