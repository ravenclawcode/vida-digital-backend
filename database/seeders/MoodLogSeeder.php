<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MoodLog;
use App\Models\User;
use Carbon\Carbon;

class MoodLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('Tidak ada user ditemukan. Jalankan DatabaseSeeder dulu!');
            return;
        }

        $moods = ['senang', 'tenang', 'biasa', 'sedih', 'cemas', 'lelah'];

        foreach ($users as $user) {
            for ($i = 0; $i < 7; $i++) {
                if (rand(0, 1)) {
                    MoodLog::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'date' => Carbon::now()->subDays($i)->format('Y-m-d')
                        ],
                        [
                            'mood_code' => $moods[array_rand($moods)]
                        ]
                    );
                }
            }
        }

        $this->command->info('Data contoh Mood Tracker berhasil dibuat!');
    }
}