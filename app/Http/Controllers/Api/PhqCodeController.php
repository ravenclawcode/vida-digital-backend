<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhqCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PhqCodeController extends Controller
{
    public function generate(Request $request)
    {
        $code = 'PHQ9-' . strtoupper(Str::random(8));

        $phqCode = PhqCode::create([
            'token_code' => $code,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $phqCode
        ], 201);
    }

    public function validateCode(Request $request)
    {
        $request->validate(['token_code' => 'required|string']);

        $code = PhqCode::where('token_code', $request->token_code)
            ->where('is_used', false)
            ->first();

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Kode akses tidak valid atau sudah kadaluarsa.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode valid. Selamat mengerjakan tes.',
            'data' => $code
        ]);
    }

    public function markAsUsed(Request $request)
    {
        $request->validate(['token_code' => 'required|string']);

        $code = PhqCode::where('token_code', $request->token_code)->first();

        if ($code) {
            $code->is_used = true;
            $code->save();

            return response()->json([
                'success' => true,
                'message' => 'Kode berhasil digunakan.'
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}
