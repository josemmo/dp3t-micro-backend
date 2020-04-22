<?php
class ExposedTest extends AbstractTest {
    /**
     * Test creation and listing of exposees
     */
    public function testExposees() {
        $client = $this->getHttpClient();
        $onset = date('Y-m-d');
        $keys = [];
        for ($i=0; $i<10; $i++) {
            $keys[] = $this->getRandomKey();
        }

        // Check invalid request method
        $this->assertEquals(405, $client->request('GET', '/v1/exposed')->getStatusCode());

        // Upload exposees
        foreach ($keys as $i=>$key) {
            $payload = ['key' => $key, 'onset' => $onset];
            $type = ($i%2 == 0) ? "json" : "form_params";
            $this->assertEquals(200, $client->request('POST', '/v1/exposed', [$type => $payload])->getStatusCode());
        }

        // Upload some duplicate exposees (should be ignored)
        $dupeOffset = date('Y-m-d', strtotime('-2 days'));
        foreach (array_slice($keys, 0, 3) as $key) {
            $payload = ['key' => $key, 'onset' => $dupeOffset];
            $this->assertEquals(200, $client->request('POST', '/v1/exposed', ['json' => $payload])->getStatusCode());
        }

        // List exposees
        $list = $client->request('GET', "/v1/exposed/$onset");
        $this->assertEquals(200, $list->getStatusCode());
        $res = json_decode($list->getBody(), true);
        if (!isset($res['exposed'])) $this->fail('Missing "exposed" field from response body');

        // Check existence of all uploaded keys
        $wsKeys = [];
        foreach ($res['exposed'] as $item) {
            $this->assertEquals($onset, $item['onset']);
            $wsKeys[] = $item['key'];
        }
        $this->assertEmpty(array_diff($keys, $wsKeys), 'Missing some uploaded exposees from response');
    }


    /**
     * Test upload of malformed exposees
     */
    public function testMalformedExposees() {
        $client = $this->getHttpClient();
        $payloads = [
            ['key' => 'not_a_valid_key', 'onset' => date('Y-m-d')],
            ['key' => str_repeat('x', 44), 'onset' => date('Y-m-d')],
            ['key' => base64_encode(random_bytes(10)), 'onset' => date('Y-m-d')],
            ['key' => base64_encode(random_bytes(50)), 'onset' => date('Y-m-d')],
            ['key' => base64_encode(random_bytes(50)), 'onset' => date('Y-m-d')],
            ['key' => $this->getRandomKey(), 'onset' => 'not_a_valid_date'],
            ['key' => $this->getRandomKey(), 'onset' => '2020-01-01'],
            ['key' => $this->getRandomKey(), 'onset' => '2099-01-01'],
            ['key' => $this->getRandomKey(), 'onset' => '2020-12-34'],
            ['key' => $this->getRandomKey(), 'onset' => '0123456789']
        ];
        foreach ($payloads as $payload) {
            $this->assertEquals(400, $client->request('POST', '/v1/exposed', ['json' => $payload])->getStatusCode());
        }
    }


    /**
     * Test cache from exposees list
     */
    public function testCache() {
        $client = $this->getHttpClient();
        $onset = date('Y-m-d');

        // Get initial ETag
        $etags = [];
        for ($i=0; $i<3; $i++) {
            $response = $client->request('GET', "/v1/exposed/$onset");
            if (!$response->hasHeader('Cache-Control')) $this->fail('Missing "Cache-Control" header from response');
            $etags[] = $response->getHeader('Etag')[0];
        }
        if (count(array_unique($etags)) !== 1) $this->fail('ETag changed without modifying exposees');

        // Test If-None-Match
        $etag = reset($etags);
        $this->assertEquals(304,
            $client->request('GET', "/v1/exposed/$onset", ['headers' => ['If-None-Match' => $etag]])->getStatusCode());
        $this->assertEquals(200,
            $client->request('GET', "/v1/exposed/$onset", ['headers' => ['If-None-Match' => '"a"']])->getStatusCode());

        // Force cache change
        $client->request('POST', '/v1/exposed', [
            'json' => ['key' => $this->getRandomKey(), 'onset' => $onset]
        ]);
        $response = $client->request('GET', "/v1/exposed/$onset", ['headers' => ['If-None-Match' => $etag]]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEquals($etag, $response->getHeader('Etag')[0]);
    }
}
