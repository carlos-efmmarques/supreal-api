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
}
