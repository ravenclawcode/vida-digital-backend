<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityLike;
use App\Models\CommunityComment;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    private function containsForbiddenContent($text)
    {
        $phonePattern = '/(\+62|62|0)8[1-9][0-9]{6,10}/';

        $forbiddenWords = ['wa', 'whatsapp', 'nomer', 'kontak', 'hubungi'];

        if (preg_match($phonePattern, str_replace([' ', '-', '.'], '', $text))) {
            return "Mohon tidak membagikan nomor telepon demi keamanan Anda.";
        }

        foreach ($forbiddenWords as $word) {
            if (stripos($text, $word) !== false) {
                return "Mohon tidak membagikan informasi kontak pribadi.";
            }
        }

        return null;
    }

    public function index()
    {
        try {
            $userId = Auth::id();

            $posts = CommunityPost::with([
                'user:id,username,profile_photo',
                'comments.user:id,username,profile_photo'
            ])
                ->withCount(['likes', 'comments'])
                ->withExists(['likes as is_liked' => fn($q) => $q->where('user_id', $userId)])
                ->latest()
                ->paginate(10);

            $posts->getCollection()->transform(function ($post) use ($userId) {
                $post->is_mine = $post->user_id === $userId;
                $post->time_ago = $post->created_at->diffForHumans();

                $post->author_name = $post->is_mine
                    ? ($post->user->username ?? 'User')
                    : 'Anonim';

                $post->author_photo = $post->is_mine
                    ? ($post->user->profile_photo ?? null)
                    : null;

                $post->comments->each(function ($comment) use ($userId) {
                    $comment->is_mine = $comment->user_id === $userId;
                    $comment->time_ago = $comment->created_at->diffForHumans();
                    $comment->author_name = $comment->is_mine
                        ? ($comment->user->username ?? 'User')
                        : 'Anonim';

                    $comment->author_photo = $comment->is_mine
                        ? ($comment->user->profile_photo ?? null)
                        : null;
                });

                return $post;
            });

            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'content' => 'required|string|min:1',
        ]);

        $error = $this->containsForbiddenContent($validated['content']);
        if ($error) {
            return response()->json([
                'status' => 'error',
                'message' => $error
            ], 422);
        }

        return DB::transaction(function () use ($validated) {
            $post = CommunityPost::create([
                'user_id' => Auth::id(),
                'category' => $validated['category'],
                'content' => $validated['content'],
            ]);

            if (class_exists(UserActivity::class)) {
                UserActivity::create([
                    'user_id' => Auth::id(),
                    'type' => 'community',
                    'description' => 'Membuat postingan baru'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Postingan berhasil dibuat',
                'data' => $post
            ], 201);
        });
    }

    public function toggleLike($postId)
    {
        $post = CommunityPost::findOrFail($postId);
        $userId = Auth::id();

        $like = CommunityLike::where('community_post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            return response()->json(['status' => 'success', 'is_liked' => false]);
        }

        CommunityLike::create([
            'community_post_id' => $post->id,
            'user_id' => $userId
        ]);

        return response()->json(['status' => 'success', 'is_liked' => true]);
    }

    public function storeComment(Request $request, $postId)
    {
        $validated = $request->validate([
            'comment' => 'required|string|min:1'
        ]);

        $error = $this->containsForbiddenContent($validated['comment']);
        if ($error) {
            return response()->json([
                'status' => 'error',
                'message' => $error
            ], 422);
        }

        $comment = CommunityComment::create([
            'community_post_id' => $postId,
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $comment
        ]);
    }

    public function destroy($id)
    {
        $post = CommunityPost::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();
        return response()->json(['status' => 'success', 'message' => 'Postingan dihapus']);
    }
}
