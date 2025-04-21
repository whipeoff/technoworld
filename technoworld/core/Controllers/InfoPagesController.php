<?php
// core/Controllers/InfoPagesController.php

namespace Core\Controllers;

class InfoPagesController extends BaseController
{
    public function home(): void {
        $this->render('pages/home.php', [
            'title'           => 'TechnoWorld — Главная',
            'metaDescription' => 'Добро пожаловать на сайт TechnoWorld.',
            'canonicalUrl'    => 'http://techno-world.free.nf/',
        ], 'base');
    }

    public function about(): void {
        $this->render('pages/about.php', [
            'title'           => 'О компании',
            'metaDescription' => 'Информация о компании TechnoWorld.',
            'canonicalUrl'    => 'http://techno-world.free.nf/about',
        ], 'base');
    }

    public function howToBuy(): void {
        $this->render('pages/how-to-buy.php', [
            'title'           => 'Как купить',
            'metaDescription' => 'Как оформить заказ и получить товар.',
            'canonicalUrl'    => 'http://techno-world.free.nf/how-to-buy',
        ], 'base');
    }

    public function news(): void {
        abort(501, "Раздел новостей в разработке.");
    }

    public function faq(): void {
        abort(501, "Раздел FAQ в разработке.");
    }
}
