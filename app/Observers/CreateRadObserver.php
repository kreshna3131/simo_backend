<?php

namespace App\Observers;

use App\Models\RequestRad;
use App\Models\User;
use App\Notifications\CreateRadNotification;

class CreateRadObserver
{
    public function created(RequestRad $requestRad)
    {
        $author = $requestRad->user;
        if (str_contains(strtolower($author->roles->first()->name), 'rad') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%rad%');
            })->get();
    
            info($users);
    
            foreach ($users as $key => $user) {
                $user->notify(new CreateRadNotification($requestRad, $author));
            }
        }
    }
}
