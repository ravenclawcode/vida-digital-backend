<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\MedicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MedicationController extends Controller
{

    public function getDailySchedule(Request $request)
    {
        try {
            $user = $request->user();
            $today = now()->toDateString();
            $now = now()->timezone('Asia/Makassar');

            $medications = Medication::where('user_id', $user->id)
                ->where(function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->orWhere('is_everyday', true);
                })
                ->with(['logs' => function ($q) use ($today) {
                    $q->whereDate('date', $today);
                }])
                ->get();

            $data = $medications->map(function ($med) use ($today, $now) {
                $log = $med->logs->first();
                $status = $log ? $log->status : 'pending';

                $medTime = \Carbon\Carbon::parse($med->reminder_time, 'Asia/Makassar');

                if ($status === 'pending' && $now->greaterThan($medTime)) {
                    try {
                        MedicationLog::updateOrCreate(
                            ['medication_id' => $med->id, 'date' => $today],
                            ['status' => 'missed']
                        );
                        $status = 'missed';
                    } catch (\Exception $e) {
                        $status = 'missed';
                    }
                }

                return [
                    'id' => $med->id,
                    'name' => $med->name,
                    'time' => $medTime->format('H:i'),
                    'is_everyday' => $med->is_everyday,
                    'status' => $status,
                ];
            })
                ->filter(function ($item) {
                    return $item['status'] !== 'missed';
                })
                ->values();

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'time' => 'required',
            'is_everyday' => 'required|boolean'
        ]);

        $now = now()->timezone('Asia/Makassar');
        $inputTime = \Carbon\Carbon::createFromFormat('H:i', $request->time, 'Asia/Makassar');

        if ($inputTime->lessThan($now)) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu sudah terlewat. Silahkan pilih waktu yang akan datang.'
            ], 422);
        }

        $medication = Medication::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'reminder_time' => $request->time,
            'is_everyday' => $request->is_everyday,
        ]);

        return response()->json(['success' => true, 'data' => $medication]);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $status = $request->status;
        $today = now()->toDateString();

        $log = MedicationLog::updateOrCreate(
            ['medication_id' => $id, 'date' => $today],
            ['status' => $status]
        );

        return response()->json(['success' => true, 'log' => $log]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'time' => 'required',
            'is_everyday' => 'required|boolean'
        ]);

        $medication = Medication::findOrFail($id);
        $medication->update([
            'name' => $request->name,
            'reminder_time' => $request->time,
            'is_everyday' => $request->is_everyday,
        ]);

        return response()->json(['success' => true, 'data' => $medication]);
    }

    public function destroy(Request $request, $id)
    {
        Medication::where('id', $id)->where('user_id', $request->user()->id)->delete();
        return response()->json(['success' => true, 'message' => 'Obat berhasil dihapus']);
    }
}
