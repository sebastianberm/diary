<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncImmich extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-immich {date? : Date to sync (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync daily photos from Immich and download missing assets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->argument('date') ?? now()->format('Y-m-d');
        $this->info("Syncing Immich for date: {$date}");

        // 1. Fetch from Immich (Just to check what exists if we wanted to auto-add)
        // For now, key requirement is "background updates" or "download missing".
        // Let's focus on downloading missing local files for EXISTING entries first.

        $photos = \App\Models\EntryPhoto::whereNull('local_path')->get();
        $count = $photos->count();

        $this->info("Found {$count} photos without local backup.");

        $photos = \App\Models\EntryPhoto::whereNull('local_path')->get();
        $count = $photos->count();

        $shouldCopy = \App\Services\SystemConfig::get('immich_copy_photos', false);
        if (!$shouldCopy) {
            $this->info("Local backup disabled in settings. Skipping download.");
            return;
        }

        $this->info("Found {$count} photos without local backup.");

        $service = new \App\Services\ImmichService();
        $bar = $this->output->createProgressBar($count);

        if (!file_exists(storage_path('app/public/photos'))) {
            mkdir(storage_path('app/public/photos'), 0755, true);
        }

        foreach ($photos as $photo) {
            $path = $service->downloadAsset($photo->immich_asset_id);
            if ($path) {
                $photo->update(['local_path' => $path]);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sync complete.');
    }
}
