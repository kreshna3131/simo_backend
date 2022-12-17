<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionRad extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['visit_id'];

    public function actionRadAttchs()
    {
        return $this->hasMany(ActionRadAttch::class, 'action_rad_id', 'id');
    }

    public function requestRad()
    {
        return $this->hasOne(RequestRad::class, 'id', 'request_rad_id');
    }

    public function getVisitIdAttribute()
    {
        return optional($this->requestRad)->visit_id;
    }
}
