<?php
$title           = 'Ошибка 501 — Раздел в разработке';
$metaDescription = 'Данный раздел временно недоступен. Мы уже работаем над его запуском.';
$metaRobots      = 'noindex, nofollow';
$canonicalUrl    = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8');
?>

<h1 class="text-danger">Раздел в разработке</h1>
<p>Мы активно работаем над этим разделом. Пожалуйста, зайдите позже.</p>
