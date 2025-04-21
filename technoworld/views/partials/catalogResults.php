<?php
// views/partials/catalogResults.php
?>
<div class="row g-4">
  <?php foreach ($goods as $good):
    $img = $previews[$good->id] ?? null;
  ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 shadow-sm">
        <?php if ($img): ?>
          <img
            src="/public/uploads/goods/<?= $good->id ?>/<?= htmlspecialchars($img->filename,ENT_QUOTES) ?>"
            class="card-img-top"
            style="object-fit:cover; height:200px;"
            alt="<?= htmlspecialchars($good->type . ' ' . $good->brand,ENT_QUOTES) ?>"
          >
        <?php else: ?>
          <div class="bg-light d-flex align-items-center justify-content-center text-muted"
               style="height:200px;">
            Нет фото
          </div>
        <?php endif; ?>
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">
            <?= htmlspecialchars($good->type . ' ' . $good->brand,ENT_QUOTES) ?>
          </h5>
          <p class="card-text fw-bold mb-3">
            <?= number_format($good->price,2,',',' ') ?> ₽
          </p>
          <a href="/catalog/<?= \slugify($good->type . ' ' . $good->brand) ?>-<?= $good->id ?>"
             class="btn btn-sm btn-primary mt-auto">
            Подробнее
          </a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
