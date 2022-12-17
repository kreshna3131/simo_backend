<?php

namespace App\Notifications;

use App\Models\CommentRecipe;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentRecipeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $commentRecipe, $user;

    public function __construct(CommentRecipe $commentRecipe, User $user)
    {
        $this->commentRecipe = $commentRecipe;
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
            'notification_for' => "E Resep",
            'notification_type' => "Membalas komentar.",
            'visit_id' => $this->commentRecipe->recipe->visit_id,
            'unique_id' => $this->commentRecipe->recipe->unique_id,
            'request_id' => $this->commentRecipe->recipe->id,
            'recipe_type' => $this->commentRecipe->recipe->type,
            'message' => $this->commentRecipe->message
        ];
    }
}
