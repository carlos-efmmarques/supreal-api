<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'is_active',
        'ip_restriction',
        'rate_limit',
        'metadata'
    ];

    protected $casts = [
        'abilities' => 'array',
        'metadata' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'rate_limit' => 'integer'
    ];

    protected $hidden = [
        'token'
    ];

    public static function generateToken(): string
    {
        return hash('sha256', Str::random(40));
    }

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

    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public function hasAbility(string $ability): bool
    {
        if (empty($this->abilities)) {
            return true;
        }

        return in_array('*', $this->abilities) || in_array($ability, $this->abilities);
    }
}