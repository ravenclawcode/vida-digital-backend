<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use App\Models\MoodLog;
use App\Models\Medication;
use App\Models\MedicationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HomeController extends Controller
{
    public function getDashboard(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $this->getUserData($user),
                'daily_medications' => $this->getDailyMedications($user),
                'mood_tracker' => $this->getMoodSummary($user),
                'recent_activities' => $this->getRecentActivities($user),
            ]
        ]);
    }

    private function getUserData(User $user): array
    {
        return [
            'username' => $user->username,
            'profile_photo_url' => $user->profile_photo
                ? asset('storage/' . $user->profile_photo)
                : null,
        ];
    }

    private function getDailyMedications(User $user)
    {
        $today = Carbon::now()->format('Y-m-d');

        return Medication::where('user_id', $user->id)
            ->get()
            ->map(
                fn(Medication $med) =>
                $this->mapMedication($med, $today)
            );
    }

    private function mapMedication(Medication $med, string $today): array
    {
        $log = MedicationLog::where('medication_id', $med->id)
            ->where('date', $today)
            ->first();

        return [
            'id' => $med->id,
            'name' => $med->name,
            'time' => Carbon::parse($med->reminder_time)->format('H:i'),
            'status' => $log ? $log->status : 'pending',
        ];
    }

    private function getMoodSummary(User $user): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $moodLogs = MoodLog::where('user_id', $user->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get()
            ->keyBy('date');

        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $moodSummary = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startOfWeek->copy()
                ->addDays($i)
                ->format('Y-m-d');

            $log = $moodLogs->get($currentDate);

            $moodSummary[] = [
                'day_name' => $days[$i],
                'date' => $currentDate,
                'mood_code' => $log ? $log->mood_code : null,
            ];
        }

        return $moodSummary;
    }

    private function getRecentActivities(User $user)
    {
        return UserActivity::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn(UserActivity $act) => [
                'type' => $act->type,
                'description' => $act->description,
                'time_ago' => $act->created_at->diffForHumans(),
            ]);
    }
}
