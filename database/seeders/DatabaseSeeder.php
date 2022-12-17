<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(SettingSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(TemplateSeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(AttributeTemplateSeeder::class);
        $this->call(ActionRadSeeder::class);
        $this->call(SubMeasureRadSeeder::class);
        // $this->call(RecipeSeeder::class);
        // $this->call(ActivityLogSeeder::class);
        $this->call(IcdNineFillSeeder::class);
        $this->call(IcdTenFillSeeder::class);
    }
}
