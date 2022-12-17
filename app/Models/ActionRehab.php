<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RequestRehab;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActionRehab extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Define Created At Accessor
     * @param  Date $data
     * @return String
     */
    public function getCreatedAtAttribute($data)
    {
        $date = Carbon::parse($data)->setTimezone('Asia/Jakarta');

        return formatDate($date, 'd/m/Y \j\a\m H:i:s');
    }

    /**
     * Define Updated At Accessor
     * @param  Date $data
     * @return String
     */
    public function getUpdatedAtAttribute($data)
    {
        $date = Carbon::parse($data)->setTimezone('Asia/Jakarta');

        return formatDate($date, 'd/m/Y \j\a\m H:i:s');
    }

    /**
     * Define Action Id Accessor
     * @param  Integer $data
     * @return String
     */
    public function getActionIdAttribute($data): String
    {
        return strval($data);
    }
    
    /**
     * Define Inverse One To Many Relation Between Request
     * 
     * @return 
     */
    public function requestRehab()
    {
        return $this->belongsTo(RequestRehab::class);
    }
}
