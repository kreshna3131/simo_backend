<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['recipe_type'];

    public function getCreatedAtAttribute($data)
    {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getUpdatedAtAttribute($data)
    {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getRecipeTypeAttribute()
    {
        if (str_contains($this->unique_id, 'RES') !== FALSE) {
            $requestLab = Recipe::where('unique_id', $this->unique_id)->first();
            return $requestLab ? $requestLab->type : '';
        } else {
            return '-';
        }
    }
}
