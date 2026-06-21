<?php

namespace Tests\Feature;

use App\Models\Counter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving the initial counter value.
     */
    public function test_can_get_initial_counter_value(): void
    {
        // Counter does not exist, so GET request should automatically create and return it with value 0
        $response = $this->getJson(route('api.counter.show'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'name' => 'default',
                'value' => 0,
            ]);

        $this->assertDatabaseHas('counters', [
            'name' => 'default',
            'value' => 0,
        ]);
    }

    /**
     * Test incrementing the counter value.
     */
    public function test_can_increment_counter_value(): void
    {
        // Seed default counter
        Counter::create(['name' => 'default', 'value' => 5]);

        $response = $this->postJson(route('api.counter.increment'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'name' => 'default',
                'value' => 6,
            ]);

        $this->assertDatabaseHas('counters', [
            'name' => 'default',
            'value' => 6,
        ]);
    }

    /**
     * Test decrementing the counter value.
     */
    public function test_can_decrement_counter_value(): void
    {
        // Seed default counter
        Counter::create(['name' => 'default', 'value' => 10]);

        $response = $this->postJson(route('api.counter.decrement'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'name' => 'default',
                'value' => 9,
            ]);

        $this->assertDatabaseHas('counters', [
            'name' => 'default',
            'value' => 9,
        ]);
    }

    /**
     * Test getting the counter stream response.
     */
    public function test_can_get_counter_stream(): void
    {
        $response = $this->get(route('api.counter.stream'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/event-stream; charset=utf-8');
        $response->assertHeader('Cache-Control', 'no-cache, private');
    }
}
