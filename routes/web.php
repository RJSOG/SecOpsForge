<?php

use App\Http\Controllers\EditorController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeoController;
use App\Support\Seo;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage with latest notes
Route::get('/', function () {
    $seo = Seo::make(jsonLd: [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => Seo::SITE_NAME,
        'url' => Seo::url(),
        'description' => Seo::DEFAULT_DESCRIPTION,
        'author' => ['@type' => 'Person', 'name' => Seo::AUTHOR],
    ]);

    return Inertia::render('HomePage', [
        'latest' => NotesController::getLatestNotes(3),
        'seo' => $seo,
    ])->withViewData(['seo' => $seo]);
})->name('home');

// Crawlers
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

// Search
Route::get('/api/search', [SearchController::class, 'search'])
    ->middleware('throttle:60,1')
    ->name('search');

// Editor (admin-only: any other authenticated account gets a 403)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/editor', [EditorController::class, 'edit'])->name('editor');
    Route::post('/api/editor/save', [EditorController::class, 'save'])->name('editor.save');
    Route::post('/api/editor/delete', [EditorController::class, 'delete'])->name('editor.delete');
    Route::post('/api/editor/preview', [EditorController::class, 'preview'])->name('editor.preview');
    Route::post('/api/editor/upload-image', [EditorController::class, 'uploadImage'])->name('editor.upload-image');
});

// Images embedded in the articles (content/{team}/images)
Route::get('/media/{team}/{file}', [MediaController::class, 'show'])
    ->where('file', '[A-Za-z0-9][A-Za-z0-9._-]*')
    ->name('media');

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

Route::get('/whoami', function () {
    $seo = Seo::make(
        title: 'Whoami · '.Seo::AUTHOR,
        description: 'Eliott Donatien (RJSOG), cybersecurity engineer at Orange Cyberdefense in Singapore: SOC, EDR/XDR, incident response and offensive security. OSCP certified.',
        type: 'profile',
        jsonLd: [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => Seo::AUTHOR,
            'alternateName' => 'RJSOG',
            'jobTitle' => 'Cybersecurity Engineer',
            'worksFor' => ['@type' => 'Organization', 'name' => 'Orange Cyberdefense'],
            'url' => Seo::url('whoami'),
            'sameAs' => [
                'https://github.com/RJSOG',
                'https://www.linkedin.com/in/eliott-donatien-615295181/',
            ],
        ],
    );

    return Inertia::render('WhoamiPage', ['seo' => $seo])->withViewData(['seo' => $seo]);
})->name('whoami');

// Auth pages
Route::get('/login', function () {
    $seo = Seo::make(title: 'Login', index: false);

    return Inertia::render('Auth/LoginPage', ['seo' => $seo])->withViewData(['seo' => $seo]);
})
    ->middleware('guest')
    ->name('login');

require __DIR__.'/auth.php';
