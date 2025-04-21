<?php
//Core/Models/User.php

namespace Core\Models;

class User {
  public int    $id;
  public string $username;
  public string $passwordHash;
  public int    $permission;

  // Находит по логину или возвращает null
  public static function findByUsername(\PDO $pdo, string $username): ?self {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute(['u' => $username]);
    $row = $stmt->fetch();
    return $row ? self::fromRow($row) : null;
  }

  private static function fromRow(array $row): self {
    $u = new self();
    $u->id           = (int)$row['id'];
    $u->username     = $row['username'];
    $u->passwordHash = $row['password'];
    $u->permission   = (int)$row['permission'];
    return $u;
  }

  public function checkPassword(string $plain): bool {
    return password_verify($plain, $this->passwordHash);
  }

  public function toJWT(): array {
    return [
      'sub'      => 'user_' . $this->id,
      'role'     => $this->permission === 1 ? 'admin' : 'user',
      'username' => $this->username
    ];
  }

  public function setRememberToken(string $token): void {
    $stmt = $GLOBALS['pdo']->prepare(
      "UPDATE users SET remember_token = :t WHERE id = :id"
    );
    $stmt->execute(['t' => $token, 'id' => $this->id]);
  }
}
