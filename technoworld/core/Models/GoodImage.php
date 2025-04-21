<?php
// core/Models/GoodImage.php

namespace Core\Models;

use PDO;

class GoodImage {
    public int $id;
    public int $good_id;
    public string $filename;
    public bool $is_main;

    public function __construct(array $row) {
        $this->id       = (int)$row['id'];
        $this->good_id  = (int)$row['good_id'];
        $this->filename = $row['filename'];
        $this->is_main  = (bool)$row['is_main'];
    }

    /**
     * Возвращает все изображения для товара.
     *
     * @return GoodImage[]
     */
    public static function findByGoodId(PDO $pdo, int $goodId): array {
        $stmt = $pdo->prepare(
            "SELECT * FROM goods_images
             WHERE good_id = :gid
             ORDER BY is_main DESC, id ASC"
        );
        $stmt->execute(['gid' => $goodId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new self($r), $rows);
    }

    /**
     * Возвращает главную картинку товара или null.
     */
    public static function findMainByGoodId(PDO $pdo, int $goodId): ?self {
        $stmt = $pdo->prepare(
            "SELECT * FROM goods_images
             WHERE good_id = :gid AND is_main = 1
             LIMIT 1"
        );
        $stmt->execute(['gid' => $goodId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new self($row) : null;
    }

    public static function add(PDO $pdo, int $goodId, string $filename, bool $isMain): void {
        $stmt = $pdo->prepare(
            "INSERT INTO goods_images (good_id, filename, is_main)
             VALUES (:gid, :fn, :im)"
        );
        $stmt->execute([
            'gid' => $goodId,
            'fn'  => $filename,
            'im'  => $isMain ? 1 : 0,
        ]);
    }
}
