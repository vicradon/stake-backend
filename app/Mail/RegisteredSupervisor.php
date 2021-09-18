<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisteredSupervisor extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $coordinator, $password)
    {
        $this->user = $user;
        $this->coordinator = $coordinator;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $frontend_root = config('app.env') === 'local' ?
            config('app.frontend_local_root_url') :
            config('app.frontend_remote_root_url');

        $app_name = config('app.name');
        $url = "{$frontend_root}/supervisor/login?email={$this->user->email}&password={$this->password}&return_url={$frontend_root}/supervisor/password-update";

        return $this->markdown('emails.auth.register_supervisor', [
            'user' =>  $this->user,
            'coordinator' => $this->coordinator,
            'password' => $this->password,
            'profile_completion_url' =>  $url,
            'app_name' => $app_name
        ]);
    }
}
