<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteMercadoApiTest extends TestCase
{
    use RefreshDatabase;

    protected $apiToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar um token de teste
        $this->apiToken = ApiToken::create([
            'name' => 'Site Mercado Test Token',
            'token' => hash('sha256', 'site-mercado-test-token'),
            'abilities' => ['*'],
            'is_active' => true,
            'rate_limit' => 100
        ]);
    }

    public function test_insere_pedido_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/site-mercado/pedidos', []);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token de autenticação não fornecido'
            ]);
    }

    public function test_insere_pedido_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer site-mercado-test-token'
        ])->postJson('/api/v1/site-mercado/pedidos', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Erro de validação dos dados do pedido'
            ])
            ->assertJsonValidationErrors([
                'nropedidoafv', 'nroempresa', 'nrocgccpf', 'digcgccpf',
                'nomerazao', 'fisicajuridica', 'cidade', 'uf', 'bairro',
                'logradouro', 'nrologradouro', 'cep', 'email',
                'indentregaretira', 'dtapedidoafv', 'valor',
                'nroformapagto', 'usuinclusao', 'nroparcelas'
            ]);
    }

    public function test_insere_pedido_with_valid_data_structure(): void
    {
        // Mock da conexão Oracle para evitar erro de conexão real
        DB::shouldReceive('connection')
            ->with('oracle')
            ->andReturnSelf();
        
        DB::shouldReceive('statement')
            ->once()
            ->andReturn(true);

        $validData = [
            'nropedidoafv' => 'PED123456',
            'nroempresa' => 1,
            'nrocgccpf' => '12345678901',
            'digcgccpf' => '23',
            'nomerazao' => 'João da Silva',
            'fantasia' => 'João da Silva',
            'fisicajuridica' => 'F',
            'sexo' => 'M',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
            'bairro' => 'Centro',
            'logradouro' => 'Rua das Flores',
            'nrologradouro' => '123',
            'cmpltologradouro' => 'Apto 101',
            'cep' => '01234567',
            'foneddd1' => '11',
            'fonenro1' => '987654321',
            'email' => 'joao@email.com',
            'emailnfe' => 'joao@email.com',
            'indentregaretira' => 'E',
            'dtapedidoafv' => '2025-01-14',
            'vlrtotfrete' => 15.50,
            'valor' => 150.75,
            'nroformapagto' => 1,
            'usuinclusao' => 'API_SITEMERCADO',
            'nroparcelas' => 1,
            'codoperadoracartao' => 1,
            'nrocartao' => '****1234'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer site-mercado-test-token'
        ])->postJson('/api/v1/site-mercado/pedidos', $validData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Pedido inserido com sucesso no ERP',
                'data' => [
                    'nropedidoafv' => 'PED123456'
                ]
            ]);
    }

    public function test_insere_itens_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/site-mercado/itens', []);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token de autenticação não fornecido'
            ]);
    }

    public function test_insere_itens_validates_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer site-mercado-test-token'
        ])->postJson('/api/v1/site-mercado/itens', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Erro de validação dos dados do item'
            ])
            ->assertJsonValidationErrors([
                'nropedidoafv', 'seqpedvendaitem', 'codacesso', 'seqproduto',
                'qtdpedida', 'qtdembalagem', 'vlrembtabpreco', 'vlrembinformado'
            ]);
    }

    public function test_insere_itens_with_valid_data_structure(): void
    {
        // Mock da conexão Oracle para evitar erro de conexão real
        DB::shouldReceive('connection')
            ->with('oracle')
            ->andReturnSelf();
        
        DB::shouldReceive('statement')
            ->once()
            ->andReturn(true);

        $validData = [
            'nropedidoafv' => 'PED123456',
            'seqpedvendaitem' => 1,
            'codacesso' => 'COD12345',
            'seqproduto' => 12345,
            'qtdpedida' => 2.5,
            'qtdembalagem' => 1.0,
            'vlrembtabpreco' => 15.90,
            'vlrembinformado' => 15.90
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer site-mercado-test-token'
        ])->postJson('/api/v1/site-mercado/itens', $validData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Item inserido com sucesso no ERP',
                'data' => [
                    'nropedidoafv' => 'PED123456',
                    'seqpedvendaitem' => 1
                ]
            ]);
    }

    public function test_insere_pedido_validates_email_format(): void
    {
        $invalidData = [
            'nropedidoafv' => 'PED123456',
            'nroempresa' => 1,
            'nrocgccpf' => '12345678901',
            'digcgccpf' => '23',
            'nomerazao' => 'João da Silva',
            'fisicajuridica' => 'F',
            'cidade' => 'São Paulo',
            'uf' => 'SP',
            'bairro' => 'Centro',
            'logradouro' => 'Rua das Flores',
            'nrologradouro' => '123',
            'cep' => '01234567',
            'email' => 'email-invalido', // Email inválido
            'indentregaretira' => 'E',
            'dtapedidoafv' => '2025-01-14',
            'valor' => 150.75,
            'nroformapagto' => 1,
            'usuinclusao' => 'API_SITEMERCADO',
            'nroparcelas' => 1
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer site-mercado-test-token'
        ])->postJson('/api/v1/site-mercado/pedidos', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_insere_pedido_validates_fisicajuridica_values(): void
    {
        $invalidData = [
            'nropedidoafv' => 'PED123456',
            'nroempresa' => 1,
            'nrocgccpf' => '12345678901',
            'digcgccpf' => '23',
            'nomerazao' => 'João da Silva',
            'fisicajuridica' => 'X', // Valor inválido
            'cidade' => 'São Paulo',
            'uf' => 'SP',
            'bairro' => 'Centro',
            'logradouro' => 'Rua das Flores',
            'nrologradouro' => '123',
            'cep' => '01234567',
            'email' => 'joao@email.com',
            'indentregaretira' => 'E',
            'dtapedidoafv' => '2025-01-14',
            'valor' => 150.75,
            'nroformapagto' => 1,
            'usuinclusao' => 'API_SITEMERCADO',
            'nroparcelas' => 1
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer site-mercado-test-token'
        ])->postJson('/api/v1/site-mercado/pedidos', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fisicajuridica']);
    }

    public function test_insere_itens_validates_numeric_values(): void
    {
        $invalidData = [
            'nropedidoafv' => 'PED123456',
            'seqpedvendaitem' => 1,
            'codacesso' => 'COD12345',
            'seqproduto' => 12345,
            'qtdpedida' => 0, // Quantidade inválida (deve ser > 0)
            'qtdembalagem' => -1, // Quantidade inválida (deve ser > 0)
            'vlrembtabpreco' => 'abc', // Valor inválido (não é número)
            'vlrembinformado' => 15.90
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer site-mercado-test-token'
        ])->postJson('/api/v1/site-mercado/itens', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['qtdpedida', 'qtdembalagem', 'vlrembtabpreco']);
    }
}