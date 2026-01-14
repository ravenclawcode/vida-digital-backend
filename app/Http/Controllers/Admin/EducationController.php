<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EducationController extends Controller
{
    public function index()
    {
        $contents = EducationContent::latest()->get();
        return view('admin.education.index', compact('contents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,artikel',
            'category' => 'required|in:dasar,kesehatan mental,gaya hidup',
            'description' => 'required',
            'video_url' => 'required_if:type,video',
            'duration' => 'required_if:type,artikel',
        ]);

        $data = $request->only([
            'title',
            'type',
            'category',
            'duration',
            'description',
            'important_note'
        ]);

        if ($request->type === 'video') {
            if (preg_match('/(?:youtube\.com\/.*v=|youtu\.be\/)([^"&?\/\s]{11})/', $request->video_url, $match)) {
                $youtubeId = $match[1];
                $data['video_url'] = $youtubeId;
                $data['thumbnail'] = "https://img.youtube.com/vi/$youtubeId/hqdefault.jpg";
                $data['duration'] = $this->getYoutubeDuration($youtubeId) ?? '00:00';
            }
        } else {
            $data['video_url'] = null;
            $data['thumbnail'] = null;
        }

        EducationContent::create($data);
        return redirect()->back()->with('success', 'Konten edukasi berhasil disimpan!');
    }


    public function destroy($id)
    {
        EducationContent::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Konten berhasil dihapus.');
    }

    private function getYoutubeDuration($videoId)
    {
        $apiKey = config('services.youtube.key');

        $response = Http::get('https://www.googleapis.com/youtube/v3/videos', [
            'id' => $videoId,
            'part' => 'contentDetails',
            'key' => $apiKey,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $duration = $response['items'][0]['contentDetails']['duration'] ?? null;

        if (!$duration) {
            return null;
        }

        $interval = new \DateInterval($duration);
        $seconds =
            ($interval->h * 3600) +
            ($interval->i * 60) +
            $interval->s;

        return gmdate($seconds >= 3600 ? "H:i:s" : "i:s", $seconds);
    }

}
