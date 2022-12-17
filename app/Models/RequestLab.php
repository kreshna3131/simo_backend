<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLab extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['allow'];

    public function getCreatedAtAttribute($data)
    {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getUpdatedAtAttribute($data)
    {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getDoneAtAttribute($data)
    {
        if($data) {
            $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
            return formatDate($local, 'd/m/Y \j\a\m H:i:s');
        }
    }

    public function setCreatedAtAttribute($data)
    {
        $this->attributes['created_at'] = Carbon::now()->setTimezone('Asia/Jakarta');
    }

    public function setUpdatedAtAttribute($data)
    {
        $this->attributes['updated_at'] = Carbon::now()->setTimezone('Asia/Jakarta');
    }

    public function getVisitIdAttribute($data)
    {
        return strval($data);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function generateUniqueId($lastTodayRequest = null)
    {
        if ($lastTodayRequest == null) {
            return 'LAB' . now()->format('ymd') . '0077';
        }

        $lastIncrement = ltrim(substr($lastTodayRequest->unique_id, -4), '0');
        $lastIncrement = $lastIncrement + 1;
        $lastIncrement = str_pad($lastIncrement, 4, '0', STR_PAD_LEFT);

        return 'LAB' . now()->format('ymd') . $lastIncrement;
    }

    public function actionLabs()
    {
        return $this->hasMany(ActionLab::class, 'request_lab_id', 'id');
    }

    public function commentLabs()
    {
        return $this->hasMany(CommentLab::class, 'request_lab_id', 'id');
    }

    public function scopeToday()
    {
        return $this->whereDate('created_at', now()->format('Y-m-d'));
    }

    public function getAllowAttribute()
    {
        if (auth()->user()->id == $this->user_id) {
            return 1;
        } else {
            return 0;
        }
    }

    // public function getInfoAttribute()
    // {
    //     $countAction = $this->actionLabs->count();
    //     $countComment =  $this->commentLabs->count();
    //     return $countAction. ' Tindakan dan '. $countComment . ' Komentar';
    // }

    // public function getIsReadAttribute()
    // {
    //     $isRead = $this->commentLabs;

    //     $data = [];
    //     foreach ($isRead as $key => $read) {
    //         $data[] = $read->is_read;
    //     }

    //     if (in_array(0, $data) || $isRead->count() == 0) {
    //         return 0;
    //     } else {
    //         return 1;
    //     }
    // }
}
