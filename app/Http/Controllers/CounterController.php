<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Counter;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return response()->json([
            'success' => true,
            'name' => $counter->name,
            'value' => $counter->value,
        ]);
    }

    /**
     * Stream counter changes to the client via Server-Sent Events (SSE).
     */
    public function stream(): StreamedResponse
    {
        $response = new StreamedResponse(function (): void {
            // Keep track of the last value we sent to minimize database queries / response size
            $lastValue = null;
            $start = time();

            // Limit execution time to 20 seconds to prevent blocking the PHP development server threads
            while (time() - $start < 20) {
                // If connection is aborted by client, terminate the loop
                if (connection_aborted()) {
                    break;
                }

                $counter = Counter::where('name', 'default')->first();
                $currentValue = $counter ? $counter->value : 0;

                // Only send data when the value actually changes, or send a heartbeat
                if ($lastValue === null || $currentValue !== $lastValue) {
                    $lastValue = $currentValue;
                    echo 'data: '.json_encode(['value' => $currentValue])."\n\n";
                    ob_flush();
                    flush();
                } else {
                    // Send a keep-alive comment/heartbeat to keep the connection open
                    echo ": heartbeat\n\n";
                    ob_flush();
                    flush();
                }

                // Check for updates every 200 milliseconds (0.2 seconds) for ultra-fast response times
                usleep(200000);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no'); // Disable buffering on Nginx/Ngrok

        return $response;
    }
}
