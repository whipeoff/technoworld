<?php
// core/Controllers/AdminController.php

namespace Core\Controllers;

use Core\Security\AuthManager;

class AdminController extends BaseController {
  public function testPage(): void {
    $payload = AuthManager::requireRole('admin', $this->pdo);
    $this->render('adminTest.php', [
      'username' => $payload['username'] ?? 'неизвестно'
    ]);
  }
}
