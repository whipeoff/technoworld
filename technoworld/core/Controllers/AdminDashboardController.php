<?php
// core/Controllers/AdminDashboardController.php

namespace Core\Controllers;

use Core\Security\AuthManager;
use Core\Security\JWTManager;

class AdminDashboardController extends BaseController
{
    public function index(): void
    {
        $token = AuthManager::extractToken();

        if (!$token) {
            header('Location: /login');
            exit;
        }

        $jwt = new JWTManager($_ENV['JWT_SECRET'] ?? 'dev-secret');
        $payload = $jwt->validate($token);

        if (!$payload) {
            header('Location: /login');
            exit;
        }

        if (!isset($payload['role']) || $payload['role'] !== 'admin') {
            abort(403, "Доступ запрещён", "У вас нет прав для просмотра этой страницы.");
        }

        $this->render('admin/dashboard.php', [
            'username'        => $payload['username'] ?? 'Админ',
            'title'           => 'Админ‑панель Technoworld',
            'metaDescription' => 'Защищённая админ‑панель.',
            'metaRobots'      => 'noindex, nofollow',
            'canonicalUrl'    => 'http://techno-world.free.nf/admin',
        ], 'base');
    }
}
