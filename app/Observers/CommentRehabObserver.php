<?php

namespace App\Observers;

use App\Models\CommentRecipe;
use App\Models\CommentRehab;
use App\Models\User;
use App\Notifications\CommentRecipeNotification;
use App\Notifications\CommentRehabNotification;

class CommentRehabObserver
{
    public function created(CommentRehab $commentRehab)
    {
        $author = $commentRehab->user;

        // $commentLabs = CommentLab::where('request_lab_id', '!=', $author->id)->groupBy('user_id')->get();
        if (str_contains(strtolower($author->roles->first()->name), 'rehab') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%rehab%');
            })->get();
            foreach ($users as $user) {
                $user->notify(new CommentRehabNotification($commentRehab, $author));
            }
        } else {
            $users = User::find($commentRehab->requestRehab->user_id);
            if (str_contains(strtolower($users->roles->first()->name), 'rehab') === FALSE) {
                $users->notify(new CommentRehabNotification($commentRehab, $author));
            }
        }
    }
}
