<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisorChatController extends Controller
{
    public function index(Request $request)
    {
        $counselors = User::where('role_id', 2)->orderBy('username', 'asc')->get();
        $selectedCounselorId = $request->get('counselor_id');
        $search = $request->get('search');

        $latestMessageIds = PrivateMessage::selectRaw('MAX(id) as id')
            ->where(function ($q) {})
            ->groupBy(\DB::raw('LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)'))
            ->pluck('id');

        $sessionQuery = PrivateMessage::with(['sender', 'receiver'])
            ->whereIn('id', $latestMessageIds);

        if ($selectedCounselorId) {
            $sessionQuery->where(function ($q) use ($selectedCounselorId) {
                $q->where('sender_id', $selectedCounselorId)
                    ->orWhere('receiver_id', $selectedCounselorId);
            });
        }

        if (!empty($search)) {
            $sessionQuery->whereHas('sender', function ($q) use ($search) {
                $q->where('role_id', 3)->where('username', 'LIKE', '%' . $search . '%');
            })->orWhereHas('receiver', function ($q) use ($search) {
                $q->where('role_id', 3)->where('username', 'LIKE', '%' . $search . '%');
            });
        }

        $sessions = $sessionQuery->latest()->get()->map(function ($msg) {
            $patient = ($msg->sender->role_id == 3) ? $msg->sender : $msg->receiver;
            $counselor = ($msg->sender->role_id == 2) ? $msg->sender : $msg->receiver;

            $totalMessages = PrivateMessage::where(function ($q) use ($patient, $counselor) {
                $q->where('sender_id', $patient->id)->where('receiver_id', $counselor->id);
            })->orWhere(function ($q) use ($patient, $counselor) {
                $q->where('sender_id', $counselor->id)->where('receiver_id', $patient->id);
            })->count();

            return (object) [
                'patient_id' => $patient->id,
                'patient_name' => $patient->username,
                'counselor_id' => $counselor->id,
                'counselor_name' => $counselor->username,
                'last_message' => $msg->message,
                'last_time' => $msg->created_at->format('H:i'),
                'total_messages' => $totalMessages
            ];
        });

        $selectedChat = null;
        $activePatient = null;
        $activeCounselorId = $request->get('active_counselor_id');

        if ($request->has('user_id') && $activeCounselorId) {
            $activePatient = User::find($request->user_id);
            if ($activePatient) {
                $selectedChat = PrivateMessage::where(function ($q) use ($activePatient, $activeCounselorId) {
                    $q->where('sender_id', $activePatient->id)->where('receiver_id', $activeCounselorId);
                })->orWhere(function ($q) use ($activePatient, $activeCounselorId) {
                    $q->where('sender_id', $activeCounselorId)->where('receiver_id', $activePatient->id);
                })
                    ->with('sender')
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('supervisor.monitoring.index', compact(
            'counselors',
            'sessions',
            'selectedChat',
            'selectedCounselorId',
            'activePatient',
            'search',
            'activeCounselorId'
        ));
    }
}
