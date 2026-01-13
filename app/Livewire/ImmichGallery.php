<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\DiaryEntry;
use App\Models\EntryPhoto;
use App\Services\ImmichService;
use Carbon\Carbon;
use Livewire\Attributes\On;

class ImmichGallery extends Component
{
    public $date;
    public $assets = [];
    public $selectedAssets = []; // [immich_id => ['caption' => '', 'selected' => true]]
    public $isConnected = false;

    public function mount()
    {
        $this->date = Carbon::today()->format('Y-m-d');
        $this->checkConnection();
        $this->loadPhotos();
    }

    #[On('date-changed')]
    public function updateDate($date)
    {
        $this->date = $date;
        $this->loadPhotos();
    }

    public function checkConnection()
    {
        $url = \App\Services\SystemConfig::get('immich_url');
        $key = \App\Services\SystemConfig::get('immich_key');
        $this->isConnected = !empty($url) && !empty($key);
    }

    public function loadPhotos()
    {
        if (!$this->isConnected)
            return;

        $service = new ImmichService();
        $this->assets = $service->getDailyAssets($this->date);

        // Load existing selections for this day
        $entry = DiaryEntry::where('user_id', auth()->id())
            ->where('date', $this->date)
            ->first();

        $this->selectedAssets = [];

        if ($entry) {
            foreach ($entry->photos as $photo) {
                $this->selectedAssets[$photo->immich_asset_id] = [
                    'selected' => true,
                    'caption' => $photo->caption
                ];
            }
        }
    }

    public function toggleAsset($id)
    {
        if (isset($this->selectedAssets[$id])) {
            unset($this->selectedAssets[$id]);
        } else {
            $this->selectedAssets[$id] = [
                'selected' => true,
                'caption' => ''
            ];
        }
    }

    public function refresh()
    {
        $this->assets = []; // Clear cache
        $this->loadPhotos();
        session()->flash('photo_message', 'Refreshed from Immich!');
    }

    public function save()
    {
        $entry = DiaryEntry::firstOrCreate(
            ['user_id' => auth()->id(), 'date' => $this->date],
            ['content' => '', 'mood_id' => null] // Default if not exists
        );

        // Sync logic: Remove photos not in selectedAssets, Add/Update others
        // 1. Get current DB asset IDs
        $currentDbIds = $entry->photos()->pluck('immich_asset_id')->toArray();
        $selectedIds = array_keys($this->selectedAssets);

        // 2. Delete removed
        $toDelete = array_diff($currentDbIds, $selectedIds);
        EntryPhoto::where('entry_id', $entry->id)
            ->whereIn('immich_asset_id', $toDelete)
            ->delete();

        // 3. Update/Create
        foreach ($this->selectedAssets as $assetId => $data) {

            // Logic to download/backup the photo if it doesn't exist locally
            // This ensures photos don't disappear if deleted from Immich
            $photo = EntryPhoto::where('entry_id', $entry->id)
                ->where('immich_asset_id', $assetId)
                ->first();

            $localPath = $photo->local_path ?? null;

            // Only download if setting is enabled AND we don't have it yet
            $shouldCopy = \App\Services\SystemConfig::get('immich_copy_photos', false);

            if ($shouldCopy && !$localPath) {
                // Trigger background download or download immediately
                $service = new ImmichService();
                if (!file_exists(storage_path('app/public/photos'))) {
                    mkdir(storage_path('app/public/photos'), 0755, true);
                }

                $localPath = $service->downloadAsset($assetId);
            }

            EntryPhoto::updateOrCreate(
                [
                    'entry_id' => $entry->id,
                    'immich_asset_id' => $assetId
                ],
                [
                    'caption' => $data['caption'],
                    'local_path' => $localPath,
                    'taken_at' => null
                ]
            );
        }

        session()->flash('photo_message', 'Photos updated!');
    }

    public function render()
    {
        return view('livewire.immich-gallery');
    }
}
