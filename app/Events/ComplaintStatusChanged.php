<?php

namespace App\Events;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Complaint $complaint,
        public readonly ComplaintStatus $oldStatus,
        public readonly ComplaintStatus $newStatus,
    ) {}
}
