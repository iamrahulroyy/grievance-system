<?php

namespace App\Listeners;

use App\Events\ComplaintStatusChanged;
use App\Notifications\ComplaintStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStatusChangedNotification implements ShouldQueue
{
    public function handle(ComplaintStatusChanged $event): void
    {
        $citizen = $event->complaint->user;

        $citizen->notify(new ComplaintStatusChangedNotification(
            $event->complaint,
            $event->oldStatus,
            $event->newStatus,
        ));
    }
}
