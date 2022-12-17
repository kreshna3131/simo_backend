<?php

namespace App\Observers;

use App\Models\RequestRehab;
use App\Models\User;
use App\Notifications\CreateRehabNotification;

class CreateRehabObserver
{
    public function created(RequestRehab $requestRehab)
    {
        $author = $requestRehab->user;
        if (str_contains(strtolower($author->roles->first()->name), 'rehab') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%rehab%');
            })->get();
    
            foreach ($users as $key => $user) {
                $user->notify(new CreateRehabNotification($requestRehab, $author));
            }
        }
    }
}
