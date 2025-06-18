<?php

namespace App\Listeners;

use App\Events\User\NewPhoneNumber;
use App\Helpers\WhatsAppHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class NewPhoneNumberListener implements ShouldQueue
{

    /**
     * Create the event listener.
     */
    public function __construct() { }

    /**
     * Handle the event.
     * @throws ConnectionException
     */
    public function handle(NewPhoneNumber $event): void
    {
        if (empty($event->user->phone))
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

        $token = null;
        foreach ($items as $item) {
            if (!empty($item['token'])) {
                $token = $item['token'];
                break;
            }
        }

        if (empty($token))
            return;

        $headers = WhatsAppHelper::headers(config('whatsapp.token'), $token);
        $response = WhatsAppHelper::userCheck([$event->user->international_phone], $headers);
        Log::debug('userCheck', $response);
        if (strtolower($response['error'] ?? '') === "no session") {
            $this->notifyWhatsAppAdmin();
            return;
        } elseif (empty($response['data'])) {
            return;
        }

        $hasWhatsapp = (bool) $response['data']['Users'][0]['IsInWhatsapp'];
        $event->user->update([
            'has_whatsapp' => $hasWhatsapp,
        ]);

        if (!$hasWhatsapp)
            return;

        App::setLocale($event->user->locale);

        $content = __('email.phone_linked.greeting', ['name' => $event->user->name]) . PHP_EOL;
        $content .= __('email.phone_linked.phone_linked', ['phone' => $event->user->international_phone, 'email' => $event->user->email]) . PHP_EOL;
        $content .=  __('email.phone_linked.contact_admin') . PHP_EOL;
        $content .=  __('email.phone_linked.thanks') . PHP_EOL . PHP_EOL;
        $content .=  config('app.name') . PHP_EOL;

        WhatsAppHelper::chatText($event->user->international_phone, $content, $headers);
    }

    private function notifyWhatsAppAdmin(): void
    {
        /** @var \App\Models\User $users */
        Log::debug('WhatsAppAdmin Notify WhatsApp Admin Start');
        $users = \App\Models\User::permission(\App\Enum\Permission::EditWhatsApp->value)->get();
        foreach ($users as $user) {
            Log::debug('WhatsAppAdmin Notify WhatsApp Admin:' . $user->name);
            $user->notify(new \App\Notifications\WhatsApp\Disconnected());
        }
        Log::debug('WhatsAppAdmin Notify WhatsApp Admin End');
    }
}
