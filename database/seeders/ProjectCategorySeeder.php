<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

class ProjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProjectCategory::create(["name" => "transportation"]);
        ProjectCategory::create(["name" => "iot"]);
        ProjectCategory::create(["name" => "communication"]);
    }
}
