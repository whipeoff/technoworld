<?php
// views/admin/requests.php

$title           = 'Заявки от клиентов';
$metaDescription = 'Список всех заявок, отправленных с сайта.';
$metaRobots      = 'noindex, nofollow';
$canonicalUrl    = 'http://techno-world.free.nf/admin/requests';
?>

<h1 class="mb-4">Заявки от клиентов</h1>

<?php if (empty($requests)): ?>
  <div class="alert alert-info">Заявок пока нет.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Имя</th>
          <th>Телефон</th>
          <th>Сообщение</th>
          <th>Дата</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $r): ?>
          <tr>
            <td><?= (int) $r['id'] ?></td>
            <td><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['phone'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['comment'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
