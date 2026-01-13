<?php

namespace App\Services;

use App\Models\DiaryEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    protected $immichService;

    public function __construct(ImmichService $immichService)
    {
        $this->immichService = $immichService;
    }

    public function generatePdf($userId, $startDate, $endDate, $options = [])
    {
        $entries = DiaryEntry::with(['mood', 'photos', 'interactions.person', 'childrenLogs.person'])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        // Pre-process images to Base64 to handle auth/remote urls compatible with DOMPDF
        foreach ($entries as $entry) {
            foreach ($entry->photos as $photo) {
                $photo->src = $this->resolveImageSource($photo);
            }
        }

        $data = [
            'entries' => $entries,
            'startDate' => Carbon::parse($startDate),
            'endDate' => Carbon::parse($endDate),
            'options' => $options, // ['front_page' => bool, 'title' => string]
            'user' => \App\Models\User::find($userId),
        ];

        $pdf = Pdf::loadView('pdf.export', $data);

        // Optional: formatting options
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true); // Backup, but we rely on base64 mostly

        return $pdf;
    }

    protected function resolveImageSource($photo)
    {
        // 1. If we have a local path that exists, use it (convert to absolute path for DOMPDF)
        if ($photo->local_path && Storage::disk('public')->exists($photo->local_path)) {
            $path = storage_path('app/public/' . $photo->local_path);
            return $this->base64EncodeImage($path);
        }

        // 2. If it's an Immich asset (reference only), fetch and convert
        if ($photo->immich_asset_id) {
            // We use the thumbnail/preview url logic from service, but we need the RAW data
            // ImmichService provides URL, but we need to fetch it with headers.
            // Let's use the downloadAsset logic but into memory.

            // Construct auth-protected URL
            $url = $this->immichService->getAssetThumbnailUrl($photo->immich_asset_id);
            // Thumbnail is lighter for PDF than full 4K image -> getAssetThumbnailUrl returns URL string.

            // WE need to make the HTTP request manually to get the blob
            $immichUrl = SystemConfig::get('immich_url');
            $immichKey = SystemConfig::get('immich_key');

            if ($immichUrl && $immichKey) {
                try {
                    // Check if service method exists for getting stream, or just manual HTTP
                    $endpoint = rtrim($immichUrl, '/') . "/api/asset/thumbnail/{$photo->immich_asset_id}?format=JPEG";

                    $response = Http::withHeaders(['x-api-key' => $immichKey])->get($endpoint);

                    if ($response->successful()) {
                        return 'data:image/jpeg;base64,' . base64_encode($response->body());
                    }
                } catch (\Exception $e) {
                    // Log error?
                }
            }
        }

        return null; // Placeholder or empty in view
    }

    protected function base64EncodeImage($path)
    {
        try {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        } catch (\Exception $e) {
            return null;
        }
    }
}
