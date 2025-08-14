<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleApiTest extends TestCase
{
    use RefreshDatabase;

    protected $apiToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar um token de teste
        $this->apiToken = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test-token'),
            'abilities' => ['*'],
            'is_active' => true,
            'rate_limit' => 100
        ]);
    }

    public function test_health_endpoint_is_accessible_without_auth(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'API está funcionando',
                'data' => [
                    'status' => 'healthy',
                    'version' => '1.0.0'
                ]
            ]);
    }

    public function test_protected_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/examples');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token de autenticação não fornecido'
            ]);
    }

    public function test_can_list_examples_with_valid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->getJson('/api/v1/examples');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dados recuperados com sucesso'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'description', 'status']
                ]
            ]);
    }

    public function test_can_create_example_with_valid_data(): void
    {
        $data = [
            'name' => 'Test Item',
            'description' => 'Test Description',
            'status' => 'active'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->postJson('/api/v1/examples', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Item criado com sucesso',
                'data' => [
                    'name' => 'Test Item',
                    'description' => 'Test Description',
                    'status' => 'active'
                ]
            ]);
    }

    public function test_create_example_validation_fails_without_required_fields(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->postJson('/api/v1/examples', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Erro de validação'
            ])
            ->assertJsonStructure([
                'data' => ['name', 'description']
            ]);
    }

    public function test_can_get_single_example(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->getJson('/api/v1/examples/1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Item recuperado com sucesso',
                'data' => [
                    'id' => 1,
                    'name' => 'Item 1'
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_example(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->getJson('/api/v1/examples/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Item não encontrado'
            ]);
    }

    public function test_can_update_example(): void
    {
        $data = [
            'name' => 'Updated Item'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->putJson('/api/v1/examples/1', $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Item atualizado com sucesso',
                'data' => [
                    'name' => 'Updated Item'
                ]
            ]);
    }

    public function test_can_delete_example(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->deleteJson('/api/v1/examples/1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Item removido com sucesso'
            ]);
    }

    public function test_invalid_token_returns_unauthorized(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token'
        ])->getJson('/api/v1/examples');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token inválido ou expirado'
            ]);
    }

    public function test_inactive_token_returns_unauthorized(): void
    {
        $this->apiToken->update(['is_active' => false]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->getJson('/api/v1/examples');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token inválido ou expirado'
            ]);
    }

    public function test_expired_token_returns_unauthorized(): void
    {
        $this->apiToken->update(['expires_at' => now()->subDay()]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token'
        ])->getJson('/api/v1/examples');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Token inválido ou expirado'
            ]);
    }
}