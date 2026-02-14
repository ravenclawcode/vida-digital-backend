<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SoapNote;
use App\Models\MedicationLog;
use App\Models\MoodLog;
use App\Models\PhqResult;
use App\Models\PrivateMessage;
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
    public function show($id): JsonResponse
    {
        try {
            $patient = User::where('id', $id)
                ->where('role_id', 3)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $this->buildPatientDetail($patient)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak ditemukan atau terjadi kesalahan server.'
            ], 404);
        }
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
            'counselor_id' => Auth::id(),
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
    private function mapPatientList(User $patient, $counselorId): array
    {
        $progress = $this->calculateProgress($patient);

        return [
            'id' => $patient->id,
            'name' => $patient->username,
            'profile_photo_url' => $patient->profile_photo_url,
            'status' => $this->determineStatusWithProgress($patient, $progress),
            'progress' => (float) $progress,
            'unread' => $this->getUnreadCount($patient, $counselorId),
            'is_online' => (bool) $patient->is_online,
            'last_seen_display' => $this->getLastSeenText($patient),
        ];
    }
    private function buildPatientDetail(User $patient): array
    {
        Carbon::setLocale('id');

        $phqHistory = $this->getPhqHistory($patient);
        $latestPhq  = $phqHistory->first();
        $progress   = $this->calculateProgress($patient);

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $moodWeekRange = $startOfWeek->translatedFormat('d') . '-' . $endOfWeek->translatedFormat('d M');

        return [
            'id' => $patient->id,
            'name' => $patient->username,
            'profile_photo_url' => $patient->profile_photo_url,
            'is_online' => (bool) $patient->is_online,
            'last_seen_display' => $this->getLastSeenText($patient),
            'progress' => (float) $progress,
            'status' => $this->determineStatusWithProgress($patient, $progress),
            'medication_logs' => $this->getMedicationLogs($patient),
            'weekly_moods' => $this->getWeeklyMoods($patient),
            'mood_week_range' => $moodWeekRange,
            'last_phq_score' => $latestPhq['score'] ?? 0,
            'last_phq_date' => $latestPhq['date'] ?? '-',
            'phq_history' => $phqHistory,
        ];
    }

    private function getUnreadCount(User $patient, $counselorId): int
    {
        if (!$counselorId) return 0;

        return PrivateMessage::where('sender_id', $patient->id)
            ->where('receiver_id', $counselorId)
            ->where('is_read', false)
            ->count();
    }

    private function getMedicationLogs(User $patient)
    {
        Carbon::setLocale('id');

        return MedicationLog::with('medication')
            ->whereHas('medication', fn($q) => $q->where('user_id', $patient->id))
            ->where('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn($log) => [
                'medication_name' => $log->medication->name ?? 'Obat Terhapus',
                'is_taken' => $log->status === 'taken' ? 1 : 0,
                'day_name' => Carbon::parse($log->date)->translatedFormat('l'),
                'date_formatted' => Carbon::parse($log->date)->format('d M'),
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
        $isRecentlyActive = $patient->is_online &&
            $patient->last_seen &&
            $patient->last_seen->diffInMinutes(now()) < 5;

        if ($isRecentlyActive) return 'Online';

        return $patient->last_seen ? $patient->last_seen->diffForHumans() : 'Offline';
    }

    private function calculateProgress(User $patient): float
    {
        $query = MedicationLog::whereHas('medication', fn($q) => $q->where('user_id', $patient->id))
            ->where('date', '>=', now()->subDays(7));

        $totalLogs = (clone $query)->count();

        if ($totalLogs === 0) return -1.0;

        $takenLogs = $query->where('status', 'taken')->count();

        return (float) ($takenLogs / $totalLogs);
    }

    private function determineStatusWithProgress(User $patient, float $progress): string
    {
        if ($progress === -1.0) {
            return 'Belum Ada Data';
        }

        $latestPhq = PhqResult::where('user_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestPhq && $latestPhq->total_score >= 15) {
            return 'Kritis';
        }

        $percentage = $progress * 100;

        if ($percentage >= 80) {
            return 'Sangat Baik';
        } elseif ($percentage >= 60) {
            return 'Baik';
        } elseif ($percentage >= 40) {
            return 'Perlu Perhatian';
        } else {
            return 'Kritis';
        }
    }
}
