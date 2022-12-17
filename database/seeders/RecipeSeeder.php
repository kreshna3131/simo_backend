<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 5; $i++) {
            DB::table('recipes')->insert([
                'visit_id' => 22081530712208001,
                'user_id' => 1,
                'name' => 'Resep',
                'status' => 'waiting',
                'created_by' => 'Dr. Sutejo',
                'created_at' => now()
            ]);
        }
    }
}
