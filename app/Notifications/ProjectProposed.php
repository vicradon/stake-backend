<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectProposed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($supervisor_user, $student_user, $proposal)
    {
        $this->supervisor_user = $supervisor_user;
        $this->student_user = $student_user;
        $this->proposal = $proposal;
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
        $proposal_id = $this->proposal->id;
        $frontend_root = config('app.env') === 'local' ?
            config('app.frontend_local_root_url') :
            config('app.frontend_remote_root_url');

        $url = "{$frontend_root}/supervisor/recent-proposals/{$proposal_id}";

        return (new MailMessage)->subject('New project proposal')
            ->markdown('emails.project.new_proposal', [
                'supervisor' => $this->supervisor_user,
                'student' => $this->student_user,
                'proposal' => $this->proposal,
                'url' => $url
            ]);
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
