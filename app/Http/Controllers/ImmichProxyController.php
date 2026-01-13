<?php

namespace App\Http\Controllers;

use App\Services\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImmichProxyController extends Controller
{
    public function show($id, $type = 'thumbnail')
    {
        $url = SystemConfig::get('immich_url');
        $key = SystemConfig::get('immich_key');

        if (!$url || !$key) {
            \Illuminate\Support\Facades\Log::error('Immich Proxy: Missing Configuration', ['url' => $url, 'key_exists' => !empty($key)]);
            return response('Immich Proxy Error: Missing Configuration', 404);
        }

        // New Immich API structure (v1.106+)
        // Thumbnail: /api/assets/{id}/thumbnail?format=WEBP
        $endpoint = rtrim($url, '/') . "/api/assets/{$id}/thumbnail?format=WEBP";

        if ($type === 'preview' || $type === 'original') {
            // Original/Preview: /api/assets/{id}/original
            // Or /api/assets/{id}/image for specific size, but original is safest for now
            $endpoint = rtrim($url, '/') . "/api/assets/{id}/original";
        }

        // Stream the response
        $response = Http::withHeaders(['x-api-key' => $key])
            ->withoutVerifying() // Optional: depending on SSL setup
            ->send('GET', $endpoint, [
                'stream' => true,
            ]);

        if ($response->failed()) {
            \Illuminate\Support\Facades\Log::error("Immich Proxy: Upstream Error {$response->status()}", ['endpoint' => $endpoint]);
            return response("Immich Proxy: Upstream Error {$response->status()} from {$endpoint}", $response->status());
        }

        return response()->stream(function () use ($response) {
            $body = $response->toPsrResponse()->getBody();
            while (!$body->eof()) {
                echo $body->read(1024);
            }
        }, $response->status(), [
            'Content-Type' => $response->header('Content-Type'),
            'Cache-Control' => 'private, max-age=86400', // Cache for 1 day
        ]);
    }
}
