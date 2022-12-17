<?php

namespace App\Notifications;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateRecipeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $recipe, $user;

    public function __construct(Recipe $recipe, User $user)
    {
        $this->recipe = $recipe;
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
            'notification_type' => "Tambah Permintaan E Resep",
            'visit_id' => $this->recipe->visit_id,
            'unique_id' => $this->recipe->unique_id,
            'request_id' => $this->recipe->id,
            'recipe_type' => $this->recipe->type,
            'message' => ""
        ];
    }
}
