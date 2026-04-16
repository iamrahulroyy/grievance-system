<?php

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Validation\ValidationException;

class ComplaintService
{
    /**
     * @param  array{title: string, description: string}  $data
     */
    public function create(array $data): Complaint
    {
        return Complaint::create([
            ...$data,
            'status' => ComplaintStatus::Open,
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

        $complaint->status = $next;
        $complaint->save();

        return $complaint;
    }
}
