<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoodLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MoodController extends Controller
{
    public function getWeeklySummary(Request $request)
    {
        $user = $request->user();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $logs = MoodLog::where('user_id', $user->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get()
            ->keyBy('date');

        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $summary = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $log = $logs->get($currentDate);

            $summary[] = [
                'day_name' => $days[$i],
                'date' => $currentDate,
                'mood_code' => $log ? $log->mood_code : null,
                'id' => $log ? $log->id : null,
            ];
        }

        return response()->json(['success' => true, 'data' => $summary]);
    }

    public function store(Request $request)
    {
        $request->validate(['mood_code' => 'required|string']);

        $user = $request->user();
        $today = Carbon::now()->format('Y-m-d');

        $mood = MoodLog::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['mood_code' => $request->mood_code]
        );

        return response()->json([
            'success' => true,
            'message' => 'Mood hari ini berhasil diperbarui',
            'data' => $mood
        ]);
    }

    public function destroy($id)
    {
        $log = MoodLog::findOrFail($id);
        $log->delete();

        return response()->json(['success' => true, 'message' => 'Riwayat mood dihapus']);
    }
}