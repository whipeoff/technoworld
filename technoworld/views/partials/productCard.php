<?php
// views/partials/productCard.php
$slug = slugify($good->type . ' ' . $good->brand);
?>
<div class="col-md-4 mb-4">
  <div class="card h-100">
    <div class="card-body d-flex flex-column">
      <h5 class="card-title"><?= htmlspecialchars($good->type) ?></h5>
      <p class="card-text text-muted"><?= htmlspecialchars($good->brand) ?></p>
      <p class="mt-auto fw-bold"><?= number_format($good->price,2,',',' ') ?> ₽</p>
      <a href="/catalog/<?= $slug ?>-<?= $good->id ?>" class="btn btn-primary mt-2">Подробнее</a>
    </div>
  </div>
</div>
