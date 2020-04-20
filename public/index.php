<?php
use App\Api\ApiEndpoint;
use App\Utils\HttpRequest;

require __DIR__ . "/../bootstrap.php";

$uri = HttpRequest::getUri();

// Apply common headers
header('X-Powered-By: ' . APP_NAME . '/' . APP_VERSION);

// Run API Endpoint
if (strpos($uri, '/v1/') === 0) {
    $api = new ApiEndpoint('/v1/');
    $api->run();
    exit;
}

// Fallback to not found response
http_response_code(404);
