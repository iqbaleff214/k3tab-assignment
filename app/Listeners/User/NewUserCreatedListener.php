<?php

namespace App\Listeners\User;

use App\Events\User\NewUserCreated;
use App\Helpers\WhatsAppHelper;
use App\Mail\AccountCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewUserCreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     * @throws ConnectionException
     */
    public function handle(NewUserCreated $event): void
    {
        Mail::to($event->user->email)
            ->send(new AccountCreatedMail($event->user, $event->password));

        if (empty($event->user->phone))
            return;

        try {
            $headers = WhatsAppHelper::headers(config('whatsapp.token'), null);
            $response = WhatsAppHelper::adminUsers($headers);
            $items = $response['data'];
        } catch (\Exception $e) {
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

        if (empty($device)) {
            $this->notifyWhatsAppAdmin();
            return;
        }

        $headers = WhatsAppHelper::headers(config('whatsapp.token'), $device['token']);
        if (!$device['connected']) {
            $response = WhatsAppHelper::sessionConnect(['Message'], $headers);
            if (empty($response['data'])) {
                Log::error('WhatsApp error: ' . $response['error'], $response);
                return;
            }
        }

        $response = WhatsAppHelper::userCheck([$event->user->international_phone], $headers);
        if (empty($response['data'])) {
            Log::error('WhatsApp error: ' . $response['error'], $response);
            return;
        }

        $hasWhatsapp = (bool) $response['data']['Users'][0]['IsInWhatsapp'];
        $event->user->update([
            'has_whatsapp' => $hasWhatsapp,
        ]);

        if (!$hasWhatsapp)
            return;

        App::setLocale($event->user->locale);

        $content = __('email.account_created.welcome_heading', ['app' => config('app.name')]) . PHP_EOL . PHP_EOL;
        $content .= __('email.account_created.hello', ['name' => $event->user->name]) . PHP_EOL;
        $content .= __('email.account_created.account_created', ['app' => config('app.name')]) . PHP_EOL;
        $content .= __('email.account_created.credentials') . PHP_EOL;
        $content .= '- *' . __('email.account_created.phone') . '*: ' . $event->user->phone . PHP_EOL;
        $content .= '- *' . __('email.account_created.password') . '*: ' . $event->password . PHP_EOL . PHP_EOL;
        $content .= '> ' . __('email.account_created.security_notice') . PHP_EOL . PHP_EOL;
        $content .=  __('email.account_created.contact_admin') . PHP_EOL . PHP_EOL;
        $content .=  __('email.account_created.thanks') . PHP_EOL . PHP_EOL . PHP_EOL;
        $content .=  config('app.name') . PHP_EOL;

        WhatsAppHelper::chatText($event->user->international_phone, $content, $headers);
    }

    private function notifyWhatsAppAdmin(): void
    {
        /** @var \App\Models\User $users */
        $users = \App\Models\User::permission(\App\Enum\Permission::EditWhatsApp->value)->get();
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\WhatsApp\Disconnected());
        }
    }
}
