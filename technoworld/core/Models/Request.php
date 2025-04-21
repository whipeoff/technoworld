<?php
namespace Core\Models;

use PDO;
use PDOException;
use function WTL;

class Request
{

    public static function create(PDO $pdo, array $data): void
    {
        try {
            $stmt = $pdo->prepare(<<<'SQL'
                INSERT INTO requests (name, phone, comment, created_at)
                VALUES (:name, :phone, :comment, NOW())
            SQL
            );
            $stmt->execute([
                ':name'    => $data['name'],
                ':phone'   => $data['phone'],
                ':comment' => $data['comment'] ?? null,
            ]);
        } catch (PDOException $e) {
            WTL("Ошибка вставки заявки: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getAll(PDO $pdo): array
    {
        try {
            $stmt = $pdo->query(
                'SELECT id, name, phone, comment, created_at
                 FROM requests
                 ORDER BY created_at DESC'
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            WTL("Ошибка чтения заявок: " . $e->getMessage());
            return [];
        }
    }
}
