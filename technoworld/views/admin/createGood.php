<?php
// views/admin/createGood.php
?>
<h1 class="mb-4">Добавить новый товар</h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" action="/admin/goods/create" enctype="multipart/form-data">
  <?= $csrf ?>

  <div class="mb-3">
    <label for="ggroup" class="form-label">Группа товара</label>
    <input
      type="text"
      id="ggroup"
      name="ggroup"
      class="form-control"
      value="<?= htmlspecialchars($old['ggroup'] ?? '', ENT_QUOTES) ?>"
      required
    >
  </div>

  <div class="mb-3">
    <label for="subgroup" class="form-label">Подгруппа товара</label>
    <input
      type="text"
      id="subgroup"
      name="subgroup"
      class="form-control"
      value="<?= htmlspecialchars($old['subgroup'] ?? '', ENT_QUOTES) ?>"
      required
    >
  </div>

  <div class="mb-3">
    <label for="type" class="form-label">Тип товара</label>
    <input
      type="text"
      id="type"
      name="type"
      class="form-control"
      value="<?= htmlspecialchars($old['type'] ?? '', ENT_QUOTES) ?>"
      required
    >
  </div>

  <div class="mb-3">
    <label for="brand" class="form-label">Бренд/производитель</label>
    <input
      type="text"
      id="brand"
      name="brand"
      class="form-control"
      value="<?= htmlspecialchars($old['brand'] ?? '', ENT_QUOTES) ?>"
      required
    >
  </div>

  <div class="mb-3">
    <label for="description" class="form-label">Описание</label>
    <textarea
      id="description"
      name="description"
      class="form-control"
      rows="4"
      required
    ><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES) ?></textarea>
  </div>

  <div class="mb-3">
    <label for="price" class="form-label">Цена (₽)</label>
    <input
      type="number"
      step="0.01"
      id="price"
      name="price"
      class="form-control"
      value="<?= htmlspecialchars($old['price'] ?? '', ENT_QUOTES) ?>"
      required
    >
  </div>

  <div class="mb-3">
    <label for="stock" class="form-label">Наличие (шт.)</label>
    <input
      type="number"
      id="stock"
      name="stock"
      class="form-control"
      min="0"
      value="<?= htmlspecialchars($old['stock'] ?? '', ENT_QUOTES) ?>"
      required
    >
  </div>

  <div class="mb-3">
    <label for="images" class="form-label">Картинки товара</label>
    <input
      type="file"
      id="images"
      name="images[]"
      class="form-control"
      accept=".jpg,.jpeg,.png,.gif"
      multiple
    >
    <div class="form-text">Можно загрузить несколько файлов. Первый станет главным.</div>
  </div>

  <button type="submit" class="btn btn-success">Создать товар</button>
</form>
