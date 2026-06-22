<?php

namespace Tests\Feature;

use App\Events\CounterUpdated;
use App\Models\Counter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
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
     * Test incrementing the counter value and broadcasting.
     */
    public function test_can_increment_counter_value(): void
    {
        Event::fake();

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

        Event::assertDispatched(CounterUpdated::class, function (CounterUpdated $event) {
            return $event->value === 6;
        });
    }

    /**
     * Test decrementing the counter value and broadcasting.
     */
    public function test_can_decrement_counter_value(): void
    {
        Event::fake();

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

        Event::assertDispatched(CounterUpdated::class, function (CounterUpdated $event) {
            return $event->value === 9;
        });
    }
}
