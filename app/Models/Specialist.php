<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    /**
     * relasi dengan user
     */
    public function users()
    {
        return $this->hasMany(User::class, 'specialist_id', 'id');
    }
        
    /**
     * format tanggal created at
     */
    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }
    
    /**
     * format tanggal updated at
     */
    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd F Y \j\a\m H:i:s');
    }
}
