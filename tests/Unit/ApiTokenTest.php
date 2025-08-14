<?php

namespace Tests\Unit;

use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_unique_token(): void
    {
        $token1 = ApiToken::generateToken();
        $token2 = ApiToken::generateToken();

        $this->assertNotEmpty($token1);
        $this->assertNotEmpty($token2);
        $this->assertNotEquals($token1, $token2);
        $this->assertEquals(64, strlen($token1));
    }

    public function test_token_is_valid_when_active_and_not_expired(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => true,
            'expires_at' => now()->addDay()
        ]);

        $this->assertTrue($token->isValid());
    }

    public function test_token_is_invalid_when_inactive(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => false
        ]);

        $this->assertFalse($token->isValid());
    }

    public function test_token_is_invalid_when_expired(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'is_active' => true,
            'expires_at' => now()->subDay()
        ]);

        $this->assertFalse($token->isValid());
    }

    public function test_token_has_all_abilities_when_empty(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'abilities' => null
        ]);

        $this->assertTrue($token->hasAbility('read'));
        $this->assertTrue($token->hasAbility('write'));
        $this->assertTrue($token->hasAbility('delete'));
    }

    public function test_token_has_all_abilities_with_wildcard(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'abilities' => ['*']
        ]);

        $this->assertTrue($token->hasAbility('read'));
        $this->assertTrue($token->hasAbility('write'));
        $this->assertTrue($token->hasAbility('delete'));
    }

    public function test_token_has_specific_abilities(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'abilities' => ['read', 'write']
        ]);

        $this->assertTrue($token->hasAbility('read'));
        $this->assertTrue($token->hasAbility('write'));
        $this->assertFalse($token->hasAbility('delete'));
    }

    public function test_update_last_used_timestamp(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test'),
            'last_used_at' => null
        ]);

        $this->assertNull($token->last_used_at);

        $token->updateLastUsed();
        $token->refresh();

        $this->assertNotNull($token->last_used_at);
        $this->assertTrue($token->last_used_at->isToday());
    }

    public function test_token_hides_sensitive_information(): void
    {
        $token = ApiToken::create([
            'name' => 'Test Token',
            'token' => hash('sha256', 'test')
        ]);

        $array = $token->toArray();

        $this->assertArrayNotHasKey('token', $array);
        $this->assertArrayHasKey('name', $array);
    }
}