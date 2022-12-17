<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\MeasureRad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubMeasureRad extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function measureRads() {
        return $this->belongsToMany(MeasureRad::class, 'measure_rad_sub_measure_rad', 'sub_measure_id', 'measure_id');
    }
}
