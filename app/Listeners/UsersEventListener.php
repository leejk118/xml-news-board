<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class UsersEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  users.created  $event
     * @return void
     */
    public function handle(User $user)
    {
        //
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            UserCreated::class,
            __CLASS__ . '@onUserCreated'
        );
    }

    public function onUserCreated(UserCreated $event)
    {
        $user = $event->user;

        Mail::send('emails.auth.confirm', compact('user'), function ($message) use ($user) {
            $message->to($user->email);
            $message->subject(
                sprintf('[%s] 회원 가입을 확인해 주세요', config('app.name'))
            );
        });
    }
}
