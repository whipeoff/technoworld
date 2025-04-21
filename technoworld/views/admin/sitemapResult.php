<?php
// views/admin/sitemapResult.php
?>
<h1 class="mb-4"><?= $success ? 'Sitemap обновлён' : 'Ошибка генерации' ?></h1>

<?php if ($success): ?>
  <div class="alert alert-success">
    Файл <code>sitemap.xml</code> успешно сохранён в корне сайта.
  </div>
  <a href="/sitemap.xml" target="_blank" class="btn btn-outline-primary">Открыть sitemap</a>
<?php else: ?>
  <div class="alert alert-danger">
    Не удалось записать <code>sitemap.xml</code>. Проверьте права на запись.
  </div>
<?php endif; ?>

<a href="/admin" class="btn btn-link mt-3">← Вернуться в панель</a>
