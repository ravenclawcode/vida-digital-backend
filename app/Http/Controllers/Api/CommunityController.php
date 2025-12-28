<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityLike;
use App\Models\CommunityComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = CommunityPost::withCount(['likes', 'comments'])
            ->latest()
            ->get()
            ->map(fn($post) => [
                'id' => $post->id,
                'display_name' => 'Anonim',
                'category' => $post->category,
                'content' => $post->content,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'time_ago' => $post->created_at->diffForHumans(),
            ]);

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'content' => 'required|string|min:5',
        ]);

        $post = CommunityPost::create([
            'user_id' => auth()->id(),
            'category' => $request->get('category'),
            'content' => $request->get('content'),
        ]);

        \App\Models\UserActivity::log('community', 'Bergabung di "Komunitas Anonim"');

        return response()->json(['status' => 'success', 'message' => 'Postingan terkirim', 'data' => $post]);
    }

    public function toggleLike($postId)
    {
        $userId = Auth::id();
        $like = CommunityLike::where('community_post_id', $postId)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Unlike berhasil']);
        }

        CommunityLike::create(['community_post_id' => $postId, 'user_id' => $userId]);
        return response()->json(['message' => 'Like berhasil']);
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate(['comment' => 'required|string|min:2']);

        $comment = CommunityComment::create([
            'community_post_id' => $postId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Komentar terkirim', 'data' => $comment]);
    }
}