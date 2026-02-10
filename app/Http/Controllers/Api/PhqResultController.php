<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhqResult;
use App\Models\PhqCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhqResultController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token_code' => 'required|exists:phq_codes,token_code',
            'answers' => 'required|array',
        ]);

        $totalScore = array_sum($request->answers);

        $category = $this->getCategory($totalScore);

        $result = PhqResult::create([
            'user_id' => Auth::id(),
            'total_score' => $totalScore,
            'category' => $category,
            'answers' => $request->answers,
        ]);

        PhqCode::where('token_code', $request->token_code)->update(['is_used' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Hasil tes berhasil disimpan. Kategori: ' . $category,
            'data' => $result
        ], 201);
    }

    private function getCategory($score)
    {
        if ($score <= 4) return 'Minimal';
        if ($score <= 9) return 'Ringan';
        if ($score <= 14) return 'Sedang';
        if ($score <= 19) return 'Cukup Berat';
        return 'Berat';
    }
}
