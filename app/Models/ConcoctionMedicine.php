<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcoctionMedicine extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function concoction()
    {
        $this->hasOne(Concoction::class, 'id', 'concoction_id');
    }
}
