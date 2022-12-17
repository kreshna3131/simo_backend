<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['type', 'created_by', 'message', 'notification_at', 'visit_id', 'request_id', 'recipe_type'];

    public function setCreatedAtAttribute($data)
    {
        $this->attributes['created_at'] = Carbon::now()->setTimezone('Asia/Jakarta');
    }

    public function setUpdatedAtAttribute($data)
    {
        $this->attributes['updated_at'] = Carbon::now()->setTimezone('Asia/Jakarta');
    }

    public function getIdAttribute()
    {
        return strval($this->attributes['id']);
    }

    public function getTypeAttribute()
    {
        return json_decode($this->attributes['data'])->notification_for;
    }

    public function getCreatedByAttribute()
    {
        return json_decode($this->attributes['data'])->user_name;
    }

    public function getVisitIdAttribute()
    {
        return json_decode($this->attributes['data'])->visit_id;
    }

    public function getRequestIdAttribute()
    {
        return json_decode($this->attributes['data'])->request_id;
    }
    
    public function getRecipeTypeAttribute()
    {
        return json_decode($this->attributes['data'])->recipe_type;
    }

    public function getMessageAttribute()
    {
        return json_decode($this->attributes['data'])->notification_type;
    }

    public function getNotificationAtAttribute()
    {
        return Carbon::parse(($this->attributes['created_at']))->diffForHumans();
    }
}
