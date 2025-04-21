<?php
// views/layouts/auth.php
// SEO‑данные должны быть заданы в контроллере до render():
// $title, $metaDescription, $metaRobots, $canonicalUrl
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <?php if (!empty($metaDescription)): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
  <?php endif; ?>
  <meta name="robots"      content="<?= htmlspecialchars($metaRobots) ?>">
  <?php if (!empty($canonicalUrl)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
  <?php endif; ?>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

  <?php require __DIR__ . '/../partials/header.php'; ?>

  <main class="flex-fill d-flex align-items-center justify-content-center py-5">
    <div class="card shadow-sm p-4" style="max-width:420px; width:100%;">
      <?= $content ?>
    </div>
  </main>

  <?php require __DIR__ . '/../partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
