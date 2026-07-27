<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaController extends Controller
{
    public function show(string $path): StreamedResponse
    {
        $relativePath = ltrim(str_replace('\\', '/', $path), '/');

        if ($relativePath === '' || str_contains($relativePath, '..')) {
            throw new NotFoundHttpException('File not found.');
        }

        if (! Storage::disk('public')->exists($relativePath)) {
            throw new NotFoundHttpException('File not found.');
        }

        $mime = Storage::disk('public')->mimeType($relativePath) ?: 'application/octet-stream';

        return Storage::disk('public')->response($relativePath, null, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
