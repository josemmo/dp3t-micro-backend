<?php
namespace App\Utils;

class HttpRequest {
    /**
     * Get method
     * @return string Request method
     */
    public static function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }


    /**
     * Get URI
     * @return string Request URI
     */
    public static function getUri(): string {
        return strtok($_SERVER['REQUEST_URI'], '?');
    }


    /**
     * Get user agent
     * @return string|null User agent
     */
    public static function getUserAgent(): ?string {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }


    /**
     * Get body
     * @return array Request body
     */
    public static function getBody(): array {
        $contentType = $_SERVER['HTTP_CONTENT_TYPE'] ?? "";

        // Handle JSON payload
        if (strcasecmp($contentType, 'application/json') === 0) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // Fallback to default behavior
        return $_POST;
    }
}
