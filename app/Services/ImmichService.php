<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImmichService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->url = SystemConfig::get('immich_url');
        $this->key = SystemConfig::get('immich_key');
    }

    /**
     * Fetch assets for a specific date.
     * 
     * @param string $date (Y-m-d)
     * @return array
     */
    public function getDailyAssets($date)
    {
        if (!$this->url || !$this->key) {
            return [];
        }

        $dateObj = Carbon::parse($date);

        try {
            // Immich API: Search metadata or use timeline/search
            // Strategy: Use the /search/metadata (or similar search endpoint) filtering by createdAfter/createdBefore
            // Or simpler: /search/date-bucket?timeBucketSize=DAY (if available) - Immich API changes often.
            // Most reliable stable: /search/metadata

            $start = $dateObj->copy()->startOfDay()->toIso8601String();
            $end = $dateObj->copy()->endOfDay()->toIso8601String();

            // V1 Search API (Common in newer Immich versions)
            $response = Http::withHeaders(['x-api-key' => $this->key])
                ->post(rtrim($this->url, '/') . '/api/search/metadata', [
                        'takenAfter' => $start,
                        'takenBefore' => $end,
                        'withExif' => false,
                        'isVisible' => true,
                        'type' => 'IMAGE', // Ensure we get images (or videos if desired)
                    ]);

            if ($response->successful()) {
                $assets = $response->json()['assets']['items'] ?? [];

                // Map to simpler structure
                return array_map(function ($asset) {
                    return [
                        'id' => $asset['id'],
                        'thumbUrl' => $this->getAssetThumbnailUrl($asset['id']),
                        'previewUrl' => $this->getAssetPreviewUrl($asset['id']),
                        'createdAt' => $asset['fileCreatedAt'],
                    ];
                }, $assets);
            }

            Log::error('Immich Search Failed: ' . $response->body());
            return [];

        } catch (\Exception $e) {
            Log::error('Immich Exception: ' . $e->getMessage());
            return [];
        }
    }

    public function getAssetThumbnailUrl($id)
    {
        // Use local proxy to avoid ORB/CORS issues and handle auth
        return route('immich.asset', ['id' => $id, 'type' => 'thumbnail']);
    }

    public function getAssetPreviewUrl($id)
    {
        // Use local proxy
        return route('immich.asset', ['id' => $id, 'type' => 'preview']);
    }

    /**
     * Download asset to local storage.
     * 
     * @param string $id Immich Asset ID
     * @return string|null Local path or null on failure
     */
    public function downloadAsset($id)
    {
        if (!$this->url || !$this->key)
            return null;

        // Use the original file endpoint or preview endpoint
        $endpoint = rtrim($this->url, '/') . "/api/asset/file/{$id}";

        try {
            $response = Http::withHeaders(['x-api-key' => $this->key])
                ->sink(storage_path("app/public/photos/{$id}.jpg")) // Simplified assumption of jpg for now
                ->get($endpoint);

            if ($response->successful()) {
                return "photos/{$id}.jpg";
            }
        } catch (\Exception $e) {
            Log::error("Failed to download asset {$id}: " . $e->getMessage());
        }

        return null;
    }
}
