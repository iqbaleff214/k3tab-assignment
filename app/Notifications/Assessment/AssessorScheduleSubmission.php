<?php

namespace App\Notifications\Assessment;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessorScheduleSubmission extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $assessee, public Assessment $assessment)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'mail', 'database',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Assessment Schedule Submission from ' . $this->assessee->name)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We would like to inform you that ' . $this->assessee->name . ' has submitted new assessment schedule proposals for ' . $this->assessment->guide?->skill_number . '.')
            ->line('The assessee has suggested several date options for the assessment session.')
            ->action('Review Schedule', route('dashboard'))
            ->line('Please review and approve one of the proposed dates at your earliest convenience.')
            ->line('Thank you for your attention and cooperation.')
            ->salutation('Best regards,
            CORSA');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
