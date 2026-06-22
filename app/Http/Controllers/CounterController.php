<?php

namespace App\Http\Controllers;

use App\Events\CounterUpdated;
use App\Models\Counter;
use Illuminate\Http\JsonResponse;

class CounterController extends Controller
{
    /**
     * Get the default counter or create it if it doesn't exist.
     */
    private function getCounter(): Counter
    {
        return Counter::firstOrCreate(
            ['name' => 'default'],
            ['value' => 0]
        );
    }

    /**
     * Display the current counter value.
     */
    public function show(): JsonResponse
    {
        $counter = $this->getCounter();

        return response()->json([
            'success' => true,
            'name' => $counter->name,
            'value' => $counter->value,
        ]);
    }

    /**
     * Increment the counter value by 1.
     */
    public function increment(): JsonResponse
    {
        $counter = $this->getCounter();
        $counter->increment('value');

        CounterUpdated::dispatch($counter->value);

        return response()->json([
            'success' => true,
            'name' => $counter->name,
            'value' => $counter->value,
        ]);
    }

    /**
     * Decrement the counter value by 1.
     */
    public function decrement(): JsonResponse
    {
        $counter = $this->getCounter();
        $counter->decrement('value');

        CounterUpdated::dispatch($counter->value);

        return response()->json([
            'success' => true,
            'name' => $counter->name,
            'value' => $counter->value,
        ]);
    }
}
