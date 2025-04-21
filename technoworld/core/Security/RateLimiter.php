<?php
// core/Security/RateLimiter.php
namespace Core\Security;

use PDO;

require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/http.php';

class RateLimiter {
  private PDO $pdo;
  private string $ip;
  private string $fingerprint;

  public function __construct(PDO $pdo) {
    $this->pdo = $pdo;
    $this->ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $this->fingerprint = getClientFingerprint();
  }

  public function check(string $action, int $interval = 10): void {
    $stmt = $this->pdo->prepare(
      "SELECT last_action FROM rate_limits WHERE ip = :ip AND action = :action"
    );
    $stmt->execute(['ip' => $this->ip, 'action' => $action]);

    $row = $stmt->fetch();
    $now = time();

    if ($row) {
      $last = strtotime($row['last_action']);
      if ($last + $interval > $now) {
        WTL("RateLimitDB: IP {$this->ip} слишком часто выполняет действие '{$action}'.");
        abort(429, "Подождите перед повторной отправкой запроса.");
      }
    }

    $this->pdo->prepare(
      "REPLACE INTO rate_limits (ip, fingerprint, action, last_action)
       VALUES (:ip, :fingerprint, :action, NOW())"
    )->execute([
      'ip'          => $this->ip,
      'fingerprint' => $this->fingerprint,
      'action'      => $action
    ]);
  }
}
