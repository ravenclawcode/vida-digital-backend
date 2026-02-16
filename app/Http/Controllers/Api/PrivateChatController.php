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
                $q->where(function ($inner) use ($user, $contact) {
                    $inner->where('sender_id', $user->id)->where('receiver_id', $contact->id);
                })->orWhere(function ($inner) use ($user, $contact) {
                    $inner->where('sender_id', $contact->id)->where('receiver_id', $user->id);
                });
            })
                ->where(function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('sender_id', $user->id)->where('deleted_by_sender', false);
                    })->orWhere(function ($sub) use ($user) {
                        $sub->where('receiver_id', $user->id)->where('deleted_by_receiver', false);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->first();

            $displayMessage = 'Ketuk untuk memulai...';
            if ($lastMsg) {
                if ($lastMsg->is_deleted_everyone) {
                    $displayMessage = ($lastMsg->sender_id == $user->id)
                        ? 'Anda menghapus pesan ini'
                        : 'Pesan ini telah dihapus';
                } else {
                    $displayMessage = $lastMsg->message;
                }
            }

            $unreadCount = PrivateMessage::where('sender_id', $contact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->where('deleted_by_receiver', false)
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
                'last_message' => $displayMessage,
                'last_message_at' => $lastMsg ? $lastMsg->created_at->toIso8601String() : $contact->updated_at->toIso8601String(),
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
        $user_id = (string) Auth::id();

        PrivateMessage::where('sender_id', $receiver_id)
            ->where('receiver_id', $user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return PrivateMessage::where(function ($q) use ($user_id, $receiver_id) {
            $q->where(function ($inner) use ($user_id, $receiver_id) {
                $inner->where('sender_id', $user_id)->where('receiver_id', $receiver_id);
            })->orWhere(function ($inner) use ($user_id, $receiver_id) {
                $inner->where('sender_id', $receiver_id)->where('receiver_id', $user_id);
            });
        })
            ->where(function ($q) use ($user_id) {
                $q->where(function ($sub) use ($user_id) {
                    $sub->where('sender_id', $user_id)->where('deleted_by_sender', false);
                })->orWhere(function ($sub) use ($user_id) {
                    $sub->where('receiver_id', $user_id)->where('deleted_by_receiver', false);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function destroyMessages($other_user_id)
    {
        $userId = (string) Auth::id();

        PrivateMessage::where(function ($q) use ($userId, $other_user_id) {
            $q->where('sender_id', $userId)->where('receiver_id', $other_user_id);
        })->orWhere(function ($q) use ($userId, $other_user_id) {
            $q->where('sender_id', $other_user_id)->where('receiver_id', $userId);
        })->chunk(100, function ($messages) use ($userId) {
            /** @var PrivateMessage $message */
            foreach ($messages as $message) {
                if ((string)$message->sender_id === $userId) {
                    $message->deleted_by_sender = true;
                }
                if ((string)$message->receiver_id === $userId) {
                    $message->deleted_by_receiver = true;
                }
                $message->save();
            }
        });

        return response()->json(['success' => true, 'message' => 'Riwayat chat berhasil dibersihkan untuk Anda']);
    }

    public function deleteSingleMessage(Request $request, $id)
    {
        /** @var PrivateMessage $message */
        $message = PrivateMessage::where('id', $id)->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Pesan tidak ditemukan'], 404);
        }

        $userId = (string) Auth::id();
        $type = $request->input('type');

        if ($type === 'everyone') {
            if ((string)$message->sender_id === $userId) {
                $message->is_deleted_everyone = true;
                $message->save();
            }
        } else {
            if ((string)$message->sender_id === $userId) {
                $message->deleted_by_sender = true;
            } elseif ((string)$message->receiver_id === $userId) {
                $message->deleted_by_receiver = true;
            }
            $message->save();
        }

        return response()->json(['success' => true, 'message' => 'Berhasil dihapus']);
    }
}
