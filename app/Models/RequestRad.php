<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestRad extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['allow'];

    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getVisitIdAttribute($data)
    {
        return strval($data);
    }

    public static function generateUniqueId($lastTodayRequest = null)
    {
        if ($lastTodayRequest == null) {
            return 'RAD' . now()->format('ymd') . '0001';
        }

        $lastIncrement = ltrim(substr($lastTodayRequest->unique_id, -4), '0');
        $lastIncrement = $lastIncrement + 1;
        $lastIncrement = str_pad($lastIncrement, 4, '0', STR_PAD_LEFT);

        return 'RAD' . now()->format('ymd') . $lastIncrement;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function actionRads()
    {
        return $this->hasMany(ActionRad::class, 'request_rad_id', 'id');
    }

    public function commentRads()
    {
        return $this->hasMany(CommentRad::class, 'request_rad_id', 'id');
    }

    public function scopeToday()
    {
        return $this->whereDate('created_at', now()->format('Y-m-d'));
    }

    public function getAllowAttribute()
    {
        if(auth()->user()->id == $this->user_id) {
            return 1;
        } else {
            return 0;
        }
    }
}
