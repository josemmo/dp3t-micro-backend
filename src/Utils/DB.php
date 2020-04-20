<?php
namespace App\Utils;

class DB {
    const CHARSET = "utf8";
    private static $instance = null;

    /**
     * Initialize database instance
     */
    private static function initialize() {
        if (!is_null(self::$instance)) return;

        try {
            self::$instance = new \SafeMySQL([
                "host" => $_ENV['DB_HOST'],
                "user" => $_ENV['DB_USER'],
                "pass" => $_ENV['DB_PASS'],
                "db" => $_ENV['DB_NAME'],
                "charset" => self::CHARSET
            ]);
        } catch (\Exception $e) {
            http_response_code(503);
            exit;
        }
    }


    /**
     * Call any static method
     * @param  string $method    Method name
     * @param  array  $arguments Method arguments
     * @return mixed             Response
     */
    public static function __callStatic(string $method, array $arguments) {
        self::initialize();
        return self::$instance->{$method}(...$arguments);
    }
}
