<?php

namespace App\Observers;

use App\Models\CommentLab;
use App\Models\User;
use App\Notifications\CommentLabNotification;

class CommentLabObserver
{
    public function created(CommentLab $commentLab)
    {
        $author = $commentLab->user;
        // $commentLabs = CommentLab::where('request_lab_id', '!=', $author->id)->groupBy('user_id')->get();
        if (str_contains(strtolower($author->roles->first()->name), 'lab') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%lab%');
            })->get();
            foreach ($users as $user) {
                $user->notify(new CommentLabNotification($commentLab, $author));
            }
        } else {
            info($commentLab);
            $users = User::find($commentLab->requestLab->user_id);
            if(str_contains(strtolower($users->roles->first()->name), 'lab') === FALSE) {
                $users->notify(new CommentLabNotification($commentLab, $author));
            }
        }
    }
}
