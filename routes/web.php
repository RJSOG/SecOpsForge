<?php

use App\Http\Controllers\NotesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('HomePage'))->name('home');

Route::get('/redteam/{path?}', [NotesController::class, 'show'])
    ->where('path', '.*')
    ->defaults('team', 'redteam')
    ->name('redteam');

Route::get('/blueteam/{path?}', [NotesController::class, 'show'])
    ->where('path', '.*')
    ->defaults('team', 'blueteam')
    ->name('blueteam');

Route::get('/about', fn() => Inertia::render('AboutPage'))->name('about');
