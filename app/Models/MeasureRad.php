<?php

namespace App\Models;

use App\Models\SubMeasureRad;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeasureRad extends Model
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

    // Function relation many to many
    public function subMeasureRads()
    {
        return $this->belongsToMany(SubMeasureRad::class, 'measure_rad_sub_measure_rad', 'measure_id', 'sub_measure_id');
    }

    // Function for updating group
    public function updateGroup($req)
    {
        $subs = $this->subMeasureRads()->get();

        $this->update($req);

        foreach ($subs as $sub) {
            $data = [];

            foreach ($sub->measureRads as $measure) {
                $data[] = $measure->name;
            }

            $sub->update([
                'group_name' => implode(', ', $data)
            ]);
        }
    }

    // Function for delegating group
    public function delegateGroup($group)
    {
        $delegateTo = MeasureRad::find($group)->first();
        $subs = $this->subMeasureRads()->get();

        foreach ($subs as $sub) {
            $subName = explode(', ', $sub->group_name);

            if (in_array($this->name, $subName)) {
                $subGroupName = collect($subName);

                $filtered = $subGroupName->filter(function ($name) {
                    return $name !== $this->name;
                });

                $filteredName = implode(', ', $filtered->toArray());

                $sub->update([
                    'group_name' => $filteredName
                ]);
            }
        }

        $this->subMeasureRads()->detach();

        foreach ($subs as $sub) {
            $subName = explode(', ', $sub->group_name);
            $nameGroup = $delegateTo->name;

            if (!in_array($nameGroup, $subName)) {
                $sub->measureRads()->attach($group);

                $delegateTo->update([
                    'sub_count' => $delegateTo->sub_count + 1
                ]);

                $sub->update([
                    'group_name' => $sub->group_name ? $sub->group_name . ", $nameGroup" : $nameGroup
                ]);
            }
        }
    }
}
