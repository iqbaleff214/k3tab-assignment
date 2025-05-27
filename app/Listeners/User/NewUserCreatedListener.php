<?php

namespace App\Listeners\User;

use App\Events\User\NewUserCreated;
use App\Mail\AccountCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewUserCreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewUserCreated $event): void
    {
        Mail::to($event->user->email)
            ->send(new AccountCreatedMail($event->user, $event->password));
    }
}
