<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonConcoction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function recipe()
    {
        return $this->hasOne(Recipe::class, 'id', 'recipe_id');
    }
}
