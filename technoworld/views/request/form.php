<?php
// views/request/form.php
?>
<h1 class="mb-4">Оставить заявку</h1>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form method="POST" action="/request" novalidate>
  <?= $csrf ?? '' ?>

  <div class="mb-3">
    <label for="name" class="form-label">Имя</label>
    <input
      type="text" id="name" name="name"
      class="form-control"
      required maxlength="100"
      value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
  </div>

  <div class="mb-3">
    <label for="phone" class="form-label">Телефон</label>
    <input
      type="text" id="phone" name="phone"
      class="form-control"
      required maxlength="20"
      placeholder="+7 999 123-45-67"
      value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
    <div class="form-text">Введите 11 цифр, например: +7 999 123-45-67</div>
  </div>

  <div class="mb-3">
    <label for="comment" class="form-label">Комментарий</label>
    <textarea
      id="comment" name="comment"
      class="form-control" rows="4" maxlength="1000"
    ><?= htmlspecialchars($old['comment'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
  </div>

  <button type="submit" class="btn btn-primary w-100">Отправить заявку</button>
</form>
