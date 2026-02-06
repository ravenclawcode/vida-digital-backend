<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
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
}
