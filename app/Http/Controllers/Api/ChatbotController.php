<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\Response;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $request->validate(['message' => 'required|string|max:1000']);

        if ($user->last_chat_at && Carbon::now()->diffInHours($user->last_chat_at) >= 24) {
            $user->update(['daily_chat_count' => 0]);
        }

        if ($user->daily_chat_count >= 20) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuota harian habis.'
            ], 429);
        }

        return DB::transaction(function () use ($user, $request) {

            ChatMessage::create([
                'user_id' => $user->id,
                'message' => $request->message,
                'sender' => 'user'
            ]);

            try {
                $apiKey = env('GEMINI_API_KEY');

                if (!$apiKey) {
                    throw new \Exception("API Key tidak ditemukan di file .env");
                }

                $prompt = "Nama kamu Teman Hati. Jawab dengan empati dan ramah. Pesan user: "
                    . $request->message;

                $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent"
                    . "?key=" . trim($apiKey);

                /** @var Response $response */
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                    ->timeout(30)
                    ->post($url, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ]
                    ]);

                if (!$response->successful()) {
                    Log::error('Google API Detail Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    throw new \Exception(
                        "Gagal menghubungi AI. Status: " . $response->status()
                    );
                }

                $data = $response->json();

                $botResponse =
                    $data['candidates'][0]['content']['parts'][0]['text']
                    ?? "Maaf, saat ini aku belum bisa memproses pesan itu.";

                $user->increment('daily_chat_count');
                $user->update(['last_chat_at' => now()]);

                $botChat = ChatMessage::create([
                    'user_id' => $user->id,
                    'message' => $botResponse,
                    'sender' => 'bot'
                ]);

                return response()->json([
                    'sender' => 'bot',
                    'message' => $botResponse,
                    'time' => now()->toIso8601String(),
                ]);
            } catch (\Exception $e) {
                Log::error('Chatbot Error: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function getHistory()
    {
        $history = ChatMessage::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($chat) => [
                'message' => $chat->message,
                'sender' => $chat->sender,
                'time' => $chat->created_at
                    ? $chat->created_at->format('H.i')
                    : now()->format('H.i')
            ]);

        return response()->json($history);
    }

    public function clearHistory()
    {
        ChatMessage::where('user_id', Auth::user()->id)->delete();
        return response()->json(['message' => 'Chat dibersihkan']);
    }
}
