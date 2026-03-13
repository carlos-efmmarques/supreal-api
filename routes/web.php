<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Supreal API',
        'version' => '1.0',
        'docs' => url('/docs'),
    ]);
});

// Serve Scribe docs without the Scribe package (production)
if (!class_exists(\Knuckles\Scribe\ScribeServiceProvider::class)) {
    Route::get('/docs', fn () => view('scribe.index'));
    Route::get('/docs.postman', fn () => response()->file(storage_path('app/private/scribe/collection.json')));
    Route::get('/docs.openapi', fn () => response()->file(storage_path('app/private/scribe/openapi.yaml')));
}
