<?php
// views/layouts/error.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <?php if (!empty($metaDescription)): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
  <meta name="robots" content="<?= htmlspecialchars($metaRobots, ENT_QUOTES, 'UTF-8') ?>">
  <?php if (!empty($canonicalUrl)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>

  <!-- meta refresh -->
  <?= $metaRefresh ?? '' ?>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

  <?php require __DIR__ . '/../partials/header.php'; ?>

  <main class="flex-fill d-flex align-items-center justify-content-center text-center py-5">
    <div class="container">
      <?= $content ?>

      <!-- JS‑редирект для браузеров без meta -->
      <?php if (!empty($metaRefresh)): ?>
        <script>
          setTimeout(function() {
            window.location.href = <?= json_encode($_SERVER['HTTP_REFERER'] ?? ($_SERVER['REQUEST_URI'] ?? '/')) ?>;
          }, 3000);
        </script>
      <?php endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/../partials/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
