<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome/descrição da chave
            $table->string('key', 80)->unique(); // Chave hash
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('created_by')->nullable(); // Quem criou a chave
            $table->json('metadata')->nullable(); // Informações adicionais
            $table->timestamps();
            
            $table->index(['key', 'is_active']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_keys');
    }
};