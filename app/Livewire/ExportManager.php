<?php

namespace App\Livewire;

use App\Jobs\ExportDiaryJob;
use App\Services\ExportService;
use Carbon\Carbon;
use Livewire\Component;

class ExportManager extends Component
{
    public $startDate;
    public $endDate;
    public $includeFrontPage = true;
    public $processing = false;

    protected $rules = [
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
    ];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function export(ExportService $service)
    {
        $this->validate();

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        // Options array
        $options = [
            'front_page' => $this->includeFrontPage,
            'title' => 'My Diary Export' // Could be customizable
        ];

        // Check duration for Background vs Direct
        // Threshold: > 31 days
        if ($start->diffInDays($end) > 31) {
            // Background Job
            ExportDiaryJob::dispatch(auth()->id(), $this->startDate, $this->endDate, $options);

            session()->flash('message', 'Export started in background! You will receive a notification when ready.');
            return;
        }

        // Direct Download
        $filename = 'diary_' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.pdf';

        $pdf = $service->generatePdf(auth()->id(), $this->startDate, $this->endDate, $options);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }

    public function render()
    {
        return view('livewire.export-manager');
    }
}
