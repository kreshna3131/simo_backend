<?php

namespace App\Observers;

use App\Models\CommentRad;
use App\Models\User;
use App\Notifications\CommentRadNotification;

class CommentRadObserver
{
    public function created(CommentRad $commentRad)
    {
        $author = $commentRad->user;

        // $commentLabs = CommentLab::where('request_lab_id', '!=', $author->id)->groupBy('user_id')->get();
        if (str_contains(strtolower($author->roles->first()->name), 'rad') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%rad%');
            })->get();
            foreach ($users as $user) {
                $user->notify(new CommentRadNotification($commentRad, $author));
            }
        } else {
            $users = User::find($commentRad->requestRad->user_id);
            if (str_contains(strtolower($users->roles->first()->name), 'rad') === FALSE) {
                $users->notify(new CommentRadNotification($commentRad, $author));
            }
        }
    }
}
