<?php
/**
 * views/product.php
 *
 * Шаблон страницы товара (public view).
 *
 * Контроллер передаёт:
 *   $good    — Core\Models\Good
 *   $main    — Core\Models\GoodImage|null
 *   $thumbs  — array<Core\Models\GoodImage>
 *   $title, $metaDescription, $metaRobots, $canonicalUrl
 */
?>
<div class="container py-4">
  <div class="row">
    <!-- ЛЕВАЯ КОЛОНКА: изображения -->
    <div class="col-md-6 mb-4">
      <?php if ($main): ?>
        <img
          id="mainImage"
          src="/public/uploads/goods/<?= $good->id ?>/<?= htmlspecialchars($main->filename, ENT_QUOTES) ?>"
          class="img-fluid mb-3"
          alt="<?= htmlspecialchars($good->type, ENT_QUOTES) ?>"
        >
      <?php endif; ?>

      <?php if (!empty($thumbs)): ?>
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($thumbs as $img): ?>
            <img
              src="/public/uploads/goods/<?= $good->id ?>/<?= htmlspecialchars($img->filename, ENT_QUOTES) ?>"
              class="img-thumbnail thumb-img"
              style="width:80px; height:80px; object-fit:cover; cursor:pointer;"
              alt=""
            >
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- ПРАВАЯ КОЛОНКА: данные товара -->
    <div class="col-md-6">
      <h1><?= htmlspecialchars($good->type, ENT_QUOTES) ?>
         <?= htmlspecialchars($good->brand, ENT_QUOTES) ?></h1>
      <p class="fw-bold fs-4">
        <?= number_format($good->price, 2, ',', ' ') ?> ₽
      </p>
      <p>В наличии: 
        <?= $good->stock > 0 
             ? $good->stock 
             : '<em>под заказ</em>' 
        ?>
      </p>
      <p><?= nl2br(htmlspecialchars($good->description, ENT_QUOTES)) ?></p>
      <a href="/request" class="btn btn-success mt-3 mb-4">
        Оставить заявку
      </a>
    </div>
  </div>
</div>

<script>
// При клике на мини‑картинку меняем её местами с главной
document.querySelectorAll('.thumb-img').forEach(function(el){
  el.addEventListener('click', function(){
    var main = document.getElementById('mainImage');
    var tmp  = main.src;
    main.src = this.src;
    this.src = tmp;
  });
});
</script>
