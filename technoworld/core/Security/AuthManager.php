<?php
//core/Security/AuthManager.php
namespace Core\Security;

use Core\Security\JWTManager;
use PDO;

require_once __DIR__ . '/../../utils/http.php';

class AuthManager {
    /**
     * Возвращает payload JWT или данные по remember‑токену, либо null.
     *
     * @param PDO $pdo
     * @return array|null
     */
    public static function getValidPayload(PDO $pdo): ?array {
        $token = self::extractToken();
        if ($token) {
            $jwt = new JWTManager($_ENV['JWT_SECRET'] ?? 'dev-secret');
            $payload = $jwt->validate($token);
            if ($payload) {
                return $payload;
            }
        }
        return self::tryRememberToken($pdo);
    }


    public static function extractToken(): ?string {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return $_COOKIE['jwt'] ?? null;
    }

    /**
     * Проверяет remember_token в куках и, если валиден, возвращает payload.
     *
     * @param PDO $pdo
     * @return array|null
     */
    public static function tryRememberToken(PDO $pdo): ?array {
        $rem = $_COOKIE['remember_token'] ?? null;
        if (!$rem || !preg_match('/^[a-f0-9]{64}$/', $rem)) {
            return null;
        }
        $stmt = $pdo->prepare("SELECT id, username, permission FROM users WHERE remember_token = :t");
        $stmt->execute(['t' => $rem]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return null;
        }
        return [
            'sub'      => 'user_' . $user['id'],
            'role'     => $user['permission'] == 1 ? 'admin' : 'user',
            'username' => $user['username']
        ];
    }

    public static function requireRole(string $role, PDO $pdo): array {
        $payload = self::getValidPayload($pdo);
        if (!$payload) {
            abort(401, "Требуется авторизация");
        }
        if (($payload['role'] ?? '') !== $role) {
            abort(403, "Доступ запрещён");
        }
        return $payload;
    }
}
