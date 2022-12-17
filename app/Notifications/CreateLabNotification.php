<?php

namespace App\Notifications;

use App\Models\RequestLab;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateLabNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $requestLab,$user;

    public function __construct(RequestLab $requestLab,User $user)
    {
        $this->requestLab = $requestLab;
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
            'notification_type' => "Tambah Permintaan Laboratorium",
            'visit_id' => $this->requestLab->visit_id,
            'unique_id' => $this->requestLab->unique_id,
            'request_id' => $this->requestLab->id,
            'recipe_type' => '-',
            'message' => ""
        ];
    } 
}
