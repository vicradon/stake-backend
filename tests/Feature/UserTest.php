<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_user()
    {
        User::factory()->isAdmin()->create();
        $response = $this->postJson('/api/login', ['email' => 'admin@polls-app.com', 'password' => 'password']);
        $response->assertStatus(200)->assertJson(['token_type' => 'Bearer']);
    }
    public function test_register_user()
    {
        $response = $this->postJson('/api/register', ['name' => 'Test User', 'email' => 'test-user@polls-app.com', 'password' => 'password']);
        $response->assertStatus(201)->assertJson(['token_type' => 'Bearer', 'success' => true]);
    }
    public function test_returns_users()
    {
        $user = User::factory()->isAdmin()->create();
        $response = $this->actingAs($user)->getJson('/api/users');
        $response->assertStatus(200);
    }
    public function test_returns_a_user()
    {
        $admin = User::factory()->isAdmin()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->getJson("/api/users/1");
        $response->assertStatus(200);
    }
}
