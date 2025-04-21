<?php
// core/Security/JWTManager.php

namespace Core\Security;

class JWTManager {
  private string $secret;
  private int $ttl;

  public function __construct(string $secret, int $ttlSeconds = 3600) {
    $this->secret = $secret;
    $this->ttl    = $ttlSeconds;
  }

  public function generate(array $payload): string {
    $header = ['alg'=>'HS256','typ'=>'JWT'];
    $payload['iat'] = time();
    $payload['exp'] = time() + $this->ttl;

    $bH = $this->base64UrlEncode(json_encode($header));
    $bP = $this->base64UrlEncode(json_encode($payload));
    $sig = hash_hmac('sha256', "$bH.$bP", $this->secret, true);
    $bS  = $this->base64UrlEncode($sig);

    return "$bH.$bP.$bS";
  }

  public function validate(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts)!==3) return null;
    [$h,$p,$s] = $parts;
    $check = hash_hmac('sha256', "$h.$p", $this->secret, true);
    if (!hash_equals($s, $this->base64UrlEncode($check))) return null;
    $data = json_decode($this->base64UrlDecode($p), true);
    if (!$data || time() > ($data['exp'] ?? 0)) return null;
    return $data;
  }

  private function base64UrlEncode(string $d): string {
    return rtrim(strtr(base64_encode($d), '+/', '-_'), '=');
  }
  private function base64UrlDecode(string $d): string {
    return base64_decode(strtr($d, '-_', '+/'));
  }
}
