<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateTokenRequest;
use App\Models\ApiToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $tokens = ApiToken::select('id', 'name', 'abilities', 'last_used_at', 'expires_at', 'is_active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return $this->paginatedResponse($tokens, 'Tokens listados com sucesso');
    }

    public function store(CreateTokenRequest $request): JsonResponse
    {
        $plainToken = ApiToken::generateToken();
        
        $token = ApiToken::create([
            'name' => $request->name,
            'token' => hash('sha256', $plainToken),
            'abilities' => $request->abilities ?? ['*'],
            'expires_at' => $request->expires_at,
            'ip_restriction' => $request->ip_restriction,
            'rate_limit' => $request->rate_limit ?? 60,
            'metadata' => $request->metadata
        ]);

        return $this->success([
            'token' => $plainToken,
            'token_info' => $token->only(['id', 'name', 'abilities', 'expires_at', 'created_at'])
        ], 'Token criado com sucesso. Guarde o token com segurança, ele não será exibido novamente.', 201);
    }

    public function show(string $id): JsonResponse
    {
        $token = ApiToken::find($id);
        
        if (!$token) {
            return $this->notFound('Token não encontrado');
        }

        return $this->success($token->makeHidden(['token']), 'Token recuperado com sucesso');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $token = ApiToken::find($id);
        
        if (!$token) {
            return $this->notFound('Token não encontrado');
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'abilities' => 'sometimes|array',
            'expires_at' => 'sometimes|nullable|date|after:now',
            'is_active' => 'sometimes|boolean',
            'ip_restriction' => 'sometimes|nullable|ip',
            'rate_limit' => 'sometimes|integer|min:1|max:1000',
            'metadata' => 'sometimes|nullable|array'
        ]);

        $token->update($request->only([
            'name', 'abilities', 'expires_at', 'is_active', 
            'ip_restriction', 'rate_limit', 'metadata'
        ]));

        return $this->success($token->makeHidden(['token']), 'Token atualizado com sucesso');
    }

    public function destroy(string $id): JsonResponse
    {
        $token = ApiToken::find($id);
        
        if (!$token) {
            return $this->notFound('Token não encontrado');
        }

        $token->delete();

        return $this->success(null, 'Token removido com sucesso');
    }

    public function revoke(string $id): JsonResponse
    {
        $token = ApiToken::find($id);
        
        if (!$token) {
            return $this->notFound('Token não encontrado');
        }

        $token->update(['is_active' => false]);

        return $this->success(null, 'Token revogado com sucesso');
    }

    public function activate(string $id): JsonResponse
    {
        $token = ApiToken::find($id);
        
        if (!$token) {
            return $this->notFound('Token não encontrado');
        }

        $token->update(['is_active' => true]);

        return $this->success(null, 'Token ativado com sucesso');
    }
}