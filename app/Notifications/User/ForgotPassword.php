<?php

namespace App\Notifications\User;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class ForgotPassword extends Notification // implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Carbon $time, public string $ip) { }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            'database', 'broadcast',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        App::setLocale($notifiable->locale);
        return [
            'title' => __('notification.user.forgot_password_title'),
            'message' => __('notification.user.forgot_password', ['ip' => $this->ip, 'timestamp' => $this->time->format('Y-m-d H:i:s')]),
            'link' => '#',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
