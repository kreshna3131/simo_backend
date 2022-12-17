<?php

namespace App\Notifications;

use App\Models\CommentRehab;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentRehabNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $commentRehab, $user;

    public function __construct(CommentRehab $commentRehab, User $user)
    {
        $this->commentRehab = $commentRehab;
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
            'notification_for' => "Rehab Medic",
            'notification_type' => "Membalas komentar.",
            'visit_id' => $this->commentRehab->requestRehab->visit_id,
            'unique_id' => $this->commentRehab->requestRehab->unique_id,
            'request_id' => $this->commentRehab->requestRehab->id,
            'recipe_type' => '-',
            'message' => $this->commentRehab->message
        ];
    }
}
