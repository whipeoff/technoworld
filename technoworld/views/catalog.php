<?php
// views/catalog.php
?>
<h1 class="mb-4">Каталог товаров</h1>

<form id="filterForm" class="row g-3 mb-4">
  <?php foreach (['ggroup'=>'Группа','subgroup'=>'Подгруппа','type'=>'Тип','brand'=>'Бренд'] as $field=>$label): ?>
    <div class="col-sm-6 col-md-3">
      <label class="form-label" for="filter<?= ucfirst($field) ?>"><?= $label ?></label>
      <select id="filter<?= ucfirst($field) ?>" name="<?= $field ?>" class="form-select">
        <option value="">Все</option>
        <?php foreach ($options[$field] as $opt): ?>
          <option value="<?= htmlspecialchars($opt,ENT_QUOTES) ?>"
            <?= $filters[$field] === $opt ? 'selected' : '' ?>>
            <?= htmlspecialchars($opt) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endforeach; ?>

  <div class="col-sm-6 col-md-3">
    <label for="sortSelect" class="form-label">Сортировка</label>
    <select id="sortSelect" name="sort" class="form-select">
      <?php
        $sortOptions = [
          'add_date DESC' => 'Сначала новые',
          'add_date ASC'  => 'Сначала старые',
          'price ASC'     => 'Цена ↑',
          'price DESC'    => 'Цена ↓',
        ];
        foreach ($sortOptions as $val=>$lbl):
      ?>
        <option value="<?= $val ?>" <?= $sort === $val ? 'selected' : '' ?>>
          <?= $lbl ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-sm-6 col-md-3 align-self-end">
    <button type="submit" class="btn btn-primary w-100">Применить</button>
  </div>
</form>

<div id="catalogResults">
  <?php require __DIR__ . '/partials/catalogResults.php'; ?>
</div>

<?php if ($pages > 1): ?>
<nav aria-label="Пагинация" class="mt-4">
  <ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $pages; $i++): 
      $query = array_merge($filters, ['sort'=>$sort,'page'=>$i]);
    ?>
      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
        <a class="page-link" href="/catalog?<?= http_build_query($query) ?>">
          <?= $i ?>
        </a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

<script>
document.getElementById('filterForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const params = new URLSearchParams(new FormData(this));
  params.set('page', 1);
  fetch('/catalog/filter?' + params.toString(), {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(r => r.text())
    .then(html => {
      document.getElementById('catalogResults').innerHTML = html;
      history.replaceState(null, '', '/catalog?' + params.toString());
    });
});
</script>
