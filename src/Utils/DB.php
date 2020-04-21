<?php
namespace App\Utils;

use App\Utils\DotEnv;

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
                "host" => DotEnv::get('DB_HOST'),
                "user" => DotEnv::get('DB_USER'),
                "pass" => DotEnv::get('DB_PASS'),
                "db" => DotEnv::get('DB_NAME'),
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
