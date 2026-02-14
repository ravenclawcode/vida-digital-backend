<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EducationContent;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $user = $request->user('sanctum');

        $data = EducationContent::when($type, function ($q) use ($type) {
            return $q->where('type', $type);
        })->latest()->get()->map(function ($item) use ($user) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'type' => $item->type,
                'category' => $item->category,
                'duration' => $item->duration,
                'description' => $item->description,
                'video_url' => $item->video_url,
                'thumbnail' => $item->thumbnail,
                'important_note' => $item->important_note,
                'likes' => (int) $item->likes,
                'published_at' => $item->created_at->locale('id')->diffForHumans(),
                'is_liked' => $user ? \DB::table('education_likes')
                    ->where('user_id', $user->id)
                    ->where('education_content_id', $item->id)
                    ->exists() : false,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function like(Request $request, $id)
    {
        $user = $request->user();
        $content = EducationContent::findOrFail($id);

        $existingLike = \DB::table('education_likes')
            ->where('user_id', $user->id)
            ->where('education_content_id', $id)
            ->first();

        if ($existingLike) {
            \DB::table('education_likes')->where('id', $existingLike->id)->delete();
            $content->decrement('likes');
            $liked = false;
        } else {
            \DB::table('education_likes')->insert([
                'user_id' => $user->id,
                'education_content_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $content->increment('likes');
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'is_liked' => $liked,
            'current_likes' => (int) $content->fresh()->likes
        ]);
    }
}
