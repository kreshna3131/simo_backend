<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\RequestLab;
use App\Models\User;
use App\Notifications\CreateLabNotification;

class CreateLabObserver
{
    public function created(RequestLab $requestLab)
    {
        $author = $requestLab->user;
        if(str_contains(strtolower($author->roles->first()->name), 'lab') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%lab%');
            })->get();

            info($users);
    
            foreach ($users as $key => $user) {
                $user->notify(new CreateLabNotification($requestLab, $author));
            }

            optional(Notification::first())->update([
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
