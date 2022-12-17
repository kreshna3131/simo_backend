<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concoction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function recipe()
    {
        return $this->hasOne(Recipe::class, 'id', 'recipe_id');
    }

    public function concoctionMedicines()
    {
        return $this->hasMany(ConcoctionMedicine::class, 'concoction_id', 'id');
    }

    public function delete()
    {
        // delete all related medicine 
        $this->concoctionMedicines()->delete();

        // delete the concoction
        return parent::delete();
    }
}
