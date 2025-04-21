<?php
// views/loginForm.php

// Здесь только фрагмент — никаких DOCTYPE, HEAD, BODY или require/layout!
?>
<h3 class="text-center mb-4">Вход в систему</h3>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger">
    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<form method="POST" action="/login" novalidate>
  <?= $csrf ?? '' ?>
  <div class="mb-3">
    <label for="username" class="form-label">Логин</label>
    <input
      type="text" id="username" name="username"
      class="form-control"
      required
      value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Пароль</label>
    <input
      type="password" id="password" name="password"
      class="form-control"
      required
    >
  </div>
  <div class="form-check mb-3">
    <input
      class="form-check-input" type="checkbox"
      id="remember" name="remember"
      <?= isset($_POST['remember']) ? 'checked' : '' ?>
    >
    <label class="form-check-label" for="remember">Запомнить меня</label>
  </div>
  <button type="submit" class="btn btn-primary w-100">Войти</button>
</form>
