<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return $this->unauthorized('Token de autenticação não fornecido');
        }

        $hashedToken = hash('sha256', $bearerToken);
        $apiToken = ApiToken::where('token', $hashedToken)->first();

        if (!$apiToken || !$apiToken->isValid()) {
            return $this->unauthorized('Token inválido ou expirado');
        }

        if ($apiToken->ip_restriction && $apiToken->ip_restriction !== $request->ip()) {
            return $this->forbidden('Acesso negado para este IP');
        }

        $apiToken->updateLastUsed();
        
        $request->merge(['api_token' => $apiToken]);

        return $next($request);
    }
}