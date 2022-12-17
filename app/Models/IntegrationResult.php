<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i');
    }

    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i');
    }

    /**
     * Relasi one to one dengan sub-assesmen
     */
    public function subAssesment()
    {
        return $this->hasOne(SubAssesment::class, 'id', 'sub_assesment_id');
    }

    public function getRencanaTindakLanjutAttribute($data)
    {
        return $data && is_string($data) ? json_decode($data) : $data;
    }
}
