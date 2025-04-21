<?php
// views/partials/footer.php
?>
<footer class="bg-dark text-light py-5 mt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-3">
        <h5>Адрес склада</h5>
        <address class="small">
          г. Москва, ул. Промышленная, д. 1<br>
          Тел.: +7 495 123‑45‑67<br>
          Email: <a href="mailto:info@technoworld.ru" class="text-light">info@technoworld.ru</a>
        </address>
      </div>
      <div class="col-md-4 mb-3">
        <h5>Навигация</h5>
        <ul class="list-unstyled small">
          <li><a class="text-light" href="/catalog">Каталог</a></li>
          <li><a class="text-light" href="/how-to-buy">Как купить</a></li>
          <li><a class="text-light" href="/news">Промо и новости</a></li>
          <li><a class="text-light" href="/faq">FAQ</a></li>
        </ul>
      </div>
      <div class="col-md-4 text-md-end">
        <button id="to-top" class="btn btn-outline-light mb-2">Наверх ↑</button><br>
        <a href="/request" class="btn btn-primary">Оставить заявку</a>
      </div>
    </div>
    <div class="text-center small mt-3">&copy; <?= date('Y') ?> TechnoWorld. Все права защищены.</div>
  </div>
  <script>
    document.getElementById('to-top')?.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>
</footer>
