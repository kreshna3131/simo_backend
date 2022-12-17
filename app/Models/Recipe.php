<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['allow'];

    public function getCreatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getUpdatedAtAttribute($data) {
        $local = Carbon::parse($data)->setTimezone('Asia/Jakarta');
        return formatDate($local, 'd/m/Y \j\a\m H:i:s');
    }

    public function getVisitIdAttribute($data)
    {
        return strval($data);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function concoctions()
    {
        return $this->hasMany(Concoction::class, 'recipe_id', 'id');
    }

    public function nonConcoctions()
    {
        return $this->hasMany(NonConcoction::class, 'recipe_id', 'id');
    }

    public static function generateUniqueId($lastTodayRecipe = null)
    {
        if ($lastTodayRecipe == null) {
            return 'RES' . now()->format('ymd') . '0001';
        }

        $lastIncrement = ltrim(substr($lastTodayRecipe->unique_id, -4), '0');
        $lastIncrement = $lastIncrement + 1;
        $lastIncrement = str_pad($lastIncrement, 4, '0', STR_PAD_LEFT);

        return 'RES' . now()->format('ymd') . $lastIncrement;
    }

    public function scopeToday()
    {
        return $this->whereDate('created_at', now()->format('Y-m-d'));
    }

    public function logStatus($req)
    {
        switch ($req) {
            case 'fixing':
                return 'Perbaikan';
            
            case 'progress':
                return 'Proses';

            case 'done':
                return 'Selesai';

            case 'cancel':
                return 'Batal';
            
            default:
                return 'Menunggu';
        }
    }

    public function getAllowAttribute()
    {
        if(auth()->user()->id == $this->user_id) {
            return 1;
        } else {
            return 0;
        }
    }
}
