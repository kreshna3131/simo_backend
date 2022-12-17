<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAssesment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getCreatedAtAttribute($data)
    {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i');
    }

    public function getUpdatedAtAttribute($data)
    {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i');
    }

    public function getTanggalTimbulGejalaAttribute($data)
    {
        return Carbon::parse($data);
    }

    public function getTerakhirDirawatSimoAttribute($data)
    {
        return Carbon::parse($data);
    }

    public function getNeurologisKepalaAttribute($data)
    {
        return $data ? json_decode($data) : '';
    }

    public function getNeurologisLeherAttribute($data)
    {
        return $data ? json_decode($data) : '';
    }

    public function getNeurologisExtrimitasAttribute($data)
    {
        return $data ? json_decode($data) : '';
    }

    public function getRencanaTindakLanjutAttribute($data)
    {
        return $data && is_string($data) ? json_decode($data) : $data;
    }

    /**
     * relasi dengan template
     *
     * @return void
     */
    public function template()
    {
        return $this->hasOne(Template::class, 'id', 'template_id');
    }

    /**
     * relasi dengan template
     *
     * @return void
     */
    public function assesment()
    {
        return $this->hasOne(Assesment::class, 'id', 'assesment_id');
    }
}
