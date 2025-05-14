<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// Check if we should prevent environment reload
$preventReload = file_exists(__DIR__.'/storage/app/prevent_env_reload');

// If we're in the setup process, set an environment variable to tell Laravel not to reload
if ($preventReload) {
    $_ENV['LARAVEL_PREVENT_ENV_RELOAD'] = true;
    putenv('LARAVEL_PREVENT_ENV_RELOAD=true');
}

require_once __DIR__.'/public/index.php'; 