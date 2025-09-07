<?php

namespace App\Notifications\Assessment;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessmentResult extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Assessment $assessment)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Assessment Result is Available')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We would like to inform you that your assessment for ' . $this->assessment->guide?->skill_number . ' has been completed.')
            ->line('Here are your results:')
            ->line('Status: ' . $this->assessment->enumResult()->label())
            ->action('View Details', route('assessee.assessment.print', $this->assessment))
            ->line('You can check the full assessment details by clicking the button above.')
            ->line('Thank you for your effort and participation.')
            ->salutation("Best regards,\nCORSA");
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
