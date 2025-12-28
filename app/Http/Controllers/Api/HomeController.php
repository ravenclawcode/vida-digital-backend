<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use App\Models\MoodLog;
use App\Models\Medication;
use App\Models\MedicationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function getDashboard(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::now()->format('Y-m-d');

        // 1. Ambil Jadwal Obat Hari Ini
        $medications = Medication::where('user_id', $user->id)
            ->get()
            ->map(function ($med) use ($today) {
                $log = MedicationLog::where('medication_id', $med->id)
                    ->where('date', $today)
                    ->first();

                return [
                    'id' => $med->id,
                    'name' => $med->name,
                    'time' => Carbon::parse($med->reminder_time)->format('H:i'),
                    'status' => $log ? $log->status : 'pending', // pending, taken, skipped
                ];
            });

        // 2. Ambil Data Mood Mingguan (Senin - Minggu)
        $startOfWeek = Carbon::now()->startOfWeek();
        $moodLogs = MoodLog::where('user_id', $user->id)
            ->whereBetween('date', [$startOfWeek, Carbon::now()->endOfWeek()])
            ->get()
            ->keyBy('date');

        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $moodSummary = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $log = $moodLogs->get($currentDate);

            $moodSummary[] = [
                'day_name' => $days[$i],
                'date' => $currentDate,
                'mood_code' => $log ? $log->mood_code : null,
            ];
        }

        // 3. Ambil 3 Aktivitas Terakhir
        $activities = UserActivity::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($act) => [
                'type' => $act->type,
                'description' => $act->description,
                'time_ago' => $act->created_at->diffForHumans(),
            ]);

        // 4. Response Gabungan untuk Beranda
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'username' => $user->username,
                    'profile_photo_url' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                ],
                'daily_medications' => $medications,
                'mood_tracker' => $moodSummary,
                'recent_activities' => $activities,
            ]
        ]);
    }
}