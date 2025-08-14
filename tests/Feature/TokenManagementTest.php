<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\MasterKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenManagementTest extends TestCase
{
    use RefreshDatabase;

    private string $validMasterKey;
    private MasterKey $masterKeyModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar uma chave mestra válida para os testes
        $this->validMasterKey = MasterKey::generateKey();
        $hashedKey = hash('sha256', $this->validMasterKey);
        
        $this->masterKeyModel = MasterKey::create([
            'name' => 'Test Master Key',
            'key' => $hashedKey,
            'is_active' => true,
            'created_by' => 'Test Suite'
        ]);
    }

    public function test_can_list_tokens_with_valid_master_key(): void
    {
        $response = $this->get('/api/tokens', [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'items',
                'pagination'
            ]
        ]);
    }

    public function test_cannot_list_tokens_without_master_key(): void
    {
        $response = $this->get('/api/tokens');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Chave mestra não fornecida. Use o header X-Master-Key.'
        ]);
    }

    public function test_cannot_list_tokens_with_invalid_master_key(): void
    {
        $response = $this->get('/api/tokens', [
            'X-Master-Key' => 'invalid-key'
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Chave mestra inválida, expirada ou inativa.'
        ]);
    }

    public function test_can_create_token_with_valid_master_key(): void
    {
        $response = $this->post('/api/tokens', [
            'name' => 'Test Token',
            'abilities' => ['read', 'write'],
            'expires_in_days' => 30
        ], [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'token',
                'token_info' => [
                    'id',
                    'name',
                    'abilities',
                    'expires_at'
                ]
            ]
        ]);

        $this->assertDatabaseHas('api_tokens', [
            'name' => 'Test Token'
        ]);
    }

    public function test_cannot_create_token_without_master_key(): void
    {
        $response = $this->post('/api/tokens', [
            'name' => 'Test Token'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Chave mestra não fornecida. Use o header X-Master-Key.'
        ]);
    }

    public function test_can_show_token_with_valid_master_key(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => true
        ]);

        $response = $this->get("/api/tokens/{$token->id}", [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'is_active'
            ]
        ]);
    }

    public function test_can_revoke_token_with_valid_master_key(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => true
        ]);

        $response = $this->post("/api/tokens/{$token->id}/revoke", [], [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Token revogado com sucesso'
        ]);

        $this->assertDatabaseHas('api_tokens', [
            'id' => $token->id,
            'is_active' => false
        ]);
    }

    public function test_can_activate_token_with_valid_master_key(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => false
        ]);

        $response = $this->post("/api/tokens/{$token->id}/activate", [], [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Token ativado com sucesso'
        ]);

        $this->assertDatabaseHas('api_tokens', [
            'id' => $token->id,
            'is_active' => true
        ]);
    }

    public function test_can_update_token_with_valid_master_key(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => true,
            'abilities' => ['read']
        ]);

        $response = $this->put("/api/tokens/{$token->id}", [
            'name' => 'Updated Token',
            'abilities' => ['read', 'write']
        ], [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Token atualizado com sucesso'
        ]);

        $this->assertDatabaseHas('api_tokens', [
            'id' => $token->id,
            'name' => 'Updated Token'
        ]);
    }

    public function test_can_delete_token_with_valid_master_key(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => true
        ]);

        $response = $this->delete("/api/tokens/{$token->id}", [], [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Token removido com sucesso'
        ]);

        $this->assertDatabaseMissing('api_tokens', [
            'id' => $token->id
        ]);
    }

    public function test_master_key_last_used_is_updated_on_request(): void
    {
        $this->assertNull($this->masterKeyModel->last_used_at);

        $this->get('/api/tokens', [
            'X-Master-Key' => $this->validMasterKey
        ]);

        $this->masterKeyModel->refresh();
        $this->assertNotNull($this->masterKeyModel->last_used_at);
        $this->assertTrue($this->masterKeyModel->last_used_at->isToday());
    }

    public function test_cannot_use_expired_master_key(): void
    {
        // Criar uma chave mestra expirada
        $expiredKey = MasterKey::generateKey();
        $hashedExpiredKey = hash('sha256', $expiredKey);
        
        MasterKey::create([
            'name' => 'Expired Master Key',
            'key' => $hashedExpiredKey,
            'is_active' => true,
            'expires_at' => now()->subDay(),
            'created_by' => 'Test Suite'
        ]);

        $response = $this->get('/api/tokens', [
            'X-Master-Key' => $expiredKey
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Chave mestra inválida, expirada ou inativa.'
        ]);
    }

    public function test_cannot_use_inactive_master_key(): void
    {
        // Criar uma chave mestra inativa
        $inactiveKey = MasterKey::generateKey();
        $hashedInactiveKey = hash('sha256', $inactiveKey);
        
        MasterKey::create([
            'name' => 'Inactive Master Key',
            'key' => $hashedInactiveKey,
            'is_active' => false,
            'created_by' => 'Test Suite'
        ]);

        $response = $this->get('/api/tokens', [
            'X-Master-Key' => $inactiveKey
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Chave mestra inválida, expirada ou inativa.'
        ]);
    }
}