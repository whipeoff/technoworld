<?php
// core/Controllers/AuthController.php

namespace Core\Controllers;

use Core\Models\User;
use Core\Security\JWTManager;
use Core\Security\AuthManager;
use function sanitizeInput;
use function validateInputLength;

class AuthController extends BaseController
{
    public function showLoginForm(): void
    {
        $token = AuthManager::extractToken();
        if ($token) {
            $jwt     = new JWTManager($_ENV['JWT_SECRET'] ?? 'dev-secret');
            $payload = $jwt->validate($token);
            if ($payload) {
                $dest = $payload['role'] === 'admin'
                    ? '/admin'
                    : '/catalog';
                header("Location: $dest");
                exit;
            }
        }

        $this->render('loginForm.php', [
            'csrf' => $this->csrf->getInputHTML()
        ], 'auth');
    }

    public function handleLoginForm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            abort(405, "Метод не поддерживается");
        }

        $this->rateLimiter->check('login', 10);

        $username = sanitizeInput($_POST['username'] ?? '');
        $password = sanitizeInput($_POST['password'] ?? '');

        $error = validateInputLength($username, 25, 'Логин')
              ?? validateInputLength($password, 255, 'Пароль');

        if ($error) {
            $this->render('loginForm.php', [
                'csrf'  => $this->csrf->getInputHTML(),
                'error' => $error,
                'old'   => ['username' => $username]
            ], 'auth');
            return;
        }

        if ($username === '' || $password === '') {
            $this->render('loginForm.php', [
                'csrf'  => $this->csrf->getInputHTML(),
                'error' => 'Введите логин и пароль.',
                'old'   => ['username' => $username]
            ], 'auth');
            return;
        }

        $user = User::findByUsername($this->pdo, $username);
        if (!$user || !password_verify($password, $user->passwordHash)) {
            $this->render('loginForm.php', [
                'csrf'  => $this->csrf->getInputHTML(),
                'error' => 'Неверный логин или пароль.',
                'old'   => ['username' => $username]
            ], 'auth');
            return;
        }

        //вфвывфыывфыв крыша едет чухчухчух
        $jwt   = new JWTManager($_ENV['JWT_SECRET'] ?? 'dev-secret');
        $token = $jwt->generate($user->toJWT());

        setcookie('jwt', $token, [
            'expires'  => 0,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        if (isset($_POST['remember'])) {
            $rt = bin2hex(random_bytes(32));
            $user->setRememberToken($rt);
            setcookie('remember_token', $rt, [
                'expires'  => time() + 86400*30,
                'path'     => '/',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        $destination = $user->permission === 1
            ? '/admin'
            : '/catalog';

        header("Location: $destination");
        exit;
    }
}
