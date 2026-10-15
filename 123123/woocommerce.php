<?php
if (!defined('ABSPATH')) exit;
get_header();
?>
<?php if (is_singular('product')) : ?>
<div class="mk-pagehead"><div class="mk-ph">Магазин</div></div>
<?php else : ?>
<div class="mk-pagehead"><h1><?php echo esc_html(wp_strip_all_tags(woocommerce_page_title(false))); ?></h1></div>
<?php endif; ?>
<div class="mk-container mk-wc">
  <?php woocommerce_content(); ?>
</div>
<?php get_footer();
