<?php

use App\Http\Controllers\NotesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('HomePage'))->name('home');

Route::get('/redteam/{path?}', [NotesController::class, 'redteam'])
    ->where('path', '.*')
    ->name('redteam');

Route::get('/blueteam/{path?}', [NotesController::class, 'blueteam'])
    ->where('path', '.*')
    ->name('blueteam');

Route::get('/automation/{path?}', [NotesController::class, 'automation'])
    ->where('path', '.*')
    ->name('automation');

Route::get('/whoami', fn() => Inertia::render('WhoamiPage'))->name('whoami');
