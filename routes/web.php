<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('api')->name('api.')->group(function () {
    require __DIR__ . '/api.php';
});

Route::get('/', fn() => Inertia::render('HomePage'))->name('home');

Route::get('/redteam', fn() => Inertia::render('RedTeamPage'))->name('redteam');

Route::get('/blueteam', fn() => Inertia::render('BlueTeamPage'))->name('blueteam');

Route::get('/about', fn() => Inertia::render('AboutPage'))->name('about');

Route::get('/page/md/{path}', [FileController::class, 'buildFilePage']);
