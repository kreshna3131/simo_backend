<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IcdNine extends Model
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

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
