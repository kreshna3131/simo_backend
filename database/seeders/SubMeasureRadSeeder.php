<?php

namespace Database\Seeders;

use App\Models\MeasureRad;
use App\Models\SubMeasureRad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubMeasureRadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sub_measure_rads')->insert([
            [
            'name' => 'USG',
            'group_name' => 'Screening, Patologi',
            'created_at' => now()  
            ],
            [
            'name' => 'Tyroid',
            'group_name' => 'Screening, Patologi',
            'created_at' => now()
            ],
        ]);

        $measureRad = MeasureRad::all();
        $submeasureRad = SubMeasureRad::all();

        foreach ($measureRad as $key => $measure) {
            foreach ($submeasureRad as $key => $subMeasure) {
                $subMeasure->measureRads()->attach($measure->id);
            }
        }
    }
}
