<?php

namespace App\Http\Middleware;

use App\Models\MasterKey;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMasterKey
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se a chave mestra foi fornecida no header X-Master-Key
        $masterKeyValue = $request->header('X-Master-Key');

        if (!$masterKeyValue) {
            return $this->unauthorized('Chave mestra não fornecida. Use o header X-Master-Key.');
        }

        // Verificar se a chave é válida
        $masterKey = MasterKey::findValidKey($masterKeyValue);

        if (!$masterKey) {
            return $this->forbidden('Chave mestra inválida, expirada ou inativa.');
        }

        // Atualizar último uso
        $masterKey->updateLastUsed();
        
        // Adicionar a chave mestra na request para uso posterior
        $request->merge(['master_key' => $masterKey]);

        return $next($request);
    }
}