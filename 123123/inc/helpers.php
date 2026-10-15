<?php
if (!defined('ABSPATH')) exit;

function mk_find_post($slug, $type = 'page') {
    $q = get_posts(array(
        'name'             => $slug,
        'post_type'        => $type,
        'post_status'      => array('publish', 'draft', 'pending', 'future', 'private', 'trash'),
        'numberposts'      => 1,
        'suppress_filters' => true,
    ));
    return $q ? $q[0] : null;
}

function mk_page_url($slug) {
    $p = mk_find_post($slug, 'page');
    if ($p && $p->post_status === 'publish') return get_permalink($p);
    return home_url('/' . $slug . '/');
}

function mk_shop_url() {
    if (function_exists('wc_get_page_permalink')) {
        $u = wc_get_page_permalink('shop');
        if ($u) return $u;
    }
    return home_url('/shop/');
}

function mk_cart_url() {
    if (function_exists('wc_get_cart_url')) return wc_get_cart_url();
    return home_url('/cart/');
}

function mk_cart_count_html() {
    $n = 0;
    if (function_exists('WC') && WC() && isset(WC()->cart) && WC()->cart) {
        $n = (int) WC()->cart->get_cart_contents_count();
    }
    return '<span class="mk-cart-count">' . $n . '</span>';
}

function mk_fallback_menu() {
    echo '<ul class="mk-menu">';
    wp_list_pages(array('title_li' => '', 'depth' => 1, 'exclude' => ''));
    echo '</ul>';
}

function mk_social_html($center = false) {
    $items = array('vk' => 'ВКонтакте', 'telegram' => 'Telegram', 'whatsapp' => 'WhatsApp', 'youtube' => 'YouTube');
    $h = '<div class="mk-social' . ($center ? ' center' : '') . '">';
    foreach ($items as $k => $label) {
        $u = mk_opt($k);
        if ($u) $h .= '<a href="' . esc_url($u) . '" target="_blank" rel="noopener nofollow">' . esc_html($label) . '</a>';
    }
    return $h . '</div>';
}

function mk_post_card() {
    $cats = get_the_category();
    $cat  = $cats ? esc_html($cats[0]->name) : 'Статья';
    ?>
    <article class="mk-post">
      <a class="mk-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php if (has_post_thumbnail()) { the_post_thumbnail('medium_large'); } else { echo '🐵'; } ?>
      </a>
      <div class="mk-pbody">
        <span class="mk-pcat"><?php echo $cat; ?></span>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="mk-pmeta"><?php echo esc_html(get_the_date()); ?></div>
        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
        <a class="mk-readmore" href="<?php the_permalink(); ?>">Читать дальше →</a>
      </div>
    </article>
    <?php
}

function mk_pagination_html($query = null, $base = '', $format = '') {
    global $wp_query;
    $q = $query ? $query : $wp_query;
    $total = (int) $q->max_num_pages;
    if ($total < 2) return '';
    $current = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    $args = array(
        'total'     => $total,
        'current'   => $current,
        'type'      => 'list',
        'prev_text' => '← Назад',
        'next_text' => 'Вперёд →',
    );
    if ($base) {
        $args['base']   = $base;
        $args['format'] = $format;
    }
    return '<nav class="mk-pagination" aria-label="Страницы">' . paginate_links($args) . '</nav>';
}
