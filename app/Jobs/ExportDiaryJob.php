<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExportDiaryJob implements ShouldQueue
{
    use Queueable;

    public $userId;
    public $startDate;
    public $endDate;
    public $options;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $startDate, $endDate, $options = [])
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\ExportService $service): void
    {
        // 1. Generate PDF content
        $pdf = $service->generatePdf($this->userId, $this->startDate, $this->endDate, $this->options);

        // 2. Save to storage
        if (!file_exists(storage_path('app/public/exports'))) {
            mkdir(storage_path('app/public/exports'), 0755, true);
        }

        $filename = 'diary_export_' . now()->format('Ymd_His') . '.pdf';
        $path = 'exports/' . $filename;

        $pdf->save(storage_path('app/public/' . $path));

        // 3. Notify User
        $user = \App\Models\User::find($this->userId);
        $url = asset('storage/' . $path);

        $user->notify(new \App\Notifications\ExportReady($url, $filename));
    }
}
