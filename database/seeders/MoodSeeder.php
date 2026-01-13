<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $moods = [
            ['name' => 'mood.happy', 'icon' => '😊'],
            ['name' => 'mood.excited', 'icon' => '🤩'],
            ['name' => 'mood.grateful', 'icon' => '🙏'],
            ['name' => 'mood.relaxed', 'icon' => '😌'],
            ['name' => 'mood.neutral', 'icon' => '😐'],
            ['name' => 'mood.tired', 'icon' => '😴'],
            ['name' => 'mood.sad', 'icon' => '😢'],
            ['name' => 'mood.angry', 'icon' => '😠'],
            ['name' => 'mood.stressed', 'icon' => '😫'],
            ['name' => 'mood.sick', 'icon' => '🤒'],
        ];

        foreach ($moods as $mood) {
            \App\Models\Mood::firstOrCreate(
                ['name' => $mood['name']],
                ['icon' => $mood['icon']]
            );
        }
    }
}
