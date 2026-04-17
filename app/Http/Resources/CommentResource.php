<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $body
 * @property int $complaint_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 */
class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'body'         => $this->body,
            'complaint_id' => $this->complaint_id,
            'user_id'      => $this->user_id,
            'user'         => new UserResource($this->whenLoaded('user')),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
