<?php
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

class LoadTest extends AbstractTest {
    const NUMBER_OF_KEYS = 10000;

    /**
     * Simulate upload of exposee keys
     */
    public function testSimulateUploads() {
        $client = $this->getHttpClient();
        $onset = date('Y-m-d');

        // Define requests generator
        $requests = function ($total) use ($client, $onset) {
            for ($i=0; $i<$total; $i++) {
                yield function() use ($client, $onset) {
                    return $client->postAsync('/v1/exposed', [
                        'json' => ['key' => $this->getRandomKey(), 'onset' => $onset]
                    ]);
                };
            }
        };

        // Send requests
        $successCount = 0;
        $pool = new Pool($client, $requests(self::NUMBER_OF_KEYS), [
            'concurrency' => 300,
            'fulfilled' => function (Response $response) use (&$successCount) {
                if ($response->getStatusCode() == 200) $successCount++;
            }
        ]);
        $startTime = microtime(true);
        $pool->promise()->wait();
        $endTime = microtime(true);

        // Validate success rate
        $avgTimePerRequest = ($endTime-$startTime) / self::NUMBER_OF_KEYS;
        $this->assertEquals(self::NUMBER_OF_KEYS, $successCount);

        // Print results
        echo "\n\n=== LOAD TEST RESULTS ===\n";
        echo "Total requests       : " . self::NUMBER_OF_KEYS . "\n";
        echo "Successful requests  : $successCount\n";
        echo "Average request time : $avgTimePerRequest seconds\n";
    }
}
