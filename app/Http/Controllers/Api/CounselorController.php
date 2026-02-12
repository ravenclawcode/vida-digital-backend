<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SoapNote;
use App\Models\MedicationLog;
use App\Models\MoodLog;
use App\Models\PhqResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class CounselorController extends Controller
{
    public function index(): JsonResponse
    {
        $counselors = User::where('role_id', 2)
            ->select('id', 'username', 'email', 'created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $counselors
        ]);
    }

    public function getPatients(Request $request): JsonResponse
    {
        Carbon::setLocale('id');
        $counselorId = Auth::id();

        $patients = User::where('role_id', 3)
            ->get()
            ->map(fn(User $patient) => $this->mapPatientList($patient, $counselorId));


        return response()->json([
            'success' => true,
            'data' => $patients
        ]);
    }

    public function storeSoap(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'subjective' => 'required',
            'objective' => 'required',
            'assessment' => 'required',
            'plan' => 'required',
        ]);

        SoapNote::create([
            'counselor_id' => $request->user()->id,
            'patient_id' => $request->patient_id,
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'assessment' => $request->assessment,
            'plan' => $request->plan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SOAP disimpan'
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $patient = $this->getPatient($id);

        return response()->json([
            'success' => true,
            'data' => $this->buildPatientDetail($patient)
        ]);
    }

    private function getPatient(int $id): User
    {
        return User::where('id', $id)
            ->where('role_id', 3)
            ->firstOrFail();
    }

    private function buildPatientDetail(User $patient): array
    {
        Carbon::setLocale('id');

        $phqHistory = $this->getPhqHistory($patient);
        $latestPhq  = $phqHistory->first();

        return [
            'id' => $patient->id,
            'name' => $patient->username,
            'is_online' => (bool) $patient->is_online,
            'last_seen_display' => $this->getLastSeenText($patient),

            'progress' => $this->calculateProgress($patient),
            'status' => $this->determineStatus($patient),
            'medication_logs' => $this->getMedicationLogs($patient),
            'weekly_moods' => $this->getWeeklyMoods($patient),

            'last_phq_score' => $latestPhq['score'] ?? 0,
            'last_phq_date' => $latestPhq['date'] ?? '-',
            'phq_history' => $phqHistory,
        ];
    }

    private function mapPatientList(User $patient, int $counselorId): array
    {
        return [
            'id' => $patient->id,
            'name' => $patient->username,
            'status' => $this->determineStatus($patient),
            'progress' => $this->calculateProgress($patient),
            'unread' => $this->getUnreadCount($patient, $counselorId),
            'is_online' => (bool) $patient->is_online,
            'last_seen_display' => $patient->is_online
                ? 'Online'
                : ($patient->last_seen ? $patient->last_seen->diffForHumans() : 'Offline'),
        ];
    }

    private function getUnreadCount(User $patient, int $counselorId): int
    {
        return \App\Models\PrivateMessage::where('sender_id', $patient->id)
            ->where('receiver_id', $counselorId)
            ->where('is_read', false)
            ->count();
    }

    private function getMedicationLogs(User $patient)
    {
        return MedicationLog::whereHas(
            'medication',
            fn($q) =>
            $q->where('user_id', $patient->id)
        )
            ->where('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn($log) => [
                'medication_name' => $log->medication->name,
                'is_taken' => $log->status === 'taken' ? 1 : 0,
                'date' => $log->date
            ]);
    }

    private function getWeeklyMoods(User $patient): array
    {
        $weeklyMoods = array_fill(0, 7, 0);

        $moods = MoodLog::where('user_id', $patient->id)
            ->where('date', '>=', now()->subDays(6))
            ->orderBy('date', 'asc')
            ->get();

        foreach ($moods as $mood) {
            $index = date('N', strtotime($mood->date)) - 1;
            if ($index >= 0 && $index < 7) {
                $weeklyMoods[$index] = (int) $mood->mood_code;
            }
        }

        return $weeklyMoods;
    }

    private function getPhqHistory(User $patient)
    {
        return PhqResult::where('user_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($phq) => [
                'score' => $phq->total_score,
                'date' => $phq->created_at->format('d M'),
                'category' => $phq->category,
            ]);
    }

    private function getLastSeenText(User $patient): string
    {
        if ($patient->is_online) {
            return 'Online';
        }

        return $patient->last_seen
            ? $patient->last_seen->diffForHumans()
            : $patient->updated_at->diffForHumans();
    }

    private function calculateProgress(User $patient): float
    {
        $totalLogs = MedicationLog::whereHas(
            'medication',
            fn($q) =>
            $q->where('user_id', $patient->id)
        )
            ->where('date', '>=', now()->subDays(7))
            ->count();

        $takenLogs = MedicationLog::whereHas(
            'medication',
            fn($q) =>
            $q->where('user_id', $patient->id)
        )
            ->where('date', '>=', now()->subDays(7))
            ->where('status', 'taken')
            ->count();

        return $totalLogs > 0 ? ($takenLogs / $totalLogs) : 0;
    }

    private function determineStatus(User $patient): string
    {
        $progress = $this->calculateProgress($patient);

        $latestPhq = PhqResult::where('user_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestPhq && $latestPhq->total_score >= 15) return 'Kritis';
        if ($progress > 0.8) return 'Sangat Baik';
        if ($progress > 0.5) return 'Baik';

        return 'Perlu Perhatian';
    }
}
