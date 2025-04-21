<?php
// views/layouts/base.php

$title           = $title           ?? 'Technoworld';
$metaDescription = $metaDescription ?? '';
$metaRobots      = $metaRobots      ?? 'index, follow';
$canonicalUrl    = $canonicalUrl    ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="yandex-verification" content="ef8cecf4f92b4583" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if ($metaDescription): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
  <meta name="robots" content="<?= htmlspecialchars($metaRobots, ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($canonicalUrl): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

  <!-- общий header -->
  <?php require __DIR__ . '/../partials/header.php'; ?>

  <!-- контент страницы -->
  <main class="flex-fill">
    <div class="container py-5">
      <?= $content ?>
    </div>
  </main>

  <!-- общий footer -->
  <?php require __DIR__ . '/../partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
