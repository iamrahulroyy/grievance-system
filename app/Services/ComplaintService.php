<?php

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Events\ComplaintStatusChanged;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ComplaintService
{
    /**
     * @param  array{title: string, description: string}  $data
     */
    public function create(array $data, User $filedBy): Complaint
    {
        return Complaint::create([
            ...$data,
            'status'  => ComplaintStatus::Open,
            'user_id' => $filedBy->id,
        ]);
    }

    /**
     * Enforces the lifecycle open → in_progress → resolved | rejected.
     * Throws a 422 if the caller tries an illegal jump (e.g. resolved → open).
     */
    public function transitionStatus(Complaint $complaint, ComplaintStatus $next): Complaint
    {
        if (! $complaint->status->canTransitionTo($next)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from {$complaint->status->value} to {$next->value}.",
            ]);
        }

        $oldStatus = $complaint->status;

        $complaint->status = $next;
        $complaint->save();

        ComplaintStatusChanged::dispatch($complaint, $oldStatus, $next);

        return $complaint;
    }
}
