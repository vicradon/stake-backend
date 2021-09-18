<?php

namespace Database\Factories;

use App\Models\Coordinator;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'department' => 'Information Management Technology',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
    // public function isStudent()
    // {

    //     $student = Student::create(['reg_number' => $this->faker->regexify('20[1-9][1-9][0-9]{6}[1-9]')]);

    //     $student->user()->save($this->model);
    // }
    // public function isSupervisor()
    // {
    //     $supervisor = Supervisor::create();
    //     $supervisor->user()->save($this->model);
    // }
    // public function isCoordinator()
    // {
    //     $coordinator = Coordinator::create();
    //     $coordinator->user()->save($this->model);
    // }
}
