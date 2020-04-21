<?php
namespace App\Utils;

/**
 * Dumb and lightweight ".env" file parser
 */
class DotEnv {
    /**
     * Get environment variable
     * @param  string      $name Variable name
     * @return string|null       Variable value or NULL if not defined
     */
    public static function get(string $name): ?string {
        $value = $_ENV[$name] ?? getenv($name);
        return ($value === false) ? null : $value;
    }


    /**
     * Load to $_ENV superglobal
     * @param string $path Path to ".env" file
     */
    public static function load(string $path) {
        if (!is_file($path)) return;

        // Read file contents
        $data = file_get_contents($path);
        $data = str_replace(["\r\n", "\n\r", "\r"], "\n", $data);
        $data = explode("\n", $data);

        // Extract environment variables
        foreach ($data as $line) {
            if (empty($line) || (strpos($line, '#') === 0)) continue;
            list($field, $value) = explode('=', $line, 2);
            $_ENV[$field] = $value;
        }
    }


    /**
     * Require fields to be defined
     * @param  string[]   $fields Environment variable names
     * @throws \Exception         Missing required environment variables exception
     */
    public static function required(array $fields) {
        $missing = [];
        foreach ($fields as $field) {
            if (self::get($field) === null) $missing[] = $field;
        }
        if (!empty($missing)) {
            $missing = implode(', ', $missing);
            throw new \Exception("The following required environment variables were not found: $missing");
        }
    }
}
