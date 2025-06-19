<?php

namespace App\Notifications\User;

use App\Broadcasting\WhatsAppChannel;
use App\Notifications\Messages\WhatsAppMessage;
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
    public function __construct(public Carbon $time, public string $ip, public ?string $link) { }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (!$notifiable->international_phone && !$this->link)
            return ['database', 'broadcast'];

        return [
            'database', 'broadcast', WhatsAppChannel::class,
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

    public function toWhatsApp(object $notifiable): WhatsAppMessage
    {
        App::setLocale($notifiable->locale);
        $content = __('email.reset_password.greeting', ['name' => $notifiable->name]) . PHP_EOL . PHP_EOL;
        $content .= __('email.reset_password.reset_password') . PHP_EOL;
        $content .= $this->link . PHP_EOL;
        $content .= __('email.reset_password.expire') . PHP_EOL . PHP_EOL;
        $content .= __('email.reset_password.no_action') . PHP_EOL . PHP_EOL . PHP_EOL;
        $content .= __('email.reset_password.thanks');
        return new WhatsAppMessage($notifiable->international_phone ?? '', $content);
    }
}
