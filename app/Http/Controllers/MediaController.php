<?php

namespace App\Http\Controllers;

use App\Enum\TeamEnum;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    private const TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];

    /**
     * Serves an image from content/{team}/images. Only known image
     * extensions, no sub-folders (the route constrains the file name).
     */
    public function show(string $team, string $file): BinaryFileResponse
    {
        $type = self::TYPES[strtolower(pathinfo($file, PATHINFO_EXTENSION))] ?? null;

        if (! TeamEnum::has($team) || $type === null) {
            abort(404);
        }

        $path = Storage::disk('notes')->path($team.'/images/'.$file);

        if (! is_file($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $type,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
