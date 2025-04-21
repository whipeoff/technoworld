<?php
/**
 * utils/helpers.php
 *
 * Вспомогательные функции
 */

require_once __DIR__ . '/http.php';

/**
 * getLogFilePath
 *
 * Возвращает абсолютный путь к лог-файлу, заданному относительно корня проекта.
 * Путь "logs/website.log" преобразуется в абсолютный с учётом операционной системы.
 * Если директория не существует — она создаётся.
 *
 * @return string Абсолютный путь к лог-файлу.
 */
function getLogFilePath(): string {
  $relativePath = 'logs/website.log';

  if (!preg_match('/^(\/|[A-Za-z]:[\/\\\\])/', $relativePath)) {
    $projectRoot = realpath(__DIR__ . '/../');
    $logPath = $projectRoot . DIRECTORY_SEPARATOR . $relativePath;
  } else {
    $logPath = $relativePath;
  }

  $logDir = dirname($logPath);
  if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
  }

  return $logPath;
}

/**
 * WTL()
 * writetolog
 * Записывает заданное сообщение об ошибке в лог-файл.
 * Путь к лог-файлу определяется через функцию getLogFilePath, чтобы быть уверенным, что он корректный.
 * Каждое сообщение дополняется символом новой строки для удобства чтения в лог-файле.
 *
 * @param string $message Сообщение, которое необходимо залогировать.
 * @return void
 */
function WTL($message): void {
  $dt = new DateTime('now', new DateTimeZone('Europe/Moscow'));
  $logTime = $dt->format('Y-m-d H:i:s');
  $logFile = getLogFilePath();
  error_log($logTime . ": " . $message . "\n", 3, $logFile);
}

/**
 * Проверяет статус сессии и запускает её, если она не активна.
 *
 * @return void
 */
function startSessionIfNeeded(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    if (session_start() === false) {
      abort(500, "Ошибка: не удалось запустить сессию.");
    }
  }
}

/**
 * Формирует отпечаток (fingerprint) клиента на основе IP-адреса и User-Agent.
 * Используется для привязки сессий и CSRF-токена к конкретному клиенту.
 * При отсутствии одного из компонентов логирует предупреждение.
 *
 * @return string 64-символьный SHA256-хеш отпечатка клиента
 */
function getClientFingerprint(): string {
  $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';

  if ($ua === '' || $ip === '') {
    WTL("Fingerprint: Пустой IP или User-Agent, возможно нестандартный клиент.");
  }

  return hash('sha256', $ua . $ip);
}

/**
 * validateUri()
 * 
 * Валидирует и нормализует URI, пригодный для роутинга.
 * Удаляет лишние слеши, запрещённые символы, обнуляет попытки path traversal.
 *
 * @param string $uri Входной URI из $_SERVER['REQUEST_URI']
 * @return string Безопасный нормализованный URI
 */
function validateUri(string $uri): string {
  $parsed = parse_url($uri, PHP_URL_PATH);
  $normalized = rtrim(preg_replace('#/+#', '/', $parsed), '/');

  if (!mb_check_encoding($normalized, 'UTF-8')) {
    abort(400, "Недопустимая кодировка в URI.");
  }
  if (strpos($normalized, '..') !== false) {
    abort(400, "Попытка обхода директорий в URI.");
  }

  return $normalized === '' ? '/' : $normalized;
}


/**
 * Очищает входную строку:
 * - убирает управляющие символы
 * - удаляет HTML‑теги
 * - обрезает пробелы по краям
 *
 * @param string $input
 * @return string
 */
function sanitizeInput(string $input): string {
  $input = preg_replace('/[\x00-\x1F\x7F]/u', '', $input);
  $input = strip_tags($input);
  return trim($input);
}

/**
 * Проверяет, что строка не длиннее max
 * 
 * @param string $input
 * @param int $maxLength
 * @param string $fieldName — для сообщения об ошибке
 * @return string|null — null если ок, строка‑ошибка если не ок
 */
function validateInputLength(string $input, int $maxLength, string $fieldName): ?string {
  if (mb_strlen($input) > $maxLength) {
      return "Поле «$fieldName » не должно превышать $maxLength символов.";
  }
  return null;
}

function slugify(string $text): string
{

    $text = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = preg_replace('~[^\p{L}\p{Nd}]+~u', '-', $text);
    $text = preg_replace('~[^\-_\w]+~u', '', $text);
    $text = preg_replace('~-+~', '-', trim($text, '-'));

    return mb_strtolower($text, 'UTF-8');
}
