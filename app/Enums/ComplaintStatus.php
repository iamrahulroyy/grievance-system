<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    /**
     * Valid next states from the current state.
     * Encodes the complaint lifecycle: open → in_progress → resolved | rejected.
     */
    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Open       => in_array($next, [self::InProgress, self::Rejected], true),
            self::InProgress => in_array($next, [self::Resolved, self::Rejected], true),
            self::Resolved, self::Rejected => false,
        };
    }
}
