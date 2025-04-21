<?php
// core/Controllers/AdminRequestsController.php

namespace Core\Controllers;

use Core\Models\Request;

class AdminRequestsController extends BaseController
{
    public function index(): void
    {
        $token = \Core\Security\AuthManager::extractToken();
        if (!$token) {
            header('Location: /login');
            exit;
        }

        $jwt     = new \Core\Security\JWTManager($_ENV['JWT_SECRET'] ?? 'dev-secret');
        $payload = $jwt->validate($token);

        if (!$payload || ($payload['role'] ?? '') !== 'admin') {
            abort(403, "Доступ запрещён");
        }

        $requests = Request::getAll($this->pdo);

        $this->render('admin/requests.php', [
            'title'           => 'Заявки',
            'metaDescription' => 'Просмотр заявок от клиентов.',
            'metaRobots'      => 'noindex, nofollow',
            'canonicalUrl'    => 'http://techno-world.free.nf/admin/requests',
            'requests'        => $requests,
        ], 'base');
    }
}
