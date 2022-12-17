<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionLab extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function requestLabs()
    {
        return $this->hasMany(RequestLab::class, 'id', 'request_lab_id');
    }
}
