<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MoodLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $lastSevenDays = Carbon::now()->subDays(7);

        $totalMoods = MoodLog::where('date', '>=', $lastSevenDays)->count();

        $moodStats = MoodLog::where('date', '>=', $lastSevenDays)
            ->select('mood_code', DB::raw('count(*) as total'))
            ->groupBy('mood_code')
            ->get();

        return view('dashboard', compact('moodStats', 'totalMoods'));
    }
}
