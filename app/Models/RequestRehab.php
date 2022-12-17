<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\ActionRehab;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestRehab extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Define Visit Id Accessor
     * @param  Integer $data
     * @return String
     */
    public function getVisitIdAttribute($data)
    {
        return strval($data);
    }

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
     * Method For Generating Unique ID
     * @param  Date $data
     * @return String
     */
    public static function generateUniqueId($lastTodayRecipe = null)
    {
        if ($lastTodayRecipe == null) {
            return 'RHM' . now()->format('ymd') . '0001';
        }

        $lastIncrement = ltrim(substr($lastTodayRecipe->unique_id, -4), '0');
        $lastIncrement = $lastIncrement + 1;
        $lastIncrement = str_pad($lastIncrement, 4, '0', STR_PAD_LEFT);

        return 'RHM' . now()->format('ymd') . $lastIncrement;
    }

    /**
     * Method For Getting Update Status Action eg: Menunggu, Perbaikan, etc.
     * @param  Request $request
     * @return String
     */
    public function logStatus($request): String
    {
        switch ($request->status) {
            case 'fixing':
                return 'Perbaikan';

            case 'progress':
                return 'Proses';

            case 'done':
                return 'Selesai';

            case 'cancel':
                return 'Batal';

            default :
                return 'Menunggu';
        }
    }

    /**
     * Local Scoping for Today
     * @param  Date $data
     * @return String
     */
    public function scopeToday()
    {
        return $this->whereDate('created_at', now()->format('Y-m-d'));
    }

    /**
     * Define One to Many Relation Between Action Rehab
     * 
     * @return 
     */
    public function actionRehabs()
    {
        return $this->hasMany(ActionRehab::class, 'request_rehab_id', 'id');
    }

    /**
     * Define One to Many Relation Between Comment Rehab
     * 
     * @return 
     */
    public function commentRehabs()
    {
        return $this->hasMany(CommentRehab::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
