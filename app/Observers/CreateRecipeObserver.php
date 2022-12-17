<?php

namespace App\Observers;

use App\Models\Recipe;
use App\Models\RequestRad;
use App\Models\User;
use App\Notifications\CreateRadNotification;
use App\Notifications\CreateRecipeNotification;

class CreateRecipeObserver
{
    public function created(Recipe $recipe)
    {
        $author = $recipe->user;
        if (str_contains(strtolower($author->roles->first()->name), 'apo') === FALSE) {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'like', '%apo%');
            })->get();
    
            foreach ($users as $key => $user) {
                $user->notify(new CreateRecipeNotification($recipe, $author));
            }
        }
    }
}
