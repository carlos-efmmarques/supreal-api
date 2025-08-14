<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ExampleStoreRequest;
use App\Http\Requests\ExampleUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExampleController extends BaseController
{
    private array $exampleData = [
        ['id' => 1, 'name' => 'Item 1', 'description' => 'Descrição do item 1', 'status' => 'active'],
        ['id' => 2, 'name' => 'Item 2', 'description' => 'Descrição do item 2', 'status' => 'inactive'],
        ['id' => 3, 'name' => 'Item 3', 'description' => 'Descrição do item 3', 'status' => 'active'],
    ];

    /**
     * Lista todos os exemplos
     * 
     * @group Example
     * @queryParam search string Filtrar por nome. Example: Item
     * @queryParam status string Filtrar por status (active/inactive). Example: active
     * @response 200 {"success": true, "message": "Dados recuperados com sucesso", "data": [{"id": 1, "name": "Item 1", "description": "Descrição do item 1", "status": "active"}]}
     */
    public function index(Request $request): JsonResponse
    {
        $data = collect($this->exampleData);
        
        if ($request->has('search')) {
            $search = strtolower($request->search);
            $data = $data->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['name']), $search);
            });
        }
        
        if ($request->has('status')) {
            $data = $data->where('status', $request->status);
        }
        
        return $this->success($data->values()->all(), 'Dados recuperados com sucesso');
    }

    /**
     * Cria um novo exemplo
     * 
     * @group Example
     * @bodyParam name string required Nome do item. Example: Novo Item
     * @bodyParam description string required Descrição do item. Example: Uma descrição detalhada
     * @bodyParam status string Status do item (active/inactive). Example: active
     * @response 201 {"success": true, "message": "Item criado com sucesso", "data": {"id": 4, "name": "Novo Item", "description": "Uma descrição detalhada", "status": "active"}}
     */
    public function store(ExampleStoreRequest $request): JsonResponse
    {
        $newItem = [
            'id' => rand(100, 999),
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
            'created_at' => now()->toDateTimeString()
        ];
        
        return $this->success($newItem, 'Item criado com sucesso', 201);
    }

    /**
     * Exibe um exemplo específico
     * 
     * @group Example
     * @urlParam id integer required ID do item. Example: 1
     * @response 200 {"success": true, "message": "Item recuperado com sucesso", "data": {"id": 1, "name": "Item 1", "description": "Descrição do item 1", "status": "active"}}
     * @response 404 {"success": false, "message": "Item não encontrado", "data": null}
     */
    public function show(string $id): JsonResponse
    {
        $item = collect($this->exampleData)->firstWhere('id', (int)$id);
        
        if (!$item) {
            return $this->notFound('Item não encontrado');
        }
        
        return $this->success($item, 'Item recuperado com sucesso');
    }

    /**
     * Atualiza um exemplo
     * 
     * @group Example
     * @urlParam id integer required ID do item. Example: 1
     * @bodyParam name string Nome do item. Example: Item Atualizado
     * @bodyParam description string Descrição do item. Example: Descrição atualizada
     * @bodyParam status string Status do item (active/inactive). Example: inactive
     * @response 200 {"success": true, "message": "Item atualizado com sucesso", "data": {"id": 1, "name": "Item Atualizado", "description": "Descrição atualizada", "status": "inactive"}}
     * @response 404 {"success": false, "message": "Item não encontrado", "data": null}
     */
    public function update(ExampleUpdateRequest $request, string $id): JsonResponse
    {
        $item = collect($this->exampleData)->firstWhere('id', (int)$id);
        
        if (!$item) {
            return $this->notFound('Item não encontrado');
        }
        
        $updatedItem = array_merge($item, $request->validated(), [
            'updated_at' => now()->toDateTimeString()
        ]);
        
        return $this->success($updatedItem, 'Item atualizado com sucesso');
    }

    /**
     * Remove um exemplo
     * 
     * @group Example
     * @urlParam id integer required ID do item. Example: 1
     * @response 200 {"success": true, "message": "Item removido com sucesso", "data": null}
     * @response 404 {"success": false, "message": "Item não encontrado", "data": null}
     */
    public function destroy(string $id): JsonResponse
    {
        $item = collect($this->exampleData)->firstWhere('id', (int)$id);
        
        if (!$item) {
            return $this->notFound('Item não encontrado');
        }
        
        return $this->success(null, 'Item removido com sucesso');
    }

    /**
     * Endpoint de teste de saúde da API
     * 
     * @group Example
     * @response 200 {"success": true, "message": "API está funcionando", "data": {"status": "healthy", "timestamp": "2025-01-14 10:00:00", "version": "1.0.0"}}
     */
    public function health(): JsonResponse
    {
        return $this->success([
            'status' => 'healthy',
            'timestamp' => now()->toDateTimeString(),
            'version' => '1.0.0'
        ], 'API está funcionando');
    }
}