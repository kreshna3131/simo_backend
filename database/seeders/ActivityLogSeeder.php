<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i=0; $i < 20; $i++) { 
            DB::table('activity_logs')->insert(
                [
                    'user_name' => 'PAIJO',
                    'user_role' => 'LABORATORIUM',
                    'unique_id' => 'LAB220802001',
                    'note' => 'membuat tindakan laboratorium cek hemoglobin, trombosit',
                    'action' => 'tambah',
                    'created_at' => Carbon::now()->subDays($i)
                ],
            );

        }
        

    }
}
