<?php
if (!defined('ABSPATH')) exit;

function mk_hero($h1, $sub, $btns = '', $sm = false) {
    return '<div class="mk-hero' . ($sm ? ' sm' : '') . '"><h1>' . $h1 . '</h1><p>' . $sub . '</p>' . $btns . '</div>';
}

function mk_rows($items) {
    $h = '';
    foreach ($items as $i) {
        $h .= '<div class="mk-row"><div>' . $i[0] . '</div><span>' . $i[1] . '</span></div>';
    }
    return $h;
}

function mk_kit_pages() {
    $home = '[mk_slider]'
        . '<div class="mk-sec"><h2>Почему выбирают нас</h2><div class="mk-grid">'
        . '<div class="mk-card"><span class="mk-ico">✅</span><h3>Преимущество 1</h3><p>Опишите первое преимущество вашей компании. Замените этот текст на свой.</p></div>'
        . '<div class="mk-card"><span class="mk-ico">🚚</span><h3>Преимущество 2</h3><p>Опишите второе преимущество. Например, быстрая доставка, гарантия или сервис.</p></div>'
        . '<div class="mk-card"><span class="mk-ico">🤝</span><h3>Преимущество 3</h3><p>Опишите третье преимущество. Поддержка клиентов, опыт или надёжность.</p></div>'
        . '</div></div>'
        . '<div class="mk-alt"><div class="mk-sec"><div class="mk-two"><div><h2>О нас кратко</h2>'
        . '<p>Здесь разместите краткий рассказ о компании: чем занимаетесь, сколько лет на рынке, что важно для клиентов. Отредактируйте этот текст под свою тематику.</p>'
        . '<a class="mk-btn c" href="%%ABOUT%%">Подробнее о нас</a></div>'
        . '<div class="mk-stats"><div class="mk-stat"><b>10+ лет</b>на рынке</div><div class="mk-stat"><b>1000+</b>довольных клиентов</div><div class="mk-stat"><b>24/7</b>поддержка</div></div>'
        . '</div></div></div>'
        . '<div class="mk-sec"><h2>Популярные товары</h2>[products category="category-1" limit="3" columns="3"]<p class="mk-more"><a class="mk-btn c" href="%%SHOP%%">Весь каталог</a></p></div>'
        . '<div class="mk-alt"><div class="mk-sec"><h2>Услуги и товары</h2>[products category="uslugi,category-2" limit="6" columns="3"]</div></div>'
        . '<div class="mk-sec"><h2>Новости и статьи</h2>[mk_blog limit="3" pagination="0"]<p class="mk-more"><a class="mk-btn c" href="%%BLOG%%">Все статьи</a></p></div>'
        . '<div class="mk-cta"><h2>Остались вопросы?</h2><a class="mk-btn c" href="%%CONTACTS%%">Связаться с нами</a></div>';

    $about = mk_hero('О компании', 'Команда специалистов в своей области.', '', true)
        . '<div class="mk-sec"><div class="mk-grid">'
        . '<div class="mk-card"><span class="mk-ico">🏢</span><h3>Кто мы</h3><p>Расскажите о команде, опыте и подходе к работе. Этот блок можно полностью переписать под свою сферу.</p></div>'
        . '<div class="mk-card"><span class="mk-ico">⚙️</span><h3>Как мы работаем</h3><p>Опишите процессы: как принимаете заказы, консультируете, сопровождаете клиента. Замените текст на свой.</p></div>'
        . '<div class="mk-card"><span class="mk-ico">⭐</span><h3>Почему нам доверяют</h3><p>Расскажите о гарантиях, отзывах и принципах. Здесь уместно упомянуть документы, сертификаты или опыт.</p></div>'
        . '</div></div>'
        . '<div class="mk-alt"><div class="mk-sec"><h2>Наши гарантии</h2><ul class="mk-check">'
        . '<li>Гарантия 1 — опишите её здесь</li>'
        . '<li>Гарантия 2 — опишите её здесь</li>'
        . '<li>Гарантия 3 — опишите её здесь</li>'
        . '<li>Гарантия 4 — опишите её здесь</li>'
        . '<li>Гарантия 5 — опишите её здесь</li>'
        . '</ul></div></div>'
        . '<div class="mk-cta"><h2>Готовы начать?</h2><a class="mk-btn c" href="%%SHOP%%">Перейти в каталог</a></div>';

    $contacts = mk_hero('Контакты', 'Свяжитесь с нами удобным способом.', '', true)
        . '<div class="mk-sec">[mk_contacts]'
        . '<p class="mk-note">Позвоните или напишите нам — поможем с выбором и ответим на вопросы.</p>'
        . '<div style="margin-top:30px">[mk_social]</div></div>'
        . '<div class="mk-alt"><div class="mk-sec"><h2>Как нас найти</h2>[mk_map]</div></div>';

    $delivery = mk_hero('Доставка и оплата', 'Удобные способы доставки и оплаты.', '', true)
        . '<div class="mk-sec"><div class="mk-grid">'
        . '<div class="mk-card"><span class="mk-ico">🚚</span><h3>Способы доставки</h3>' . mk_rows(array(
            array('<b>Самовывоз</b><br>из офиса', 'бесплатно'),
            array('<b>Курьер по городу</b><br>1–2 дня', 'от 500 ₽'),
            array('<b>Доставка по России</b><br>2–7 дней', 'от 1 000 ₽'),
            array('<b>Экспресс-доставка</b><br>в день заказа', 'от 1 500 ₽'),
        )) . '</div>'
        . '<div class="mk-card"><span class="mk-ico">💳</span><h3>Способы оплаты</h3>' . mk_rows(array(
            array('Наличными при получении', ''),
            array('Банковской картой на сайте', ''),
            array('Банковский перевод по счёту', ''),
            array('Предоплата 30%, остаток при получении', ''),
        )) . '</div>'
        . '</div></div>';

    $blog = mk_hero('Блог', 'Полезные статьи и новости.', '', true)
        . '<div class="mk-sec">[mk_blog limit="6" pagination="1"]</div>';

    return array(
        'home' => array('title' => 'Главная', 'content' => $home,
            'seo_t' => 'Главная — ' . mk_opt('brand'),
            'seo_d' => 'Официальный сайт. Каталог, услуги, контакты и полезные статьи.'),
        'about' => array('title' => 'О компании', 'content' => $about,
            'seo_t' => 'О компании — ' . mk_opt('brand'),
            'seo_d' => 'Рассказ о компании, команде и принципах работы.'),
        'contacts' => array('title' => 'Контакты', 'content' => $contacts,
            'seo_t' => 'Контакты — ' . mk_opt('brand'),
            'seo_d' => 'Адрес, телефон, карта проезда и режим работы.'),
        'delivery' => array('title' => 'Доставка и оплата', 'content' => $delivery,
            'seo_t' => 'Доставка и оплата — ' . mk_opt('brand'),
            'seo_d' => 'Способы доставки и оплаты. Сроки и стоимость.'),
        'blog' => array('title' => 'Блог', 'content' => $blog,
            'seo_t' => 'Блог — ' . mk_opt('brand'),
            'seo_d' => 'Полезные статьи и новости компании.'),
    );
}

