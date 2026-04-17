<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Complaint $complaint): bool
    {
        return $user->isAdmin() || $complaint->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only admins can update status / assign complaints.
     */
    public function update(User $user, Complaint $complaint): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only the citizen who filed it (while still open) or an admin can delete.
     */
    public function delete(User $user, Complaint $complaint): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $complaint->user_id === $user->id
            && $complaint->status === \App\Enums\ComplaintStatus::Open;
    }
}
