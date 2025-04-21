<?php
// core/Controllers/CatalogController.php

namespace Core\Controllers;

use Core\Models\Good;
use Core\Models\GoodImage;
use Core\Security\AuthManager;
use Core\Security\JWTManager;
use function slugify;
use PDO;

class CatalogController extends BaseController
{
    private int $perPage = 12;

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'ggroup'    => $_GET['ggroup']   ?? '',
            'subgroup' => $_GET['subgroup'] ?? '',
            'type'      => $_GET['type']     ?? '',
            'brand'     => $_GET['brand']    ?? '',
        ];
        $sort = $_GET['sort'] ?? 'add_date DESC';

        $groups    = $this->pdo->query("SELECT DISTINCT ggroup FROM goods")->fetchAll(PDO::FETCH_COLUMN);
        $subgroups = $this->pdo->query("SELECT DISTINCT subgroup FROM goods")->fetchAll(PDO::FETCH_COLUMN);
        $types     = $this->pdo->query("SELECT DISTINCT type FROM goods")->fetchAll(PDO::FETCH_COLUMN);
        $brands    = $this->pdo->query("SELECT DISTINCT brand FROM goods")->fetchAll(PDO::FETCH_COLUMN);

        $offset = ($page - 1) * $this->perPage;
        $result = Good::findAll($this->pdo, $this->perPage, $offset, $filters, $sort);
        $goods  = $result['data'];
        $total  = $result['total'];
        $pages  = (int)ceil($total / $this->perPage);

        $previews = [];
        foreach ($goods as $good) {
            $previews[$good->id] = GoodImage::findMainByGoodId($this->pdo, $good->id);
        }

        $this->render('catalog.php', [
            'goods'      => $goods,
            'previews'   => $previews,
            'page'       => $page,
            'pages'      => $pages,
            'filters'    => $filters,
            'sort'       => $sort,
            'options'    => [
                'ggroup'   => $groups,
                'subgroup'=> $subgroups,
                'type'     => $types,
                'brand'    => $brands,
            ],
            'title'           => 'Каталог товаров',
            'metaDescription' => 'Полный каталог товаров Technoworld',
            'metaRobots'      => 'index, follow',
            'canonicalUrl'    => 'http://techno-world.free.nf/catalog',
        ], 'base');
    }

    public function filter(): void
    {
        header('Content-Type: text/html; charset=UTF-8');

        $filters = [
            'ggroup'    => $_GET['ggroup']   ?? '',
            'subgroup' => $_GET['subgroup'] ?? '',
            'type'      => $_GET['type']     ?? '',
            'brand'     => $_GET['brand']    ?? '',
        ];
        $sort = $_GET['sort'] ?? 'add_date DESC';

        $result = Good::findAll($this->pdo, $this->perPage, 0, $filters, $sort);
        $goods  = $result['data'];

        $previews = [];
        foreach ($goods as $good) {
            $previews[$good->id] = GoodImage::findMainByGoodId($this->pdo, $good->id);
        }

        require __DIR__ . '/../../views/partials/catalogResults.php';
        exit;
    }

    public function show(string $slug, int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->uploadImage($id);
            return;
        }

        $good = Good::findById($this->pdo, $id);
        if (!$good) {
            abort(404, "Товар #{$id} не найден.");
        }

        $expected = slugify("{$good->type} {$good->brand}");
        if ($slug !== $expected) {
            header("Location: /catalog/{$expected}-{$id}", true, 301);
            exit;
        }

        $isAdmin = false;
        $token   = AuthManager::extractToken();
        if ($token) {
            $payload = (new JWTManager($_ENV['JWT_SECRET'] ?? 'dev-secret'))->validate($token);
            if ($payload && ($payload['role'] ?? '') === 'admin') {
                $isAdmin = true;
            }
        }

        $images = GoodImage::findByGoodId($this->pdo, $id);
        $main   = $images[0] ?? null;
        $thumbs = array_slice($images, 1);
        $csrfField = $this->csrf->getInputHTML();

        $this->render('product.php', [
            'good'            => $good,
            'isAdmin'         => $isAdmin,
            'main'            => $main,
            'thumbs'          => $thumbs,
            'csrfField'       => $csrfField,
            'title'           => "{$good->type} {$good->brand}",
            'metaDescription' => mb_substr($good->description, 0, 150),
            'metaRobots'      => 'index, follow',
            'canonicalUrl'    => "http://techno-world.free.nf/catalog/{$slug}-{$id}",
        ], 'base');
    }

    private function uploadImage(int $id): void
    {
        AuthManager::requireRole('admin', $this->pdo);

        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            abort(400, "Не удалось загрузить файл.");
        }

        $file = $_FILES['image'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif'], true)) {
            abort(400, "Неподдерживаемый формат изображения.");
        }

        $uploadDir = __DIR__ . '/../../public/uploads/goods/' . $id;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid() . '.' . $ext;
        $dest     = "{$uploadDir}/{$filename}";
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            abort(500, "Не удалось сохранить файл.");
        }

        $existing = GoodImage::findByGoodId($this->pdo, $id);
        $isMain   = empty($existing);

        GoodImage::add($this->pdo, $id, $filename, $isMain);

        $good = Good::findById($this->pdo, $id);
        $slug = slugify("{$good->type} {$good->brand}");
        header("Location: /catalog/{$slug}-{$id}", true, 303);
        exit;
    }
}
