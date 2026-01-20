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
    /**
     * Filter konten terlarang (Nomor HP & Kata Kunci Kontak)
     */
    private function containsForbiddenContent($text)
    {
        // Regex untuk mendeteksi nomor telepon (contoh: 0812..., +62..., 085-...)
        $phonePattern = '/(\+62|62|0)8[1-9][0-9]{6,10}/';
        
        // Daftar kata terlarang (Blacklist)
        $forbiddenWords = ['wa', 'whatsapp', 'nomer', 'kontak', 'hubungi'];

        // Cek pola nomor HP
        if (preg_match($phonePattern, str_replace([' ', '-', '.'], '', $text))) {
            return "Mohon tidak membagikan nomor telepon demi keamanan Anda.";
        }

        // Cek kata terlarang
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

            $posts = CommunityPost::with(['user:id,username', 'comments.user:id,username'])
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

                $post->comments->each(function ($comment) use ($userId) {
                    $comment->is_mine = $comment->user_id === $userId;
                    $comment->time_ago = $comment->created_at->diffForHumans();
                    $comment->author_name = $comment->is_mine
                        ? ($comment->user->username ?? 'User')
                        : 'Anonim';
                });

                return $post;
            });

            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'content' => 'required|string|min:5',
        ]);

        // Cek konten postingan
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
            'comment' => 'required|string|min:2'
        ]);

        // Cek konten komentar
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