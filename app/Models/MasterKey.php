<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MasterKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'is_active',
        'expires_at',
        'last_used_at',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    protected $hidden = [
        'key' // Ocultar a chave por segurança
    ];

    /**
     * Gera uma nova chave mestra
     */
    public static function generateKey(): string
    {
        return 'mk_' . Str::random(60); // Prefixo 'mk_' para master key
    }

    /**
     * Verifica se a chave é válida
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Atualiza o timestamp de último uso
     */
    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Encontra uma chave válida pelo hash
     */
    public static function findValidKey(string $key): ?self
    {
        $hashedKey = hash('sha256', $key);
        
        $masterKey = self::where('key', $hashedKey)
            ->where('is_active', true)
            ->first();

        if (!$masterKey || !$masterKey->isValid()) {
            return null;
        }

        return $masterKey;
    }
}