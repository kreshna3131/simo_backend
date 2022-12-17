<?php

namespace Database\Seeders;

use App\Models\MeasureRad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionRadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('measure_rads')->insert([
            [
            'name' => 'Screening',
            'sub_count' => '2',
            'created_at' => now()
            ],
            [
            'name' => 'Patologi',
            'sub_count' => '2',
            'created_at' => now()
            ],
        ]);
    }
}
