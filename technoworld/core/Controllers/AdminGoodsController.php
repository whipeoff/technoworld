<?php
// core/Controllers/AdminGoodsController.php

namespace Core\Controllers;

use Core\Models\GoodImage;
use Core\Security\AuthManager;
use function sanitizeInput;
use function slugify;

class AdminGoodsController extends BaseController
{
    public function create(): void
    {
        AuthManager::requireRole('admin', $this->pdo);

        $this->render(
            'admin/createGood.php',
            [
                'csrf'   => $this->csrf->getInputHTML(),
                'errors' => [],
                'old'    => []
            ],
            'base'
        );
    }

    public function store(): void
    {
        AuthManager::requireRole('admin', $this->pdo);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            abort(405, "Метод не поддерживается");
        }

        $errors = [];
        $data   = [];

        foreach (['ggroup','subgroup','type','brand','description'] as $f) {
            $v = sanitizeInput($_POST[$f] ?? '');
            if ($v === '') {
                $errors[] = "Поле «{$f}» обязательно.";
            }
            $data[$f] = $v;
        }

        $price = $_POST['price'] ?? '';
        if (!is_numeric($price) || $price < 0) {
            $errors[] = "Неверное значение цены.";
        }
        $data['price'] = $price;

        $stock = $_POST['stock'] ?? '';
        if (!ctype_digit((string)$stock)) {
            $errors[] = "Неверное значение наличия.";
        }
        $data['stock'] = $stock;

        $images = $_FILES['images'] ?? null;

        if ($errors) {
            $this->render('admin/createGood.php', [
                'csrf'   => $this->csrf->getInputHTML(),
                'errors' => $errors,
                'old'    => $data
            ], 'base');
            return;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO goods
               (ggroup, subgroup, type, brand, description, price, stock)
             VALUES
               (:ggroup, :subgroup, :type, :brand, :description, :price, :stock)"
        );
        $stmt->execute([
            'ggroup'      => $data['ggroup'],
            'subgroup'    => $data['subgroup'],
            'type'        => $data['type'],
            'brand'       => $data['brand'],
            'description' => $data['description'],
            'price'       => $data['price'],
            'stock'       => $data['stock'],
        ]);

        $newId = (int)$this->pdo->lastInsertId();
        $slug  = slugify($data['type'] . ' ' . $data['brand']);

        if ($images && is_array($images['tmp_name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/goods/' . $newId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $first = true;
            for ($i = 0; $i < count($images['tmp_name']); $i++) {
                if ($images['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }
                $ext = strtolower(pathinfo($images['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif'], true)) {
                    continue;
                }
                $filename = uniqid() . '.' . $ext;
                $dest = "{$uploadDir}/{$filename}";
                if (move_uploaded_file($images['tmp_name'][$i], $dest)) {
                    GoodImage::add($this->pdo, $newId, $filename, $first);
                    $first = false;
                }
            }
        }

        header("Location: /catalog/{$slug}-{$newId}", true, 303);
        exit;
    }
}
