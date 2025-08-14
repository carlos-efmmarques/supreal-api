<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\V1\ExampleController;
use App\Http\Controllers\Api\V1\SiteMercadoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas
Route::get('/health', [ExampleController::class, 'health'])->name('api.health');

// Rotas para gerenciamento de tokens (protegidas por autenticação básica ou outro método interno)
Route::prefix('tokens')->name('api.tokens.')->group(function () {
    Route::get('/', [TokenController::class, 'index'])->name('index');
    Route::post('/', [TokenController::class, 'store'])->name('store');
    Route::get('/{id}', [TokenController::class, 'show'])->name('show');
    Route::put('/{id}', [TokenController::class, 'update'])->name('update');
    Route::delete('/{id}', [TokenController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/revoke', [TokenController::class, 'revoke'])->name('revoke');
    Route::post('/{id}/activate', [TokenController::class, 'activate'])->name('activate');
});

// Rotas versionadas da API
Route::prefix('v1')->middleware(['auth.api'])->name('api.v1.')->group(function () {
    
    // Example endpoints
    Route::prefix('examples')->name('examples.')->group(function () {
        Route::get('/', [ExampleController::class, 'index'])->name('index');
        Route::post('/', [ExampleController::class, 'store'])->name('store');
        Route::get('/{id}', [ExampleController::class, 'show'])->name('show');
        Route::put('/{id}', [ExampleController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExampleController::class, 'destroy'])->name('destroy');
    });
    
    // Site Mercado - APIs para integração com ERP Oracle
    Route::prefix('site-mercado')->name('site-mercado.')->group(function () {
        Route::post('/pedidos', [SiteMercadoController::class, 'inserePedido'])->name('pedidos.store');
        Route::post('/itens', [SiteMercadoController::class, 'insereItens'])->name('itens.store');
    });
    
    // Adicione aqui novos grupos de rotas para outros recursos
    // Route::apiResource('users', UserController::class);
    // Route::apiResource('products', ProductController::class);
});