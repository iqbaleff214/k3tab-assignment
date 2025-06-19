<?php

namespace App\Broadcasting;

use App\Helpers\WhatsAppHelper;
use App\Models\User;
use App\Notifications\Messages\WhatsAppMessage;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user): array|bool
    {
        return $user->has_whatsapp;
    }

    /**
     * @throws ConnectionException
     */
    public function send(object $notifiable, Notification $notification): void
    {
        /** @var WhatsAppMessage $content */
        $content = $notification->toWhatsApp($notifiable);

        $phone = $notifiable->international_phone;
        if (empty($phone) || !$notifiable->has_whatsapp)
            return;

        try {
            $headers = WhatsAppHelper::headers(config('whatsapp.token'), null);
            $response = WhatsAppHelper::adminUsers($headers);
            $items = $response['data'];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return;
        }

        if (empty($items))
            return;

        $device = null;
        foreach ($items as $item) {
            if (!empty($item['token']) && !empty($item['jid'])) {
                $device = $item;
                break;
            }
        }

        if (empty($device))
            return;

        $headers = WhatsAppHelper::headers(config('whatsapp.token'), $device['token']);
        if (!$device['connected']) {
            $response = WhatsAppHelper::sessionConnect(['Message'], $headers);
            if (empty($response['data'])) {
                Log::error('WhatsApp error: ' . $response['error'], $response);
                return;
            }
        }

        App::setLocale($notifiable->locale);
        $response = $this->handle($content, $headers);
        if (empty($response['data'])) {
            Log::error('WhatsApp error: ' . $response['error'], $response);
        }
    }

    /**
     * @throws ConnectionException
     */
    private function handle(WhatsAppMessage $message, array $headers): array
    {
        if (!empty($message->image)) {
            return WhatsAppHelper::chatImage($message->phone, $message->image, $message->message ?? '', $headers);
        } elseif (!empty($message->document)) {
            return WhatsAppHelper::chatDocument($message->phone, $message->document, $message->documentName, $headers);
        } else {
            return WhatsAppHelper::chatText($message->phone, $message->message ?? 'Hi', $headers);
        }
    }
}
