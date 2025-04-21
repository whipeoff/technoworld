<?php
// core/Controllers/BaseController.php

namespace Core\Controllers;

use Core\Security\CSRFManager;
use Core\Security\RateLimiter;
use PDO;

abstract class BaseController {
    protected PDO $pdo;
    protected CSRFManager $csrf;
    protected RateLimiter $rateLimiter;

    public function __construct() {
        $this->pdo         = $GLOBALS['pdo'];
        $this->csrf        = $GLOBALS['csrf'];
        $this->rateLimiter = $GLOBALS['rateLimiter'];
    }

    protected function render(string $view, array $params = [], string $layout = 'auth'): void {
        extract($params, EXTR_SKIP);

        ob_start();
        require __DIR__ . '/../../views/' . $view;
        $content = ob_get_clean();

        require __DIR__ . '/../../views/layouts/' . $layout . '.php';
        exit;
    }
}
