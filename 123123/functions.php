<?php
if (!defined('ABSPATH')) exit;

define('MK_VER', '1.1.0');
define('MK_DIR', get_template_directory());
define('MK_URI', get_template_directory_uri());

require_once MK_DIR . '/inc/options.php';
require_once MK_DIR . '/inc/helpers.php';
require_once MK_DIR . '/inc/shortcodes.php';
require_once MK_DIR . '/inc/content.php';
require_once MK_DIR . '/inc/setup.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus(array(
        'primary' => 'Главное меню (шапка)',
        'footer'  => 'Меню в подвале',
    ));
    $GLOBALS['content_width'] = 1100;
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('universal-shop', get_stylesheet_uri(), array(), MK_VER);
    wp_enqueue_script('universal-shop', MK_URI . '/assets/js/main.js', array(), MK_VER, true);
});

// WooCommerce
add_filter('woocommerce_show_page_title', '__return_false');
add_filter('loop_shop_per_page', function () { return 9; });
add_action('init', function () {
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
});
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    $fragments['span.mk-cart-count'] = mk_cart_count_html();
    return $fragments;
});

// SEO (если не установлен Yoast)
add_filter('pre_get_document_title', function ($title) {
    if (is_singular() && !defined('WPSEO_VERSION')) {
        $t = get_post_meta(get_queried_object_id(), '_mk_seo_title', true);
        if ($t) return $t;
    }
    return $title;
});
add_action('wp_head', function () {
    if (is_singular() && !defined('WPSEO_VERSION')) {
        $d = get_post_meta(get_queried_object_id(), '_mk_seo_desc', true);
        if ($d) echo '<meta name="description" content="' . esc_attr($d) . '">' . "\n";
    }
}, 1);