<?php

namespace App\Notifications;

use App\Models\RequestRehab;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateRehabNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $requestRehab,$user;

    public function __construct(RequestRehab $requestRehab,User $user)
    {
        $this->requestRehab = $requestRehab;
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
            'notification_type' => "Tambah Permintaan Rehab Medic",
            'visit_id' => $this->requestRehab->visit_id,
            'unique_id' => $this->requestRehab->unique_id,
            'request_id' => $this->requestRehab->id,
            'recipe_type' => '-',
            'message' => ""
        ];
    } 
}
