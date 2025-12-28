<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Gemini\Laravel\Facades\Gemini;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = auth()->user();
        $now = Carbon::now();
        $limit = 20;

        // 1. Reset kuota jika sudah lewat 24 jam
        if ($user->last_chat_at && $now->diffInHours($user->last_chat_at) >= 24) {
            $user->daily_chat_count = 0;
            $user->save();
        }

        // 2. Cek kuota
        if ($user->daily_chat_count >= $limit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuota harian habis. Coba lagi besok.',
            ], 429);
        }

        $request->validate(['message' => 'required|string']);

        // 3. Simpan pesan user
        ChatMessage::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'sender' => 'user'
        ]);

        try {
            // 4. Panggil Gemini AI
            $prompt = "Kamu adalah 'Teman Hati', asisten suportif di aplikasi Vida. 
                       Bantu user tentang kesehatan mental dan HIV/AIDS dengan empati. 
                       Pesan user: " . $request->message;

            $result = Gemini::geminiPro()->generateContent($prompt);
            $botResponse = $result->text();

            // 5. Update data user
            $user->increment('daily_chat_count');
            $user->update(['last_chat_at' => $now]);

            // 6. Simpan balasan bot
            $chat = ChatMessage::create([
                'user_id' => $user->id,
                'message' => $botResponse,
                'sender' => 'bot'
            ]);

            \App\Models\UserActivity::log('chatbot', 'Bercerita dengan "Teman Hati"');

            return response()->json([
                'sender' => 'bot',
                'message' => $botResponse,
                'time' => $chat->created_at->format('H.i')
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal terhubung ke AI.'], 500);
        }
    }

    public function getHistory()
    {
        $history = ChatMessage::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($chat) => [
                'message' => $chat->message,
                'sender' => $chat->sender,
                'time' => $chat->created_at->format('H.i')
            ]);

        return response()->json($history);
    }
}