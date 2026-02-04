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
        $user = $request->user();
        $today = now()->toDateString();

        $medications = Medication::where('user_id', $user->id)
            ->where(function ($query) use ($today) {
                $query->whereDate('created_at', $today)
                    ->orWhere('is_everyday', true);
            })
            ->with([
                    'logs' => function ($q) use ($today) {
                        $q->whereDate('date', $today);
                    }
                ])
            ->get()
            ->map(function ($med) {
                $log = $med->logs->first();
                return [
                    'id' => $med->id,
                    'name' => $med->name,
                    'time' => \Carbon\Carbon::parse($med->reminder_time)->format('H:i'),
                    'is_everyday' => $med->is_everyday,
                    'status' => $log ? $log->status : 'pending',
                ];
            });

        return response()->json(['success' => true, 'data' => $medications]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'time' => 'required',
            'is_everyday' => 'required|boolean'
        ]);

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