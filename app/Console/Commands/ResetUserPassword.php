<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:password-reset {email} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the password for a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        if (!$password) {
            $password = Str::random(12);
            $this->info("No password provided, generated: {$password}");
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password for user {$email} has been reset successfully.");

        return 0;
    }
}
