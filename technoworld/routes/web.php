<?php
// routes/web.php

$uri    = validateUri($_SERVER['REQUEST_URI'] ?? '/');
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/login' && $method === 'GET') {
    (new \Core\Controllers\AuthController())->showLoginForm();
}
elseif ($uri === '/login' && $method === 'POST') {
    (new \Core\Controllers\AuthController())->handleLoginForm();
}

elseif ($uri === '/admin' && $method === 'GET') {
    (new \Core\Controllers\AdminDashboardController())->index();
}

elseif ($uri === '/admin/goods/create' && $method === 'GET') {
    (new \Core\Controllers\AdminGoodsController())->create();
}
elseif ($uri === '/admin/goods/create' && $method === 'POST') {
    (new \Core\Controllers\AdminGoodsController())->store();
}

elseif ($uri === '/admin/requests' && $method === 'GET') {
    (new \Core\Controllers\AdminRequestsController())->index();
}

elseif ($uri === '/admin/sitemap/generate' && $method === 'GET') {
    (new \Core\Controllers\SitemapController())->generate();
}

elseif ($uri === '/request' && $method === 'GET') {
    (new \Core\Controllers\RequestController())->showForm();
}
elseif ($uri === '/request' && $method === 'POST') {
    (new \Core\Controllers\RequestController())->submit();
}
elseif ($uri === '/request/success' && $method === 'GET') {
    (new \Core\Controllers\RequestController())->success();
}

elseif ($uri === '/catalog' && $method === 'GET') {
    (new \Core\Controllers\CatalogController())->index();
}
elseif ($uri === '/catalog/filter' && $method === 'GET') {
    (new \Core\Controllers\CatalogController())->filter();
}
elseif (preg_match('#^/catalog/([\w\-]+)-(\d+)$#', $uri, $m) && $method === 'GET') {
    $slug = $m[1];
    $id   = (int) $m[2];
    (new \Core\Controllers\CatalogController())->show($slug, $id);
}

elseif (($uri === '/' || $uri === '') && $method === 'GET') {
    (new \Core\Controllers\InfoPagesController())->home();
}
elseif ($uri === '/about' && $method === 'GET') {
    (new \Core\Controllers\InfoPagesController())->about();
}
elseif ($uri === '/how-to-buy' && $method === 'GET') {
    (new \Core\Controllers\InfoPagesController())->howToBuy();
}
elseif ($uri === '/news' && $method === 'GET') {
    (new \Core\Controllers\InfoPagesController())->news();
}
elseif ($uri === '/faq' && $method === 'GET') {
    (new \Core\Controllers\InfoPagesController())->faq();
}

elseif ($uri === '/logout' && $method === 'GET') {
    setcookie('jwt', '', time() - 3600, '/');
    setcookie('remember_token', '', time() - 3600, '/');
    header('Location: /login');
    exit;
}

//Думал что яндекс не может добраться из-за кривого роутинга
elseif (preg_match('#^/yandex_([a-z0-9]+)\.html$#', $uri, $m)) {
    header('Content-Type: text/html; charset=UTF-8');
    echo "yandex_{$m[1]}.html";
    exit;
}

elseif (preg_match('#^/error/([1-5][0-9]{2})$#', $uri, $e) && $method === 'GET') {
    renderError((int)$e[1]);
}

else {
    WTL("ROUTING: Не найден маршрут для {$uri} [{$method}]");
    abort(404, "Маршрут не найден.");
}
