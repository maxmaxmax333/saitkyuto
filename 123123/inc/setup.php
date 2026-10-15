<?php
if (!defined('ABSPATH')) exit;

/* ---------- запуск: автоматически после активации темы ---------- */
add_action('admin_init', function () {
    if (wp_doing_ajax() || (defined('DOING_CRON') && DOING_CRON)) return;
    if (!current_user_can('manage_options')) return;
    if (get_option('mk_setup_done') === MK_VER) return;
    if (!class_exists('WooCommerce')) return; // ждём WooCommerce, покажем уведомление
    mk_run_setup_safe();
});

add_action('admin_post_mk_run_setup', function () {
    if (!current_user_can('manage_options')) wp_die('Нет доступа');
    check_admin_referer('mk_run_setup');
    mk_run_setup_safe();
    wp_safe_redirect(admin_url('themes.php?page=mk-settings'));
    exit;
});

add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-warning"><p><strong>Universal Shop:</strong> для создания магазина нужен плагин WooCommerce. <a href="' . esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')) . '">Установить WooCommerce</a>, затем активируйте его — сайт настроится автоматически.</p></div>';
    }
    $msg = get_option('mk_setup_msg');
    if ($msg) {
        $cls = (strpos($msg, 'Ошибка') === 0) ? 'notice-error' : 'notice-success';
        echo '<div class="notice ' . esc_attr($cls) . ' is-dismissible"><p><strong>Universal Shop:</strong> ' . esc_html($msg) . '</p></div>';
        delete_option('mk_setup_msg');
    }
});

function mk_run_setup_safe() {
    @set_time_limit(300);
    try {
        mk_run_setup();
        update_option('mk_setup_done', MK_VER);
        update_option('mk_setup_msg', 'Сайт настроен: страницы, категории, товары, посты и меню созданы.');
    } catch (Throwable $e) {
        update_option('mk_setup_msg', 'Ошибка настройки: ' . $e->getMessage());
    }
}

/* ---------- основная настройка ---------- */
function mk_run_setup() {
    if (!class_exists('WooCommerce')) throw new Exception('не активен WooCommerce');

    mk_setup_permalinks();
    mk_setup_wc_pages();          // Магазин, Корзина, Оформление, Аккаунт
    $pages = mk_setup_pages();    // Главная, О нас, Контакты, Доставка, Блог
    $cats  = mk_setup_categories();
    mk_setup_products($cats);
    mk_setup_posts();
    mk_setup_menus($pages);
    mk_setup_options($pages);
    mk_cleanup_defaults();
    flush_rewrite_rules();
}

function mk_setup_permalinks() {
    global $wp_rewrite;
    if (!get_option('permalink_structure')) {
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();
    }
}

function mk_upsert_page($slug, $title, $content, $kit = false) {
    $existing = mk_find_post($slug, 'page');
    $now      = current_time('mysql');
    $now_gmt  = current_time('mysql', true);
    $data = array(
        'post_type'    => 'page',
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_status'  => 'publish',
    );
    if ($existing) {
        if ($existing->post_status === 'trash') wp_untrash_post($existing->ID);
        $data['ID']            = $existing->ID;
        $data['post_date']     = $now;
        $data['post_date_gmt'] = $now_gmt;
        $id = wp_update_post(wp_slash($data), true);
    } else {
        $id = wp_insert_post(wp_slash($data), true);
    }
    if (is_wp_error($id)) throw new Exception($id->get_error_message());
    if ($kit) update_post_meta($id, '_mk_kit', 1);
    return (int) $id;
}

function mk_setup_wc_pages() {
    $bad = array('home', 'about', 'contacts', 'delivery', 'blog');
    $specs = array(
        'shop'      => array('shop', 'Магазин', ''),
        'cart'      => array('cart', 'Корзина', '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->'),
        'checkout'  => array('checkout', 'Оформление заказа', '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->'),
        'myaccount' => array('my-account', 'Мой аккаунт', '<!-- wp:shortcode -->[woocommerce_my_account]<!-- /wp:shortcode -->'),
    );
    foreach ($specs as $key => $s) {
        $opt = 'woocommerce_' . $key . '_page_id';
        $id  = (int) get_option($opt);
        $p   = $id ? get_post($id) : null;
        $ok  = $p && $p->post_type === 'page' && $p->post_status === 'publish' && !in_array($p->post_name, $bad, true);
        if (!$ok) {
            $id = mk_upsert_page($s[0], $s[1], $s[2], false);
            update_option($opt, $id);
        }
    }
}

