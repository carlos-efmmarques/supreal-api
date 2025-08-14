<?php

namespace Database\Seeders;

use App\Models\ApiToken;
use Illuminate\Database\Seeder;

class ApiTokenSeeder extends Seeder
{
    public function run(): void
    {
        // Token de desenvolvimento com todas as permissões
        $devToken = 'dev-token-' . bin2hex(random_bytes(20));
        ApiToken::create([
            'name' => 'Development Token',
            'token' => hash('sha256', $devToken),
            'abilities' => ['*'],
            'is_active' => true,
            'rate_limit' => 1000,
            'metadata' => [
                'environment' => 'development',
                'created_by' => 'seeder'
            ]
        ]);
        
        $this->command->info("Token de desenvolvimento criado: $devToken");

        // Token de teste com permissões limitadas
        $testToken = 'test-token-' . bin2hex(random_bytes(20));
        ApiToken::create([
            'name' => 'Test Token (Read Only)',
            'token' => hash('sha256', $testToken),
            'abilities' => ['read'],
            'is_active' => true,
            'rate_limit' => 100,
            'expires_at' => now()->addMonths(3),
            'metadata' => [
                'environment' => 'testing',
                'created_by' => 'seeder'
            ]
        ]);
        
        $this->command->info("Token de teste criado: $testToken");

        // Token com IP restrito
        $restrictedToken = 'restricted-token-' . bin2hex(random_bytes(20));
        ApiToken::create([
            'name' => 'IP Restricted Token',
            'token' => hash('sha256', $restrictedToken),
            'abilities' => ['*'],
            'is_active' => true,
            'ip_restriction' => '127.0.0.1',
            'rate_limit' => 60,
            'metadata' => [
                'environment' => 'production',
                'created_by' => 'seeder'
            ]
        ]);
        
        $this->command->info("Token com restrição de IP criado: $restrictedToken");

        // Token expirado (para testes)
        ApiToken::create([
            'name' => 'Expired Token',
            'token' => hash('sha256', 'expired-token'),
            'abilities' => ['*'],
            'is_active' => true,
            'expires_at' => now()->subDay(),
            'rate_limit' => 60,
            'metadata' => [
                'environment' => 'testing',
                'created_by' => 'seeder',
                'note' => 'Este token está expirado propositalmente para testes'
            ]
        ]);

        // Token inativo (para testes)
        ApiToken::create([
            'name' => 'Inactive Token',
            'token' => hash('sha256', 'inactive-token'),
            'abilities' => ['*'],
            'is_active' => false,
            'rate_limit' => 60,
            'metadata' => [
                'environment' => 'testing',
                'created_by' => 'seeder',
                'note' => 'Este token está inativo propositalmente para testes'
            ]
        ]);

        $this->command->warn('⚠️  IMPORTANTE: Guarde os tokens gerados com segurança!');
        $this->command->warn('Eles não serão exibidos novamente.');
        $this->command->info('');
        $this->command->info('Tokens de teste fixos (apenas para desenvolvimento):');
        $this->command->info('- Token expirado: expired-token');
        $this->command->info('- Token inativo: inactive-token');
    }
}