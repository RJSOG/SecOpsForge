<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

// Security: these endpoints read/write the filesystem and were previously
// reachable without authentication. Require a logged-in session (the api
// middleware group is stateful via Sanctum, so the standard 'auth' guard
// works here) for all of them.
Route::middleware('auth')->group(function () {
    Route::prefix('build')
        ->name('build.')
        ->group(function () {
            Route::prefix('file')
                ->name('file.')
                ->group(function () {
                    Route::post('tree', [FileController::class, 'buildFileTree'])
                        ->name('tree');
                    Route::post('page', [FileController::class, 'buildFilePage'])
                        ->name('page');
                });
        })
        ->withoutMiddleware(HandleInertiaRequests::class);

    Route::prefix('validate')
        ->name('validate.')
        ->group(function () {
            Route::prefix('file')
                ->name('file.')
                ->group(function () {
                    Route::post('tree', [FileController::class, 'validateFileTree'])
                        ->name('tree');
                });
        });
});

