<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PrivateMessage;
use App\Models\SoapNote;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        return view('supervisor.dashboard.index');
    }

    public function monitoringChat(Request $request)
    {
        $sessions = User::where('role_id', 3)->get();

        $selectedChat = null;
        if ($request->user_id) {
            $selectedChat = PrivateMessage::where('sender_id', $request->user_id)
                ->orWhere('receiver_id', $request->user_id)
                ->orderBy('created_at', 'asc')->get();
        }

        return view('supervisor.monitoring.index', compact('sessions', 'selectedChat'));
    }

    public function catatanSoap()
    {
        $soaps = SoapNote::with(['patient', 'counselor'])->latest()->get();
        return view('supervisor.soap.index', compact('soaps'));
    }
}
