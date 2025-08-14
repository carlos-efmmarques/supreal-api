<?php

namespace Tests\Unit;

use App\Models\MasterKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_unique_key(): void
    {
        $key1 = MasterKey::generateKey();
        $key2 = MasterKey::generateKey();

        $this->assertNotEmpty($key1);
        $this->assertNotEmpty($key2);
        $this->assertNotEquals($key1, $key2);
        $this->assertStringStartsWith('mk_', $key1);
        $this->assertEquals(63, strlen($key1)); // 'mk_' (3) + 60 random chars
    }

    public function test_key_is_valid_when_active_and_not_expired(): void
    {
        $masterKey = MasterKey::create([
            'name' => 'Test Master Key',
            'key' => hash('sha256', 'test'),
            'is_active' => true,
            'expires_at' => now()->addDay(),
            'created_by' => 'Test'
        ]);

        $this->assertTrue($masterKey->isValid());
    }

    public function test_key_is_invalid_when_inactive(): void
    {
        $masterKey = MasterKey::create([
            'name' => 'Test Master Key',
            'key' => hash('sha256', 'test'),
            'is_active' => false,
            'created_by' => 'Test'
        ]);

        $this->assertFalse($masterKey->isValid());
    }

    public function test_key_is_invalid_when_expired(): void
    {
        $masterKey = MasterKey::create([
            'name' => 'Test Master Key',
            'key' => hash('sha256', 'test'),
            'is_active' => true,
            'expires_at' => now()->subDay(),
            'created_by' => 'Test'
        ]);

        $this->assertFalse($masterKey->isValid());
    }

    public function test_update_last_used_timestamp(): void
    {
        $masterKey = MasterKey::create([
            'name' => 'Test Master Key',
            'key' => hash('sha256', 'test'),
            'is_active' => true,
            'created_by' => 'Test',
            'last_used_at' => null
        ]);

        $this->assertNull($masterKey->last_used_at);

        $masterKey->updateLastUsed();
        $masterKey->refresh();

        $this->assertNotNull($masterKey->last_used_at);
        $this->assertTrue($masterKey->last_used_at->isToday());
    }

    public function test_find_valid_key_returns_correct_key(): void
    {
        $plainKey = 'test-master-key';
        $hashedKey = hash('sha256', $plainKey);

        $masterKey = MasterKey::create([
            'name' => 'Test Master Key',
            'key' => $hashedKey,
            'is_active' => true,
            'created_by' => 'Test'
        ]);

        $foundKey = MasterKey::findValidKey($plainKey);

        $this->assertNotNull($foundKey);
        $this->assertEquals($masterKey->id, $foundKey->id);
    }

    public function test_find_valid_key_returns_null_for_invalid_key(): void
    {
        $plainKey = 'test-master-key';
        $hashedKey = hash('sha256', $plainKey);

        MasterKey::create([
            'name' => 'Test Master Key',
            'key' => $hashedKey,
            'is_active' => false, // Inactive
            'created_by' => 'Test'
        ]);

        $foundKey = MasterKey::findValidKey($plainKey);

        $this->assertNull($foundKey);
    }

    public function test_key_hides_sensitive_information(): void
    {
        $masterKey = MasterKey::create([
            'name' => 'Test Master Key',
            'key' => hash('sha256', 'test'),
            'is_active' => true,
            'created_by' => 'Test'
        ]);

        $array = $masterKey->toArray();

        $this->assertArrayNotHasKey('key', $array);
        $this->assertArrayHasKey('name', $array);
    }
}