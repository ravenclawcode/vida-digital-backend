<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MindfulnessAudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MindfulnessAudioController extends Controller
{
    public function index()
    {
        $audios = MindfulnessAudio::latest()->get();
        return view('admin.audio.index', compact('audios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required',
            'audio_file' => 'required|mimes:mp3|max:40960',
            'cover_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi cover
        ]);

        if ($request->hasFile('audio_file')) {
            // 1. Upload Audio
            $file = $request->file('audio_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('audios', $fileName, 'public');

            // Deteksi Durasi
            $getID3 = new \getID3;
            $fileInfo = $getID3->analyze(storage_path('app/public/' . $path));
            $duration = $fileInfo['playtime_string'] ?? '00:00';

            // 2. Upload Cover (Jika ada)
            $coverPath = null;
            if ($request->hasFile('cover_file')) {
                $coverFile = $request->file('cover_file');
                $coverName = time() . '_cover_' . $coverFile->getClientOriginalName();
                $coverPath = $coverFile->storeAs('covers', $coverName, 'public');
            }

            // 3. Simpan ke Database
            MindfulnessAudio::create([
                'title' => $request->title,
                'category' => $request->category,
                'description' => $request->description,
                'duration' => $duration,
                'audio_url' => \Storage::url($path),
                'cover_url' => $coverPath ? \Storage::url($coverPath) : null, // Simpan URL Cover
            ]);

            return redirect()->back()->with('success', "Berhasil! Audio dan Cover telah diunggah.");
        }
    }

    public function destroy($id)
    {
        $audio = MindfulnessAudio::findOrFail($id);

        // Hapus file audio
        if ($audio->audio_url) {
            $audioPath = str_replace('/storage/', '', $audio->audio_url);
            Storage::disk('public')->delete($audioPath);
        }

        // Hapus file cover
        if ($audio->cover_url) {
            $coverPath = str_replace('/storage/', '', $audio->cover_url);
            Storage::disk('public')->delete($coverPath);
        }

        $audio->delete();
        return redirect()->back()->with('success', 'Audio dan Cover berhasil dihapus!');
    }
}