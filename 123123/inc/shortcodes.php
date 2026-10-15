<?php
if (!defined('ABSPATH')) exit;

// [mk_slider]
add_shortcode('mk_slider', function () {
    $slides = array(
        array('mk-s1', 'Заголовок слайда 1', 'Краткое описание предложения или акции. Замените текст на свой.', 'Кнопка 1', mk_shop_url(), 'Кнопка 2', mk_page_url('contacts')),
        array('mk-s2', 'Заголовок слайда 2', 'Расскажите о ключевом преимуществе или категории товаров.', 'В каталог', mk_shop_url(), 'Читать блог', mk_page_url('blog')),
        array('mk-s3', 'Заголовок слайда 3', 'Опишите сервис, гарантию или дополнительную услугу.', 'О нас', mk_page_url('about'), 'Доставка', mk_page_url('delivery')),
    );
    $h = '<div class="mk-slider" aria-roledescription="carousel">';
    foreach ($slides as $i => $s) {
        $h .= '<div class="mk-slide ' . esc_attr($s[0]) . ($i === 0 ? ' is-active' : '') . '"><div>'
            . '<' . ($i === 0 ? 'h1' : 'h2') . '>' . esc_html($s[1]) . '</' . ($i === 0 ? 'h1' : 'h2') . '><p>' . esc_html($s[2]) . '</p>'
            . '<a class="mk-btn a" href="' . esc_url($s[4]) . '">' . esc_html($s[3]) . '</a>'
            . '<a class="mk-btn b" href="' . esc_url($s[6]) . '">' . esc_html($s[5]) . '</a>'
            . '</div></div>';
    }
    $h .= '<button class="mk-arrow mk-prev" type="button" aria-label="Назад">‹</button><button class="mk-arrow mk-next" type="button" aria-label="Вперёд">›</button><div class="mk-dots">';
    foreach ($slides as $i => $s) {
        $h .= '<button type="button" aria-label="Слайд ' . ($i + 1) . '"' . ($i === 0 ? ' class="is-active"' : '') . '></button>';
    }
    return $h . '</div></div>';
});

// [mk_blog limit="6" pagination="1"]
add_shortcode('mk_blog', function ($atts) {
    $a = shortcode_atts(array('limit' => 6, 'pagination' => 1), $atts);
    $limit = max(1, (int) $a['limit']);
    $paged = 1;
    if ((int) $a['pagination']) {
        $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    }
    $q = new WP_Query(array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => $limit,
        'paged'               => $paged,
        'ignore_sticky_posts' => true,
    ));
    ob_start();
    if ($q->have_posts()) {
        echo '<div class="mk-posts">';
        while ($q->have_posts()) {
            $q->the_post();
            mk_post_card();
        }
        echo '</div>';
        if ((int) $a['pagination']) {
            $pid = get_queried_object_id();
            $link = $pid ? get_permalink($pid) : home_url('/');
            if (get_option('permalink_structure')) {
                echo mk_pagination_html($q, trailingslashit($link) . '%_%', 'page/%#%/');
            } else {
                echo mk_pagination_html($q, add_query_arg('paged', '%#%', $link), '');
            }
        }
    } else {
        echo '<p style="text-align:center">Пока нет статей.</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
});

// [mk_contacts]
add_shortcode('mk_contacts', function () {
    $hours = nl2br(esc_html(mk_opt('hours')));
    return '<div class="mk-grid">'
        . '<div class="mk-card"><span class="mk-ico">📍</span><h3>Адрес</h3><p>' . esc_html(mk_opt('address')) . '</p></div>'
        . '<div class="mk-card"><span class="mk-ico">📞</span><h3>Телефон</h3><p><a href="tel:' . esc_attr(mk_phone_raw()) . '">' . esc_html(mk_opt('phone')) . '</a></p></div>'
        . '<div class="mk-card"><span class="mk-ico">✉️</span><h3>Email</h3><p><a href="mailto:' . esc_attr(mk_opt('email')) . '">' . esc_html(mk_opt('email')) . '</a></p></div>'
        . '<div class="mk-card"><span class="mk-ico">🕐</span><h3>Режим работы</h3><p>' . $hours . '</p></div>'
        . '</div>';
});

// [mk_map]
add_shortcode('mk_map', function () {
    $addr = mk_opt('address');
    $src  = 'https://maps.google.com/maps?q=' . rawurlencode($addr) . '&z=15&output=embed';
    $ya   = 'https://yandex.ru/maps/?text=' . rawurlencode($addr);
    $gm   = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($addr);
    return '<div class="mk-map"><iframe src="' . esc_url($src) . '" title="Карта проезда" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>'
        . '<p class="mk-route">' . esc_html(mk_opt('route')) . '<br><a href="' . esc_url($ya) . '" target="_blank" rel="noopener">Открыть в Яндекс.Картах</a> · <a href="' . esc_url($gm) . '" target="_blank" rel="noopener">Открыть в Google Maps</a></p>';
});

// [mk_social]
add_shortcode('mk_social', function () {
    return mk_social_html(true);
});