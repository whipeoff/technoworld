<?php
namespace Core\Security;

/**
 * core/Security/CSRFManager.php
 *
 * Генерация и проверка CSRF токенов.
 */

require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/http.php';

class CSRFManager {
  private string $tokenKey;
  private int $ttl;

  /**
   * Конструктор инициализирует ключ и TTL токена. Запускает сессию.
   *
   * @param string $tokenKey Ключ в сессии
   * @param int $ttl Время жизни токена в секундах
   */
  public function __construct(string $tokenKey = 'csrf_token', int $ttl = 900) {
    $this->tokenKey = $tokenKey;
    $this->ttl = $ttl;
    startSessionIfNeeded();
  }

  /**
   * Генерирует CSRF-токен, сохраняет в сессии:
   * - сам токен
   * - время генерации
   * - fingerprint клиента
   *
   * @return string Сгенерированный токен
   */
  public function generate(): string {
    $token = bin2hex(random_bytes(32));
    $_SESSION[$this->tokenKey] = $token;
    $_SESSION[$this->tokenKey . '_time'] = time();
    $_SESSION[$this->tokenKey . '_fp'] = getClientFingerprint();
    return $token;
  }

  /**
   * Возвращает существующий токен или генерирует новый.
   *
   * @return string
   */
  public function getToken(): string {
    return $_SESSION[$this->tokenKey] ?? $this->generate();
  }

  /**
   * Проверяет валидность CSRF-токена:
   * - соответствует формату
   * - совпадает с сессионным
   * - не истёк TTL
   * - fingerprint совпадает
   *
   * В конце удаляет токен (одноразовый).
   *
   * @param string $tokenFromForm Токен из формы
   * @return bool
   */
  public function isValid(string $tokenFromForm): bool {
    if (strlen($tokenFromForm) !== 64 || !ctype_xdigit($tokenFromForm)) {
      WTL("CSRF: Некорректный формат токена: {$tokenFromForm}");
      return false;
    }

    $stored = $_SESSION[$this->tokenKey] ?? null;
    $time   = $_SESSION[$this->tokenKey . '_time'] ?? 0;
    $fp     = $_SESSION[$this->tokenKey . '_fp'] ?? '';

    $isValid = (
      $stored &&
      hash_equals($stored, $tokenFromForm) &&
      ($time + $this->ttl) >= time() &&
      $fp === getClientFingerprint()
    );

    unset(
      $_SESSION[$this->tokenKey],
      $_SESSION[$this->tokenKey . '_time'],
      $_SESSION[$this->tokenKey . '_fp']
    );

    return $isValid;
  }


  public function getInputHTML(): string {
    $token = htmlspecialchars($this->getToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="' . $this->tokenKey . '" value="' . $token . '">';
  }

  public function requireValid(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $token = $_POST[$this->tokenKey] ?? '';
      if (!$this->isValid($token)) {
        abort(419, "Сессия истекла", "Пожалуйста, обновите страницу и отправьте форму заново.");
      }
    }
  }
}
