<?php
// core/init.php

spl_autoload_register(function(string $className) {
    $paths = [
        __DIR__ . '/Config/',
        __DIR__ . '/Controllers/',
        __DIR__ . '/Models/',
        __DIR__ . '/Security/',
    ];

    $parts = explode('\\', $className);
    $file  = end($parts) . '.php';

    foreach ($paths as $dir) {
        $fullPath = $dir . $file;
        if (is_readable($fullPath)) {
            require_once $fullPath;
            return;
        }
    }
});

require_once __DIR__ . '/../utils/helpers.php';
require_once __DIR__ . '/../utils/http.php';

use Core\Config\Database;
$dbConfig = new Database();
$pdo      = $dbConfig->getPDO();

use Core\Security\CSRFManager;
startSessionIfNeeded(); 
$csrf = new CSRFManager(); 
$csrf->requireValid();

use Core\Security\RateLimiter;
$rateLimiter = new RateLimiter($pdo);

$GLOBALS['pdo']         = $pdo;
$GLOBALS['csrf']        = $csrf;
$GLOBALS['rateLimiter'] = $rateLimiter;
