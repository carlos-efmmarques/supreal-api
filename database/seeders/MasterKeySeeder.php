<?php

namespace Database\Seeders;

use App\Models\MasterKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existe uma chave mestra ativa
        if (MasterKey::where('is_active', true)->exists()) {
            $this->command->info('Já existe uma chave mestra ativa. Nenhuma nova chave foi criada.');
            return;
        }

        // Gerar uma chave mestra inicial
        $plainKey = MasterKey::generateKey();
        $hashedKey = hash('sha256', $plainKey);

        $masterKey = MasterKey::create([
            'name' => 'Chave Mestra Inicial',
            'key' => $hashedKey,
            'is_active' => true,
            'created_by' => 'Sistema (Seeder)',
            'metadata' => [
                'created_via' => 'seeder',
                'created_at_timestamp' => now()->timestamp,
                'description' => 'Chave mestra criada automaticamente durante a configuração inicial do sistema'
            ]
        ]);

        $this->command->info('✅ Chave mestra inicial criada com sucesso!');
        $this->command->newLine();
        
        $this->command->warn('🔑 CHAVE MESTRA INICIAL (guarde com segurança):');
        $this->command->line($plainKey);
        $this->command->newLine();
        
        $this->command->warn('⚠️  IMPORTANTE:');
        $this->command->line('• Esta chave será exibida apenas uma vez');
        $this->command->line('• Guarde-a em local seguro');
        $this->command->line('• Use ela no header X-Master-Key para criar tokens da API');
        $this->command->line('• Para criar novas chaves mestras, use: php artisan master-key:create');
    }
}
