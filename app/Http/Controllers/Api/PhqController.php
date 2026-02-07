<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhqQuestion;
use Illuminate\Http\JsonResponse;

class PhqController extends Controller
{
    /**
     * Mengambil daftar pertanyaan PHQ.
     *
     * @return JsonResponse
     */
    public function getQuestionsForApp()
    {
        $questions = PhqQuestion::orderBy('id', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Pertanyaan PHQ',
            'data'    => $questions
        ], 200);
    }
}
