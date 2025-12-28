<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationContent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EducationController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        $type = $request->query('type');

        $data = EducationContent::when($type, function ($q) use ($type) {
            return $q->where('type', $type);
        })->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'type' => $item->type,
                'category' => $item->category,
                'duration' => $item->duration,
                'description' => $item->description,
                'video_url' => $item->video_url,
                'thumbnail' => $item->thumbnail,
                'likes' => $item->likes, 
                'published_at' => $item->created_at->diffForHumans(), 
            ];
        });

        \App\Models\UserActivity::log('education', 'Membaca "Tips & Edukasi"');

        return response()->json(['success' => true, 'data' => $data]);
    }

   public function like($id)
{
    $content = EducationContent::findOrFail($id);
    
    $content->increment('likes'); 
    
    return response()->json([
        'success' => true, 
        'current_likes' => $content->likes
    ]);
}
}