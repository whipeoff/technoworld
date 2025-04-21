<?php
// core/Config/Database.php

namespace Core\Config;

use PDO;
use PDOException;
use Exception;

require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/http.php';

class Database {
  private string $driver;
  private string $host;
  private int $port;
  private string $user;
  private string $pass;
  private string $name;
  private string $charset;
  private string $dsn;
  private PDO $pdo;

  public function __construct() {
    $this->loadEnv();
    $this->validate();
    $this->buildDSN();
    $this->connect();
  }

  private function loadEnv(): void {
    $envPath = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . '.env';
    if (!file_exists($envPath)) {
      abort(500, "Ошибка конфигурации", ".env файл не найден по пути {$envPath}");
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      $line = trim($line);
      if (strpos($line, '#') === 0 || !str_contains($line, '=')) {
        continue;
      }
      [$key, $value] = explode('=', $line, 2);
      $_ENV[trim($key)] = trim($value);

      if ($key === '') {
        WTL("Warning: .env содержит строку без ключа: '{$line}'");
        continue;
      }
      if ($value === '') {
        WTL("Warning: .env содержит строку без значения: '{$line}'");
        continue;
      }
    }

    $this->driver  = $_ENV['DB_DRIVER'] ?? '';
    $this->host    = $_ENV['DB_HOST'] ?? '';
    $this->port    = (int) ($_ENV['DB_PORT'] ?? 0);
    $this->user    = $_ENV['DB_USER'] ?? '';
    $this->pass    = $_ENV['DB_PASS'] ?? '';
    $this->name    = $_ENV['DB_NAME'] ?? '';
    $this->charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
  }

  private function validate(): void {
    if (!in_array($this->driver, ['mysql', 'pgsql', 'sqlite', 'sqlsrv'])) {
      abort(500, "Ошибка конфигурации", "Неподдерживаемый драйвер БД: {$this->driver}");
    }

    if (!preg_match('/^[\w\-]+$/', $this->user)) {
      abort(500, "Ошибка конфигурации", "Недопустимый логин БД: {$this->user}");
    }

    if ($this->pass === '') {
      abort(500, "Ошибка конфигурации", "Пустой пароль БД.");
    }

    if ($this->driver === 'sqlite') {
      if (!file_exists($this->name) && !is_writable(dirname($this->name))) {
        abort(500, "Ошибка SQLite", "Файл не существует или не может быть создан: {$this->name}");
      }
    } else {
      if (!preg_match('/^[\w\-]+$/', $this->name)) {
        abort(500, "Ошибка конфигурации", "Недопустимое имя базы данных: {$this->name}");
      }

      if ($this->port < 1 || $this->port > 65535) {
        abort(500, "Ошибка конфигурации", "Порт БД вне допустимого диапазона: {$this->port}");
      }

      $fp = @fsockopen($this->host, $this->port, $errno, $errstr, 2);
      if (!$fp) {
        abort(500, "Не удалось подключиться к базе данных.", "Сервер БД {$this->host}:{$this->port} — {$errstr} ({$errno})");
      }
      fclose($fp);
    }
  }

  private function buildDSN(): void {
    switch ($this->driver) {
      case 'mysql':
        $this->dsn = "mysql:host={$this->host};dbname={$this->name};charset={$this->charset}";
        break;
      case 'pgsql':
        $this->dsn = "pgsql:host={$this->host};dbname={$this->name}";
        break;
      case 'sqlsrv':
        $this->dsn = "sqlsrv:Server={$this->host};Database={$this->name}";
        break;
      case 'sqlite':
        $this->dsn = "sqlite:{$this->name}";
        break;
    }
  }

  private function connect(): void {
    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
      $this->pdo = new PDO($this->dsn, $this->user, $this->pass, $options);
      $this->pdo->exec("SET time_zone = '+00:00'");
    } catch (PDOException $e) {
      abort(500, "Ошибка подключения к базе данных.", "PDOException: " . $e->getMessage());
    }
  }

  public function getPDO(): PDO {
    return $this->pdo;
  }
}
