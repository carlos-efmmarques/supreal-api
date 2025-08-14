<?php

namespace App\Console\Commands;

use App\Models\MasterKey;
use Illuminate\Console\Command;

class CreateMasterKey extends Command
{
    protected $signature = 'master-key:create 
                            {name : Nome/descrição da chave mestra}
                            {--expires= : Data de expiração (formato: Y-m-d H:i:s)}
                            {--created-by= : Quem está criando a chave}';

    protected $description = 'Cria uma nova chave mestra para gerenciamento de tokens da API';

    public function handle(): int
    {
        $name = $this->argument('name');
        $expiresAt = $this->option('expires');
        $createdBy = $this->option('created-by') ?: 'Artisan Command';

        // Gerar a chave
        $plainKey = MasterKey::generateKey();
        $hashedKey = hash('sha256', $plainKey);

        // Criar a chave no banco
        $masterKey = MasterKey::create([
            'name' => $name,
            'key' => $hashedKey,
            'is_active' => true,
            'expires_at' => $expiresAt ? now()->parse($expiresAt) : null,
            'created_by' => $createdBy,
            'metadata' => [
                'created_via' => 'artisan_command',
                'created_at_timestamp' => now()->timestamp
            ]
        ]);

        // Exibir informações
        $this->info('✅ Chave mestra criada com sucesso!');
        $this->newLine();
        
        $this->info('📋 Informações da chave:');
        $this->line("ID: {$masterKey->id}");
        $this->line("Nome: {$masterKey->name}");
        $this->line("Criada por: {$masterKey->created_by}");
        $this->line("Status: " . ($masterKey->is_active ? 'Ativa' : 'Inativa'));
        
        if ($masterKey->expires_at) {
            $this->line("Expira em: {$masterKey->expires_at->format('d/m/Y H:i:s')}");
        } else {
            $this->line("Expira em: Nunca");
        }

        $this->newLine();
        $this->warn('🔑 CHAVE MESTRA (guarde com segurança):');
        $this->line($plainKey);
        $this->newLine();
        
        $this->warn('⚠️  IMPORTANTE:');
        $this->line('• Esta chave será exibida apenas uma vez');
        $this->line('• Guarde-a em local seguro');
        $this->line('• Use ela no header X-Master-Key para criar tokens da API');
        $this->line('• Sem esta chave, não será possível gerenciar tokens');

        return Command::SUCCESS;
    }
}