function mk_kit_categories() {
    return array(
        array('slug' => 'category-1', 'name' => 'Категория 1', 'parent' => ''),
        array('slug' => 'category-1-1', 'name' => 'Подкатегория 1.1', 'parent' => 'category-1'),
        array('slug' => 'category-1-2', 'name' => 'Подкатегория 1.2', 'parent' => 'category-1'),
        array('slug' => 'uslugi', 'name' => 'Услуги', 'parent' => ''),
        array('slug' => 'category-2', 'name' => 'Категория 2', 'parent' => ''),
    );
}

function mk_kit_products() {
    return array(
        array('sku' => 'SKU-001', 'slug' => 'tovar-1', 'name' => 'Товар 1', 'price' => 1000, 'stock' => 10, 'single' => false, 'featured' => true,
            'cats' => array('category-1'), 'img' => 'igrunka.png',
            'tags' => array('тег 1', 'тег 2'),
            'short' => 'Короткое описание товара 1. Замените на своё.',
            'desc' => '<p>Полное описание товара 1. Здесь можно рассказать о характеристиках, составе, размерах и особенностях.</p>'),
        array('sku' => 'SKU-002', 'slug' => 'tovar-2', 'name' => 'Товар 2', 'price' => 2500, 'stock' => 5, 'single' => false, 'featured' => true,
            'cats' => array('category-1'), 'img' => 'saymiri.png',
            'tags' => array('тег 1', 'тег 3'),
            'short' => 'Короткое описание товара 2. Замените на своё.',
            'desc' => '<p>Полное описание товара 2. Опишите преимущества, материал, гарантию.</p>'),
        array('sku' => 'SKU-003', 'slug' => 'tovar-3', 'name' => 'Товар 3', 'price' => 5000, 'stock' => 3, 'single' => false, 'featured' => true,
            'cats' => array('category-1-1'), 'img' => 'kapucin.png',
            'tags' => array('тег 2', 'тег 4'),
            'short' => 'Короткое описание товара 3. Замените на своё.',
            'desc' => '<p>Полное описание товара 3.</p>'),
        array('sku' => 'SRV-001', 'slug' => 'usluga-1', 'name' => 'Услуга 1', 'price' => 3000, 'stock' => 0, 'single' => true, 'featured' => false,
            'cats' => array('uslugi'), 'img' => 'consult.png',
            'tags' => array('услуга'),
            'short' => 'Короткое описание услуги 1.',
            'desc' => '<p>Описание услуги 1. Опишите, что входит, сроки и условия.</p>'),
        array('sku' => 'SRV-002', 'slug' => 'usluga-2', 'name' => 'Услуга 2', 'price' => 8000, 'stock' => 0, 'single' => true, 'featured' => false,
            'cats' => array('uslugi'), 'img' => 'volier.png',
            'tags' => array('услуга'),
            'short' => 'Короткое описание услуги 2.',
            'desc' => '<p>Описание услуги 2.</p>'),
        array('sku' => 'SRV-003', 'slug' => 'usluga-3', 'name' => 'Услуга 3', 'price' => 15000, 'stock' => 0, 'single' => true, 'featured' => false,
            'cats' => array('uslugi'), 'img' => 'dostavka.png',
            'tags' => array('услуга'),
            'short' => 'Короткое описание услуги 3.',
            'desc' => '<p>Описание услуги 3.</p>'),
        array('sku' => 'ACC-001', 'slug' => 'tovar-4', 'name' => 'Товар 4', 'price' => 2900, 'stock' => 30, 'single' => false, 'featured' => false,
            'cats' => array('category-2'), 'img' => 'korm.png',
            'tags' => array('тег 2'),
            'short' => 'Короткое описание товара 4.',
            'desc' => '<p>Полное описание товара 4.</p>'),
        array('sku' => 'ACC-002', 'slug' => 'tovar-5', 'name' => 'Товар 5', 'price' => 3500, 'stock' => 15, 'single' => false, 'featured' => false,
            'cats' => array('category-2'), 'img' => 'igrushki.png',
            'tags' => array('тег 3'),
            'short' => 'Короткое описание товара 5.',
            'desc' => '<p>Полное описание товара 5.</p>'),
        array('sku' => 'ACC-003', 'slug' => 'tovar-6', 'name' => 'Товар 6', 'price' => 6900, 'stock' => 10, 'single' => false, 'featured' => false,
            'cats' => array('category-2'), 'img' => 'perenoska.png',
            'tags' => array('тег 4'),
            'short' => 'Короткое описание товара 6.',
            'desc' => '<p>Полное описание товара 6.</p>'),
    );
}

function mk_kit_posts() {
    return array(
        array('slug' => 'pervaya-statya', 'title' => 'Первая статья блога', 'cat' => 'news',
            'excerpt' => 'Короткое описание первой статьи. Замените на своё.',
            'body' => '<p>Текст первой статьи. Отредактируйте под свою тему.</p>'),
        array('slug' => 'vtoraya-statya', 'title' => 'Вторая статья блога', 'cat' => 'care',
            'excerpt' => 'Короткое описание второй статьи.',
            'body' => '<p>Текст второй статьи.</p>'),
        array('slug' => 'tretya-statya', 'title' => 'Третья статья блога', 'cat' => 'docs',
            'excerpt' => 'Короткое описание третьей статьи.',
            'body' => '<p>Текст третьей статьи.</p>'),
        array('slug' => 'chetvertaya-statya', 'title' => 'Четвёртая статья блога', 'cat' => 'care',
            'excerpt' => 'Короткое описание четвёртой статьи.',
            'body' => '<p>Текст четвёртой статьи.</p>'),
    );
}