function mk_setup_pages() {
    $urls = array(
        '%%SHOP%%'     => mk_shop_url(),
        '%%ABOUT%%'    => home_url('/about/'),
        '%%CONTACTS%%' => home_url('/contacts/'),
        '%%DELIVERY%%' => home_url('/delivery/'),
        '%%BLOG%%'     => home_url('/blog/'),
        '%%CART%%'     => mk_cart_url(),
    );
    $ids = array();
    foreach (mk_kit_pages() as $slug => $p) {
        $content = str_replace(array_keys($urls), array_values($urls), $p['content']);
        $id = mk_upsert_page($slug, $p['title'], $content, true);
        update_post_meta($id, '_mk_seo_title', $p['seo_t']);
        update_post_meta($id, '_mk_seo_desc', $p['seo_d']);
        update_post_meta($id, '_yoast_wpseo_title', $p['seo_t']);
        update_post_meta($id, '_yoast_wpseo_metadesc', $p['seo_d']);
        $ids[$slug] = $id;
    }
    return $ids;
}

function mk_term_id($name, $slug, $tax, $parent = 0) {
    $t = $slug ? get_term_by('slug', $slug, $tax) : get_term_by('name', $name, $tax);
    if ($t && !is_wp_error($t)) return (int) $t->term_id;
    $args = array();
    if ($slug) $args['slug'] = $slug;
    if ($parent) $args['parent'] = $parent;
    $r = wp_insert_term($name, $tax, $args);
    if (is_wp_error($r)) {
        $d = $r->get_error_data('term_exists');
        return $d ? (int) $d : 0;
    }
    return (int) $r['term_id'];
}

function mk_setup_categories() {
    $map = array();
    foreach (mk_kit_categories() as $c) {
        $parent = $c['parent'] ? (int) $map[$c['parent']] : 0;
        $map[$c['slug']] = mk_term_id($c['name'], $c['slug'], 'product_cat', $parent);
    }
    return $map;
}

function mk_import_image($filename, $title) {
    $src = MK_DIR . '/assets/img/' . $filename;
    if (!file_exists($src)) return 0;
    $found = get_posts(array('post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1,
        'meta_key' => '_mk_src', 'meta_value' => $filename, 'fields' => 'ids'));
    if ($found) return (int) $found[0];
    $up = wp_upload_bits('mk-' . $filename, null, file_get_contents($src));
    if (!empty($up['error'])) return 0;
    $att = wp_insert_attachment(array(
        'post_mime_type' => 'image/png',
        'post_title'     => $title,
        'post_status'    => 'inherit',
    ), $up['file']);
    if (is_wp_error($att) || !$att) return 0;
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($att, wp_generate_attachment_metadata($att, $up['file']));
    update_post_meta($att, '_mk_src', $filename);
    update_post_meta($att, '_wp_attachment_image_alt', $title);
    return (int) $att;
}

function mk_setup_products($cats) {
    foreach (mk_kit_products() as $s) {
        $pid = wc_get_product_id_by_sku($s['sku']);
        $p   = $pid ? wc_get_product($pid) : null;
        if (!$p) $p = new WC_Product_Simple();
        $p->set_name($s['name']);
        $p->set_slug($s['slug']);
        $p->set_status('publish');
        $p->set_catalog_visibility('visible');
        $p->set_sku($s['sku']);
        $p->set_regular_price((string) $s['price']);
        $p->set_short_description($s['short']);
        $p->set_description($s['desc']);
        $p->set_featured(!empty($s['featured']));
        $p->set_sold_individually(!empty($s['single']));
        if ($s['stock'] > 0) {
            $p->set_manage_stock(true);
            $p->set_stock_quantity((int) $s['stock']);
        } else {
            $p->set_manage_stock(false);
        }
        $p->set_stock_status('instock');
        $cat_ids = array();
        foreach ($s['cats'] as $slug) {
            if (!empty($cats[$slug])) $cat_ids[] = (int) $cats[$slug];
        }
        $p->set_category_ids($cat_ids);
        $tag_ids = array();
        foreach ($s['tags'] as $tn) {
            $tid = mk_term_id($tn, '', 'product_tag');
            if ($tid) $tag_ids[] = $tid;
        }
        $p->set_tag_ids($tag_ids);
        $img = mk_import_image($s['img'], $s['name']);
        if ($img) $p->set_image_id($img);
        $p->save();
    }
}

