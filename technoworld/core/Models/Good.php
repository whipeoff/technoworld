<?php
// core/Models/Good.php

namespace Core\Models;

use PDO;

class Good
{
    public int $id;
    public string $add_date;
    public string $ggroup;
    public string $subgroup;
    public string $type;
    public string $brand;
    public string $description;
    public float $price;
    public int $stock;

    public static function findAll(
        PDO $pdo,
        int $limit,
        int $offset,
        array $filters = [],
        string $sort = 'add_date DESC'
    ): array {
        $where = [];
        $params = [];

        if (!empty($filters['ggroup'])) {
            $where[] = 'ggroup = :ggroup';
            $params['ggroup'] = $filters['ggroup'];
        }
        if (!empty($filters['subgroup'])) {
            $where[] = 'subgroup = :subgroup';
            $params['subgroup'] = $filters['subgroup'];
        }
        if (!empty($filters['type'])) {
            $where[] = 'type = :type';
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['brand'])) {
            $where[] = 'brand = :brand';
            $params['brand'] = $filters['brand'];
        }

        $sql = 'SELECT * FROM goods';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $countSql = "SELECT COUNT(*) FROM goods" . ($where ? " WHERE " . implode(' AND ', $where) : '');

        // total count
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // data page
        $sql .= " ORDER BY $sort LIMIT :lim OFFSET :off";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":$k", $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $goods = [];
        foreach ($rows as $r) {
            $g = new self();
            foreach ($r as $k => $v) {
                $g->$k = $v;
            }
            $goods[] = $g;
        }

        return ['data' => $goods, 'total' => $total];
    }

    public static function findById(PDO $pdo, int $id): ?self
    {
        $stmt = $pdo->prepare('SELECT * FROM goods WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $r = $stmt->fetch();
        if (!$r) {
            return null;
        }
        $g = new self();
        foreach ($r as $k => $v) {
            $g->$k = $v;
        }
        return $g;
    }
}
