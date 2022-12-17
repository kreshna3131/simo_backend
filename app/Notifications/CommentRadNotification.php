<?php

namespace App\Notifications;

use App\Models\CommentRad;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentRadNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $commentRad, $user;

    public function __construct(CommentRad $commentRad, User $user)
    {
        $this->commentRad = $commentRad;
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
            'notification_for' => "Radiologi",
            'notification_type' => "Membalas komentar.",
            'visit_id' => $this->commentRad->requestRad->visit_id,
            'unique_id' => $this->commentRad->requestRad->unique_id,
            'request_id' => $this->commentRad->requestRad->id,
            'recipe_type' => '-',
            'message' => $this->commentRad->message
        ];
    }
}
