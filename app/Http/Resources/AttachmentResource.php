<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $filename
 * @property string $mime_type
 * @property int $size
 * @property int $complaint_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 */
class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'filename'     => $this->filename,
            'mime_type'    => $this->mime_type,
            'size'         => $this->size,
            'complaint_id' => $this->complaint_id,
            'user_id'      => $this->user_id,
            'download_url' => url("/api/attachments/{$this->id}"),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
