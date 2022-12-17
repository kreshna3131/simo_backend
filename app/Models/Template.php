<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    /**
     * format tanggal created at
     */
    public function getCreatedAtAttribute($data) {
        if($data) {
            $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
            return formatDate($local, 'd F Y \j\a\m H:i:s');
        } else {
            return '-';
        }
    }
    
    /**
     * format tanggal updated at
     *
     * @param  mixed $data
     * @return void
     */
    public function getUpdatedAtAttribute($data) {
        if($data) {
            $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
            return formatDate($local, 'd F Y \j\a\m H:i:s');
        } else {
            return '-';
        }
    }

    /**
     * relasi many to many dengan attribute
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class)->withPivot('id', 'group_name', 'status');
    }
}
