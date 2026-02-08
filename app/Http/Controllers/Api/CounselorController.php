<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SoapNote;
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

    public function getPatients()
    {
        $patients = User::where('role_id', 3)->select('id', 'username')->get();
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
}
