<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MedicationController extends Controller {
    
    // Tampil di Beranda (Filter hari ini)
    public function getDailySchedule(Request $request) {
        $today = Carbon::now()->format('Y-m-d');
        
        $medications = Medication::where('user_id', auth()->id())
            ->get()
            ->map(function ($med) use ($today) {
                $log = MedicationLog::where('medication_id', $med->id)
                    ->where('date', $today)
                    ->first();

                return [
                    'id' => $med->id,
                    'name' => $med->name,
                    'time' => Carbon::parse($med->reminder_time)->format('H:i'),
                    'status' => $log ? $log->status : 'pending',
                ];
            });

        return response()->json(['success' => true, 'data' => $medications]);
    }

    // Tambah Obat Baru
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'time' => 'required' 
        ]);

        $medication = Medication::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'reminder_time' => $request->time
        ]);

        UserActivity::log('medication', 'Menambahkan obat "' . $request->name . '"');

        return response()->json(['success' => true, 'data' => $medication]);
    }

    // Update Status: "Tanda" (taken) atau "Batal" (skipped)
    public function updateStatus(Request $request, $id) {
        $request->validate(['status' => 'required|in:taken,skipped']);
        $today = Carbon::now()->format('Y-m-d');

        $log = MedicationLog::updateOrCreate(
            ['medication_id' => $id, 'date' => $today],
            ['status' => $request->status]
        );

        return response()->json(['success' => true, 'message' => 'Status diperbarui']);
    }

    // Hapus Obat
    public function destroy($id) {
        Medication::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json(['success' => true, 'message' => 'Obat berhasil dihapus']);
    }
}