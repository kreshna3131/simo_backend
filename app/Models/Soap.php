<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soap extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'soaps';

    protected $appends = ['is_disabled'];
    
    /**
     * format tanggal created at
     *
     * @param  mixed $data
     * @return void
     */
    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y H:i:s');
    }
    
    /**
     * format tanggal updated at
     *
     * @param  mixed $data
     * @return void
     */
    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y H:i:s');
    }
    
    /**
     * relasi dengan assesment
     */
    public function assesments()
    {
        return $this->hasMany(Assesment::class, 'soap_id', 'id');
    }

    public function getIsDisabledAttribute()
    {
        // info($this->assesments);
        $data = [];
        foreach ($this->assesments as $key => $assesment) {
            $data[] = $assesment->type;
        }

        if(in_array('Covid', $data) && in_array('Global', $data)) {
            return 1;
        } else {
            return 0;
        }
    }
    
}
