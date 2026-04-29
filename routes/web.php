<?php

use App\Http\Controllers\EditorController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage with latest notes
Route::get('/', function () {
    return Inertia::render('HomePage', [
        'latest' => NotesController::getLatestNotes(3),
    ]);
})->name('home');

// Search
Route::get('/api/search', [SearchController::class, 'search'])->name('search');

// Editor (auth-protected)
Route::middleware('auth')->group(function () {
    Route::get('/editor', [EditorController::class, 'edit'])->name('editor');
    Route::post('/api/editor/save', [EditorController::class, 'save'])->name('editor.save');
    Route::post('/api/editor/delete', [EditorController::class, 'delete'])->name('editor.delete');
    Route::post('/api/editor/preview', [EditorController::class, 'preview'])->name('editor.preview');
});

// Notes sections
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

// Auth pages
Route::get('/login', fn() => Inertia::render('Auth/LoginPage'))
    ->middleware('guest')
    ->name('login');
