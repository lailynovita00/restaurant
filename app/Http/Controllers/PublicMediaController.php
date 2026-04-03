<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class PublicMediaController extends Controller
{
    public function show(string $path)
    {
        $normalizedPath = ltrim($path, '/');

        if (str_contains($normalizedPath, '..')) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($normalizedPath)) {
            abort(404);
        }

        return response()->file($disk->path($normalizedPath));
    }
}

