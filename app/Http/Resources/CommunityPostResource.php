<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunityPostResource extends JsonResource
{
    public function toArray($request)
    {
        $user = auth('sanctum')->user();
        $userId = $user->id;
        $isMine = $this->user_id === $userId;

        $canSeeRealName = $isMine || ($user && $user->role_id == 2);

        return [
            'id' => $this->id,
            'category' => $this->category,
            'content' => $this->content,
            'likes_count' => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'is_liked' => (bool) $this->is_liked,
            'is_mine' => $isMine,
            'author_name' => $canSeeRealName ? ($this->user->username ?? 'User') : 'Anonim',
            'author_photo' => $canSeeRealName ? $this->user->profile_photo : null,
            'time_ago' => $this->created_at->diffForHumans(),
            'comments' => CommunityCommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
