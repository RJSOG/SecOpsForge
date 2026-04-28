<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::prefix('build')
    ->name('build.')
    ->group(function () {
        Route::prefix('file')
            ->name('file.')
            ->group(function () {
                Route::post('tree', [FileController::class, 'buildFileTree'])
                    ->name('tree');
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

