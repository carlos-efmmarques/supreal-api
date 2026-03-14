<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Supreal API',
        'version' => '1.0',
        'docs' => url('/docs'),
    ]);
});

// Serve Scribe docs - em produção o pacote Scribe não está instalado (dev-only),
// então estas rotas servem os arquivos pré-gerados (blade view, postman, openapi).
// Em dev, o Scribe registra suas próprias rotas via ServiceProvider que têm prioridade.
Route::get('/docs', fn () => view('scribe.index'))->name('scribe');
Route::get('/docs.postman', function () {
    $path = storage_path('app/private/scribe/collection.json');
    if (!file_exists($path)) {
        abort(404, 'Postman collection não encontrada. Execute: php artisan scribe:generate');
    }
    return response()->file($path, ['Content-Type' => 'application/json']);
})->name('scribe.postman');
Route::get('/docs.openapi', function () {
    $path = storage_path('app/private/scribe/openapi.yaml');
    if (!file_exists($path)) {
        abort(404, 'OpenAPI spec não encontrada. Execute: php artisan scribe:generate');
    }
    return response()->file($path, ['Content-Type' => 'application/x-yaml']);
})->name('scribe.openapi');
