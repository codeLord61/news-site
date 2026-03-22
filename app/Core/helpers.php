<?php

/**
 * Generate a dynamic URL relative to the project root.
 * 
 * @param string $path
 * @return string
 */
function url(string $path = ''): string
{
    $baseUrl = \app\core\App::$PROJECT_ROOT_URL;
    // Ensure path starts with /
    $path = '/' . ltrim($path, '/');
    return $baseUrl . $path;
}
