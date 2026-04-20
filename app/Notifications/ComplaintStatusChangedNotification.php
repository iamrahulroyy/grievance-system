<?php

namespace App\Notifications;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Complaint $complaint,
        public readonly ComplaintStatus $oldStatus,
        public readonly ComplaintStatus $newStatus,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = str_replace('_', ' ', $this->newStatus->value);

        return (new MailMessage)
            ->subject("Complaint #{$this->complaint->id} — Status updated to {$statusLabel}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your complaint \"{$this->complaint->title}\" has been updated.")
            ->line("**Status:** {$this->oldStatus->value} → **{$this->newStatus->value}**")
            ->line("**Complaint ID:** #{$this->complaint->id}")
            ->salutation('Regards, Grievance Portal')
            ->line('Thank you for using the Grievance Portal.');
    }
}
