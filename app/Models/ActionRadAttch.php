<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionRadAttch extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'action_rad_attchs';

    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i');
    }

    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i');
    }

    public function actionRad()
    {
        return $this->hasOne(ActionRad::class, 'id', 'action_rad_id');
    }
}
