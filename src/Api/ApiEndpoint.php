<?php
namespace App\Api;

use App\Model\Exposee;
use App\Model\ExposeeList;
use App\Utils\DB;
use App\Utils\HttpRequest;

class ApiEndpoint {
    private $basePath;

    /**
     * Class constructor
     * @param string $basePath Endpoint base path
     */
    public function __construct(string $basePath='/') {
        $this->basePath = $basePath;
    }


    /**
     * Run API endpoint
     */
    public function run() {
        $uri = HttpRequest::getUri();

        // Match static routes
        if ($uri === $this->basePath) {
            $this->getHello();
            return;
        }
        if ($uri === "{$this->basePath}exposed") {
            $this->postExposed();
            return;
        }

        // Match dynamic routes
        $exposedPattern = preg_quote("{$this->basePath}exposed/", '/') . "([0-9\-]{10})";
        if (preg_match("/^$exposedPattern\$/", $uri, $matches)) {
            $this->getExposed($matches[1]);
            return;
        }

        // Fallback to Not Found route
        $this->notFound();
    }


    /**
     * GET hello
     */
    private function getHello() {
        $this->enforceRequestMethod('GET');

        header('Content-Type: text/plain');
        echo "Hello from DP3T WS";
    }


    /**
     * POST exposed
     */
    private function postExposed() {
        $this->enforceRequestMethod('POST');

        // TODO: validate User-Agent
        // Why do we need the UA for?

        // Sanitize request body
        $body = HttpRequest::getBody();
        if (!isset($body['key'])) {
            $this->serveError(400, 'Missing "key" from request body');
            return;
        }
        $secretKey = Exposee::parseKey($body['key']);
        if (empty($secretKey)) {
            $this->serveError(400, 'Not a valid base64 key');
            return;
        }
        if (!isset($body['onset'])) {
            $this->serveError(400, 'Missing "onset" from request body');
            return;
        }
        if (!Exposee::isValidOnset($body['onset'])) {
            $this->serveError(400, 'Not a valid onset date');
            return;
        }

        // TODO: validate authData
        // Waiting for DP-3T to publish specification

        // Persist exposee in database
        DB::query('INSERT IGNORE INTO exposees (`key`, `onset`) VALUES (x?s, ?s)', bin2hex($secretKey), $body['onset']);

        // Send response
        $this->serveJson(['success' => true]);
    }


    /**
     * GET exposed
     */
    private function getExposed(string $date) {
        $this->enforceRequestMethod('GET');

        $list = ExposeeList::fromOnset($date);

        // Check whether client has the latest data already
        $this->handleEtag($list->getDigest());

        // Build response and send contents
        $exposed = [];
        foreach ($list->getExposees() as $exposee) {
            $exposed[] = [
                "key" => base64_encode($exposee->getKey()),
                "onset" => $exposee->getOnset()
            ];
        }
        $this->serveJson(['exposed' => $exposed]);
    }


    /**
     * Not found
     */
    private function notFound() {
        http_response_code(404);
    }


    /**
     * Enforce request method
     * @param string $method Allowed method
     */
    private function enforceRequestMethod(string $method) {
        if (HttpRequest::getMethod() !== $method) {
            header("Allow: $method");
            $this->serveError(405, 'Method Not Allowed');
            exit;
        }
    }


    /**
     * Handle ETag
     * @param string $digest Digest raw bytes
     */
    private function handleEtag(string $digest) {
        $serverEtag = '"' . bin2hex($digest) . '"';
        header("Etag: $serverEtag");

        // Get client request ETag
        $clientEtag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : null;
        if ($clientEtag === null) return;

        // Compare tags
        if ($clientEtag == $serverEtag) {
            http_response_code(304);
            exit;
        }
    }


    /**
     * Serve JSON response
     * @param mixed $payload Payload
     */
    private function serveJson($payload) {
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    }


    /**
     * Serve JSON error response
     * @param int    $status  HTTP status code
     * @param string $message Error message
     */
    private function serveError(int $status, string $message) {
        http_response_code($status);
        $this->serveJson(['error' => $status, 'message' => $message]);
    }
}
