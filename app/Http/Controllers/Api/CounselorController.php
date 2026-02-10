<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SoapNote;
use App\Models\MedicationLog;
use App\Models\MoodLog;
use App\Models\PhqResult;

use Illuminate\Http\Request;

class CounselorController extends Controller
{
    public function index()
    {
        $counselors = User::where('role_id', 2)
            ->select('id', 'username', 'email', 'created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $counselors
        ]);
    }

    public function getPatients(Request $request)
    {
        $patients = User::where('role_id', 3)->get()->map(function ($patient) {
            $totalLogs = MedicationLog::whereHas('medication', function ($q) use ($patient) {
                $q->where('user_id', $patient->id);
            })->where('date', '>=', now()->subDays(7))->count();

            $takenLogs = MedicationLog::whereHas('medication', function ($q) use ($patient) {
                $q->where('user_id', $patient->id);
            })->where('date', '>=', now()->subDays(7))
                ->where('status', 'taken')->count();

            $progress = $totalLogs > 0 ? ($takenLogs / $totalLogs) : 0;

            return [
                'id' => $patient->id,
                'name' => $patient->username,
                'status' => $this->determineStatus($patient),
                'progress' => $progress,
                'unread' => 0,
            ];
        });

        return response()->json(['success' => true, 'data' => $patients]);
    }

    public function storeSoap(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'subjective' => 'required',
            'objective' => 'required',
            'assessment' => 'required',
            'plan' => 'required',
        ]);

        $soap = SoapNote::create([
            'counselor_id' => $request->user()->id,
            'patient_id' => $request->patient_id,
            'subjective' => $request->subjective,
            'objective' => $request->objective,
            'assessment' => $request->assessment,
            'plan' => $request->plan,
        ]);

        return response()->json(['success' => true, 'message' => 'SOAP disimpan'], 201);
    }

    public function show($id)
    {
        $patient = User::where('id', $id)->where('role_id', 3)->firstOrFail();

        $medicationLogs = MedicationLog::whereHas('medication', function ($q) use ($patient) {
            $q->where('user_id', $patient->id);
        })->where('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'medication_name' => $log->medication->name,
                    'is_taken' => $log->status === 'taken' ? 1 : 0,
                    'date' => $log->date
                ];
            });

        $moods = MoodLog::where('user_id', $patient->id)
            ->where('date', '>=', now()->subDays(6))
            ->orderBy('date', 'asc')
            ->get();

        $weeklyMoods = [0, 0, 0, 0, 0, 0, 0];
        foreach ($moods as $mood) {
            $dayIndex = date('N', strtotime($mood->date)) - 1;
            if ($dayIndex >= 0 && $dayIndex < 7) {
                $weeklyMoods[$dayIndex] = (int)$mood->mood_code;
            }
        }

        $phqHistory = PhqResult::where('user_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($phq) {
                return [
                    'score' => $phq->total_score,
                    'date' => $phq->created_at->format('d M'),
                    'category' => $phq->category,
                ];
            });

        $latestPhq = $phqHistory->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $patient->id,
                'name' => $patient->username,
                'progress' => $this->calculateProgress($patient),
                'status' => $this->determineStatus($patient),
                'medication_logs' => $medicationLogs,
                'weekly_moods' => $weeklyMoods,
                'last_phq_score' => $latestPhq ? $latestPhq['score'] : 0,
                'last_phq_date' => $latestPhq ? $latestPhq['date'] : '-',
                'phq_history' => $phqHistory,
            ]
        ]);
    }

    private function calculateProgress($patient)
    {
        $totalLogs = MedicationLog::whereHas('medication', function ($q) use ($patient) {
            $q->where('user_id', $patient->id);
        })->where('date', '>=', now()->subDays(7))->count();

        $takenLogs = MedicationLog::whereHas('medication', function ($q) use ($patient) {
            $q->where('user_id', $patient->id);
        })->where('date', '>=', now()->subDays(7))
            ->where('status', 'taken')->count();

        return $totalLogs > 0 ? ($takenLogs / $totalLogs) : 0;
    }

    private function determineStatus($patient)
    {
        $progress = $this->calculateProgress($patient);
        $latestPhq = PhqResult::where('user_id', $patient->id)->orderBy('created_at', 'desc')->first();

        if ($latestPhq && $latestPhq->total_score >= 15) return 'Kritis';
        if ($progress > 0.8) return 'Sangat Baik';
        if ($progress > 0.5) return 'Baik';
        return 'Perlu Perhatian';
    }
}
