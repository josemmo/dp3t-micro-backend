<?php
use App\Utils\DotEnv;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

abstract class AbstractTest extends TestCase {
    const DEFAULT_BASE_URI = "http://localhost";

    private static $httpClient = null;

    /**
     * Get HTTP client
     * @return Client HTTP client instance
     */
    protected function getHttpClient(): Client {
        if (self::$httpClient === null) {
            self::$httpClient = new Client([
                'base_uri' => DotEnv::get('TEST_BASE_URI') ?? self::DEFAULT_BASE_URI,
                'http_errors' => false
            ]);
        }
        return self::$httpClient;
    }


    /**
     * Get random exposee key
     * @param  bool   $raw Return raw bytes or Base64-encoded string
     * @return string      Exposee key
     */
    protected function getRandomKey(bool $raw=false): string {
        $res = random_bytes(32);
        return $raw ? $res : base64_encode($res);
    }
}