function mk_setup_posts() {
    $cat_defs = array(
        'news' => 'Новости',
        'care' => 'Полезное',
        'docs' => 'Документы',
    );
    $cat_ids = array();
    foreach ($cat_defs as $slug => $name) {
        $cat_ids[$slug] = mk_term_id($name, $slug, 'category');
    }
    $gmt_offset = (float) get_option('gmt_offset');
    foreach (mk_kit_posts() as $i => $p) {
        $ts   = time() - ($i + 1) * 86400 * 2;
        $data = array(
            'post_type'     => 'post',
            'post_status'   => 'publish',
            'post_title'    => $p['title'],
            'post_name'     => $p['slug'],
            'post_content'  => $p['body'],
            'post_excerpt'  => $p['excerpt'],
            'post_date'     => gmdate('Y-m-d H:i:s', $ts + (int) round($gmt_offset * 3600)),
            'post_date_gmt' => gmdate('Y-m-d H:i:s', $ts),
        );
        $ex = mk_find_post($p['slug'], 'post');
        if ($ex) {
            if ($ex->post_status === 'trash') wp_untrash_post($ex->ID);
            $data['ID'] = $ex->ID;
            $id = wp_update_post(wp_slash($data), true);
        } else {
            $id = wp_insert_post(wp_slash($data), true);
        }
        if (is_wp_error($id)) throw new Exception($id->get_error_message());
        if (!empty($cat_ids[$p['cat']])) wp_set_post_categories($id, array((int) $cat_ids[$p['cat']]));
    }
}

function mk_get_menu_id($name) {
    $m = wp_get_nav_menu_object($name);
    if ($m) return (int) $m->term_id;
    $id = wp_create_nav_menu($name);
    if (is_wp_error($id)) throw new Exception($id->get_error_message());
    return (int) $id;
}

function mk_menu_add($menu_id, $title, $page_id) {
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title'     => $title,
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $page_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
    ));
}

function mk_setup_menus($pages) {
    $shop = (int) get_option('woocommerce_shop_page_id');
    $cart = (int) get_option('woocommerce_cart_page_id');
    $acc  = (int) get_option('woocommerce_myaccount_page_id');

    $main = mk_get_menu_id('Главное меню');
    foreach ((array) wp_get_nav_menu_items($main) as $it) wp_delete_post($it->ID, true);
    mk_menu_add($main, 'Главная', $pages['home']);
    mk_menu_add($main, 'Каталог', $shop);
    mk_menu_add($main, 'Блог', $pages['blog']);
    mk_menu_add($main, 'О нас', $pages['about']);
    mk_menu_add($main, 'Доставка и оплата', $pages['delivery']);
    mk_menu_add($main, 'Контакты', $pages['contacts']);

    $foot = mk_get_menu_id('Меню в подвале');
    foreach ((array) wp_get_nav_menu_items($foot) as $it) wp_delete_post($it->ID, true);
    mk_menu_add($foot, 'Каталог', $shop);
    mk_menu_add($foot, 'Блог', $pages['blog']);
    mk_menu_add($foot, 'О нас', $pages['about']);
    mk_menu_add($foot, 'Доставка и оплата', $pages['delivery']);
    mk_menu_add($foot, 'Контакты', $pages['contacts']);
    mk_menu_add($foot, 'Корзина', $cart);
    mk_menu_add($foot, 'Мой аккаунт', $acc);

    $loc = get_theme_mod('nav_menu_locations');
    if (!is_array($loc)) $loc = array();
    $loc['primary'] = $main;
    $loc['footer']  = $foot;
    set_theme_mod('nav_menu_locations', $loc);
}

function mk_setup_options($pages) {
    // главная страница
    update_option('show_on_front', 'page');
    update_option('page_on_front', $pages['home']);
    update_option('page_for_posts', 0);
    if (!get_option('blogname') || get_option('blogname') === 'mysite') update_option('blogname', 'Мой сайт');
    update_option('blogdescription', 'Универсальный магазин');

    // WooCommerce: валюта, страна, открытый магазин
    update_option('woocommerce_currency', 'RUB');
    update_option('woocommerce_default_country', 'RU');
    update_option('woocommerce_currency_pos', 'right_space');
    update_option('woocommerce_price_thousand_sep', ' ');
    update_option('woocommerce_price_decimal_sep', ',');
    update_option('woocommerce_price_num_decimals', '0');
    update_option('woocommerce_coming_soon', 'no');
    update_option('woocommerce_store_pages_only', 'no');

    // способы оплаты
    $cod = get_option('woocommerce_cod_settings', array());
    if (!is_array($cod)) $cod = array();
    $cod['enabled'] = 'yes';
    $cod['title']   = 'Оплата при получении';
    update_option('woocommerce_cod_settings', $cod);
    $bacs = get_option('woocommerce_bacs_settings', array());
    if (!is_array($bacs)) $bacs = array();
    $bacs['enabled'] = 'yes';
    $bacs['title']   = 'Банковский перевод';
    update_option('woocommerce_bacs_settings', $bacs);
}

function mk_cleanup_defaults() {
    $hw = get_post(1);
    if ($hw && $hw->post_type === 'post' && in_array($hw->post_name, array('hello-world', 'privet-mir'), true)) {
        wp_delete_post(1, true);
    }
    $sp = get_post(2);
    if ($sp && $sp->post_type === 'page' && in_array($sp->post_name, array('sample-page', 'primer-stranitsy'), true)) {
        wp_delete_post(2, true);
    }
}