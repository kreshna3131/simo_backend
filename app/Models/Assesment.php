<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assesment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['allow'];
    
    /**
     * format tanggal created at
     *
     * @param  mixed $data
     * @return void
     */
    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }
    
    /**
     * format tanggal updated at
     *
     * @param  mixed $data
     * @return void
     */
    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }

    /**
     * format tanggal tanggal timbul gejala
     *
     * @param  mixed $data
     * @return void
     */
    public function getTanggalTimbulGejalaAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return $local;
    }
    
    /**
     * relasi dengan user
     *
     * @return void
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * relasi dengan sub assesment
     *
     * @return void
     */
    public function subAssesments()
    {
        return $this->hasMany(SubAssesment::class, 'id', 'assesment_id');
    }

    /**
     * relasi dengan soap
     *
     * @return void
     */
    public function soap()
    {
        return $this->hasMany(Soap::class, 'id', 'soap_id');
    }
    
    /**
     * untuk disabled status covid dan global
     *
     * @return void
     */
    public function getAllowAttribute()
    {
        if(($this->type == 'Covid' || $this->type == 'Global') || auth()->user()->id != $this->user_id) {
            return 0;
        } else {
            return 1;
        }
    }
}
