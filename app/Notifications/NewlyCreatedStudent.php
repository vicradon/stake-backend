<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewlyCreatedStudent extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $password = "password")
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $app_name = config('app.name');

        $frontend_root = config('app.env') === 'local' ?
            config('app.frontend_local_root_url') :
            config('app.frontend_remote_root_url');
        $url = "{$frontend_root}/student/login?email={$this->user->email}&password={$this->password}&next={$frontend_root}/student/password-update";


        return (new MailMessage)
            ->subject('Welcome Aboard')

            ->line("Welcome to {$app_name}, {$this->user->name}")
            ->line("{$app_name} is a project management software for final year students and their supervisors.")
            ->line("You are required to login to your profile, update your password and create a project proposal.")
            ->action('Login', $url)
            ->line("Thank you for using {$app_name}!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
