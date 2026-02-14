<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateChatController extends Controller
{
    public function getContacts()
    {
        $user = Auth::user();

        \Carbon\Carbon::setLocale('id');

        if ($user->role_id == 3) {
            $contacts = User::where('role_id', 2)->get();
        } else {
            $contacts = User::whereHas('messagesSent', function ($q) use ($user) {
                $q->where('receiver_id', $user->id);
            })->orWhereHas('messagesReceived', function ($q) use ($user) {
                $q->where('sender_id', $user->id);
            })->where('id', '!=', $user->id)->get();
        }

        return response()->json($contacts->map(function ($contact) use ($user) {
            $lastMsg = PrivateMessage::where(function ($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
            })->orWhere(function ($q) use ($user, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
            })->orderBy('created_at', 'desc')->first();

            $unreadCount = PrivateMessage::where('sender_id', $contact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            $statusText = $contact->is_online
                ? 'Online'
                : ($contact->last_seen
                    ? $contact->last_seen->diffForHumans()
                    : $contact->updated_at->diffForHumans());

            return [
                'id' => (string) $contact->id,
                'username' => $contact->username,
                'profile_photo_url' => $contact->profile_photo_url,
                'last_message' => $lastMsg ? $lastMsg->message : 'Ketuk untuk memulai...',
                'last_message_time' => $lastMsg ? $lastMsg->created_at->format('H:i') : '',
                'unread_count' => (int) $unreadCount,
                'is_online' => (bool) $contact->is_online,
                'last_seen_display' => $statusText,
            ];
        }));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        try {
            $chat = PrivateMessage::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'is_read' => false
            ]);

            return response()->json($chat->fresh(), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengirim pesan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getMessages($receiver_id)
    {
        $user_id = Auth::id();

        PrivateMessage::where('sender_id', $receiver_id)
            ->where('receiver_id', $user_id)
            ->update(['is_read' => true]);

        $messages = PrivateMessage::where(function ($q) use ($user_id, $receiver_id) {
            $q->where('sender_id', $user_id)->where('receiver_id', $receiver_id);
        })->orWhere(function ($q) use ($user_id, $receiver_id) {
            $q->where('sender_id', $receiver_id)->where('receiver_id', $user_id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function destroyMessages($other_user_id)
    {
        $user_id = Auth::id();

        PrivateMessage::where(function ($q) use ($user_id, $other_user_id) {
            $q->where('sender_id', $user_id)->where('receiver_id', $other_user_id);
        })->orWhere(function ($q) use ($user_id, $other_user_id) {
            $q->where('sender_id', $other_user_id)->where('receiver_id', $user_id);
        })->delete();

        return response()->json(['message' => 'Chat deleted']);
    }

    public function deleteSingleMessage(Request $request, $id)
    {
        $message = PrivateMessage::findOrFail($id);
        $userId = Auth::id();
        $type = $request->input('type');

        if ($type === 'everyone') {
            if ($message->sender_id === $userId) {
                $message->update(['is_deleted_everyone' => true]);
            }
        } else {
            if ($message->sender_id === $userId) {
                $message->update(['deleted_by_sender' => true]);
            } else {
                $message->update(['deleted_by_receiver' => true]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Berhasil dihapus']);
    }
}
