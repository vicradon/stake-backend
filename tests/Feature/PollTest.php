<?php

namespace Tests\Feature;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PollTest extends TestCase
{

    use RefreshDatabase;

    public function test_returns_users_polls()
    {
        $user = User::factory()->has(Poll::factory()->count(5))->create();
        $response = $this->actingAs($user)->getJson('/api/polls');
        $response->assertStatus(200)->assertJson(['polls_count' => 5]);
    }

    public function test_creates_a_poll()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/polls', ['title' => "National Poll", 'description' => 'A nationwide poll for students']);
        $response->assertStatus(201)->assertJson(['data' => ['title' => 'National Poll', 'description' => 'A nationwide poll for students']]);
    }
    public function test_updates_a_poll()
    {
        $user = User::factory()->create();
        $poll = Poll::create(['title' => 'National Poll', 'description' => 'A nationwide poll for students']);
        $user->polls()->save($poll);
        $poll_id = $poll->id;

        $response = $this->actingAs($user)->patchJson("/api/polls/{$poll_id}", ['title' => 'State Poll']);
        $response->assertStatus(200)->assertJson(['data' => ['title' => 'State Poll']]);
    }
    public function test_deletes_a_poll()
    {
        $user = User::factory()->create();
        $poll = Poll::create(['title' => 'National Poll', 'description' => 'A nationwide poll for students']);
        $user->polls()->save($poll);
        $poll_id = $poll->id;

        $response = $this->actingAs($user)->deleteJson("/api/polls/${poll_id}");
        $response->assertStatus(200);
    }
}
