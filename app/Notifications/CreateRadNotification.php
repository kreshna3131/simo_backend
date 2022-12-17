<?php

namespace App\Notifications;

use App\Models\RequestRad;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateRadNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $requestRad,$user;

    public function __construct(RequestRad $requestRad,User $user)
    {
        $this->requestRad = $requestRad;
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
            'notification_type' => "Tambah Permintaan Radiologi",
            'visit_id' => $this->requestRad->visit_id,
            'unique_id' => $this->requestRad->unique_id,
            'request_id' => $this->requestRad->id,
            'recipe_type' => '-',
            'message' => ""
        ];
    }  
}
