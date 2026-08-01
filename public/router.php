<?php

/**
 * Router for Wasmer Edge / PHP built-in server.
 * PHPIx cannot evaluate Laravel's RewriteRule .htaccess, so we route here.
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$path = __DIR__.$uri;

if ($uri !== '/' && file_exists($path) && ! is_dir($path)) {
    return false;
}

require_once __DIR__.'/index.php';
