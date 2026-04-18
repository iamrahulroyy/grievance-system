<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property ?int $user_id
 * @property string $action
 * @property ?array $changes
 * @property \Illuminate\Support\Carbon $created_at
 */
class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user'       => new UserResource($this->whenLoaded('user')),
            'action'     => $this->action,
            'changes'    => $this->changes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
