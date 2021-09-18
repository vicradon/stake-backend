<?php

namespace Database\Seeders;

use App\Models\Coordinator;
use App\Models\ProjectCategory;
use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Student::factory()->count(20)->hasUser()->create();
        Coordinator::factory()->count(2)->hasUser()->create();
    }
}
