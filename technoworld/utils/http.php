<?php
// utils/http.php

require_once __DIR__ . '/helpers.php';

function abort(int $code = 500, ?string $message = null): void {
    WTL("ABORT {$code}: {$message}");
    renderError($code, $message);
    exit;
}

function renderError(int $code, ?string $message = null): void {
    http_response_code($code);

    $backUrl = $_SERVER['HTTP_REFERER'] ?? ($_SERVER['REQUEST_URI'] ?? '/');
    $encodedBackUrl = htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8');
    $metaRefresh = '<meta http-equiv="refresh" content="3;URL=' . $encodedBackUrl . '">';

    $safeMessage    = $message ? htmlspecialchars($message, ENT_QUOTES, 'UTF-8') : 'Произошла ошибка.';
    $errorViewPath  = __DIR__ . '/../views/errors/' . $code . '.php';

    if (!file_exists($errorViewPath)) {
        echo "<h1>{$code} Ошибка</h1><p>{$safeMessage}</p>";
        exit;
    }

    $title           = "Ошибка {$code}";
    $metaDescription = $safeMessage;
    $metaRobots      = 'noindex, nofollow';
    $canonicalUrl    = $encodedBackUrl;

    ob_start();
    require $errorViewPath;
    $content = ob_get_clean();

    require __DIR__ . '/../views/layouts/error.php';
    exit;
}
