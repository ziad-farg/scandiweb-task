<?php

if (!function_exists('url')) {
    function url($path = '')
    {
        // Get the base URL dynamically
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // Remove trailing slashes from the base directory

        // Remove leading slashes from the path to avoid double slashes
        $path = ltrim($path, '/');

        // Construct the URL
        $url = $protocol . '://' . $host . $base .  $path;

        return $url;
    }
}

if (!function_exists('dd')) {
    function dd(...$arr)
    {
        echo '<pre>';
        var_dump($arr);
        echo '</pre>';
        die;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header("location:{$url}");
        exit();
    }
}

if (!function_exists('back')) {
    function back()
    {
        return $_SERVER['HTTP_REFERER'];
    }
}

if (!function_exists('json')) {
    /**
     * Convert a given data to json string
     *
     * @param mixed $value
     * @param int $status
     * @return string|false
     */
    function json(mixed $value, int $status = 200): string|false
    {
        http_response_code($status);
        return json_encode($value);
    }
}
