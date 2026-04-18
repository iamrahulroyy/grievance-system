<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Complaint;

class ComplaintObserver
{
    public function created(Complaint $complaint): void
    {
        $this->log($complaint, 'created');
    }

    public function updated(Complaint $complaint): void
    {
        $dirty = $complaint->getChanges();
        unset($dirty['updated_at']);

        if (empty($dirty)) {
            return;
        }

        $original = collect($complaint->getOriginal())
            ->only(array_keys($dirty))
            ->toArray();

        $this->log($complaint, 'updated', [
            'old' => $original,
            'new' => $dirty,
        ]);
    }

    public function deleted(Complaint $complaint): void
    {
        $this->log($complaint, 'deleted');
    }

    private function log(Complaint $complaint, string $action, ?array $changes = null): void
    {
        Activity::create([
            'user_id'      => auth()->id(),
            'subject_type' => Complaint::class,
            'subject_id'   => $complaint->id,
            'action'       => $action,
            'changes'      => $changes,
        ]);
    }
}
