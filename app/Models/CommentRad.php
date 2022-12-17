<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentRad extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['user_name','user_role'];

    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i');
    }

    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i');
    }

    /**
     * relasi dengan user
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function requestRad()
    {
        return $this->hasOne(RequestRad::class, 'id', 'request_rad_id');
    }

    public function getUserNameAttribute()
    {
       return  $this->user->name;
    }

    public function getUserRoleAttribute()
    {
       return  $this->user->roles->first()->name;
    }
}
