<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendInactivityReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-inactivity-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enabled = \App\Services\SystemConfig::get('reminder_enabled', false);
        if (!$enabled) {
            $this->info('Reminders are disabled.');
            return;
        }

        $days = (int) \App\Services\SystemConfig::get('reminder_days', 3);
        $thresholdDate = now()->subDays($days)->format('Y-m-d');

        $this->info("Checking for users inactive since {$thresholdDate}...");

        $users = \App\Models\User::whereDoesntHave('diaryEntries', function ($query) use ($thresholdDate) {
            $query->where('date', '>=', $thresholdDate);
        })->get();

        foreach ($users as $user) {
            // Optional: Check if we already sent one recently to avoid spam?
            // For MVP: Just send.

            $this->info("Sending reminder to: {$user->email}");
            $user->notify(new \App\Notifications\InactivityNotification());
        }

        $this->info('Done.');
    }
}
