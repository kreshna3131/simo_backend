<?php

namespace App\Notifications;

use App\Models\CommentLab;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentLabNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $commentLab,$user;

    public function __construct(CommentLab $commentLab,User $user)
    {
        $this->commentLab = $commentLab;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_role' => $this->user->roles->first()->name,
            'notification_for' => "Laboratorium",
            'notification_type' => "Membalas komentar.",
            'visit_id' => $this->commentLab->requestLab->visit_id,
            'unique_id' => $this->commentLab->requestLab->unique_id,
            'request_id' => $this->commentLab->requestLab->id,
            'recipe_type' => '-',
            'message' => $this->commentLab->message
        ];
    } 
}
