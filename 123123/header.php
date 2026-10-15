<?php if (!defined('ABSPATH')) exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="mk-header" id="mk-header">
  <div class="mk-topbar">
    <div class="mk-container">
      <span>📞 <a href="tel:<?php echo esc_attr(mk_phone_raw()); ?>"><?php echo esc_html(mk_opt('phone')); ?></a></span>
      <span>🕐 <?php echo esc_html(mk_opt('hours_short')); ?></span>
    </div>
  </div>
  <div class="mk-container mk-navbar">
    <a class="mk-logo" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(mk_opt('brand')); ?></a>
    <button class="mk-burger" type="button" aria-label="Меню" aria-expanded="false">☰</button>
    <nav class="mk-nav" aria-label="Главное меню">
      <?php
      wp_nav_menu(array(
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'mk-menu',
          'fallback_cb'    => 'mk_fallback_menu',
      ));
      ?>
    </nav>
    <a class="mk-cart" href="<?php echo esc_url(mk_cart_url()); ?>">🛒 Корзина <?php echo mk_cart_count_html(); ?></a>
  </div>
</header>
<main id="content" class="mk-main">