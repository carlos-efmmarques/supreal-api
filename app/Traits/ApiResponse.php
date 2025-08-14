<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Operação realizada com sucesso', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function error(string $message = 'Erro ao processar requisição', $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $errors
        ], $code);
    }

    protected function notFound(string $message = 'Recurso não encontrado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], 404);
    }

    protected function unauthorized(string $message = 'Não autorizado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], 401);
    }

    protected function forbidden(string $message = 'Acesso negado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], 403);
    }

    protected function validationError($errors, string $message = 'Erro de validação'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $errors
        ], 422);
    }

    protected function serverError(string $message = 'Erro interno do servidor'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], 500);
    }

    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Dados recuperados com sucesso'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'items' => $paginator->items(),
                'pagination' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'next_page_url' => $paginator->nextPageUrl(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                ]
            ]
        ], 200);
    }
}