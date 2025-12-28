<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MindfulnessAudio;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Tambahkan ini

class AudioController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');

        $audios = MindfulnessAudio::when($category, function ($q) use ($category) {
            return $q->where('category', $category);
        })->latest()->get()->map(function ($item) {

            // Logika untuk Audio URL
            $finalAudioUrl = Str::startsWith($item->audio_url, ['http://', 'https://'])
                ? $item->audio_url
                : asset($item->audio_url);

            // Logika untuk Cover URL
            if ($item->cover_url) {
                $finalCoverUrl = Str::startsWith($item->cover_url, ['http://', 'https://'])
                    ? $item->cover_url
                    : asset($item->cover_url);
            } else {
                $finalCoverUrl = asset('images/default-cover.png');
            }

            return [
                'id' => $item->id,
                'title' => $item->title,
                'category' => $item->category,
                'description' => $item->description,
                'duration' => $item->duration,
                'audio_url' => $finalAudioUrl,
                'cover_url' => $finalCoverUrl,
            ];
        });

        // TRIGGER LOG (Tambahkan kembali jika belum ada)
        \App\Models\UserActivity::log('audio', 'Membuka fitur Audio Mindfulness');

        return response()->json([
            'success' => true,
            'data' => $audios
        ]);
    }
}