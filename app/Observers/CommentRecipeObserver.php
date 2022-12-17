<?php

namespace App\Observers;

use App\Models\CommentRecipe;
use App\Models\User;
use App\Notifications\CommentRecipeNotification;

class CommentRecipeObserver
{
    public function created(CommentRecipe $commentRecipe)
    {
        $author = $commentRecipe->user;

        // $commentLabs = CommentLab::where('request_lab_id', '!=', $author->id)->groupBy('user_id')->get();
        if (str_contains(strtolower($author->roles->first()->name), 'apo') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%apo%');
            })->get();
            foreach ($users as $user) {
                $user->notify(new CommentRecipeNotification($commentRecipe, $author));
            }
        } else {
            $users = User::find($commentRecipe->recipe->user_id);
            if (str_contains(strtolower($users->roles->first()->name), 'apo') === FALSE) {
                $users->notify(new CommentRecipeNotification($commentRecipe, $author));
            }
        }
    }
}
