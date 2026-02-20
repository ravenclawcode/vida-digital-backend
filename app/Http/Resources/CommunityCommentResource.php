<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunityCommentResource extends JsonResource
{
    public function toArray($request)
    {
        $user = auth('sanctum')->user();
        $userId = $user ? $user->id : null;
        $isMine = $this->user_id === $userId;

        $canSeeRealName = $isMine || ($user && $user->role_id == 2);

        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'is_mine' => $isMine,
            'author_name' => $canSeeRealName ? ($this->user->username ?? 'User') : 'Anonim',
            'author_photo' => $canSeeRealName ? $this->user->profile_photo : null,
            'time_ago' => $this->created_at->diffForHumans(),
        ];
    }
}
