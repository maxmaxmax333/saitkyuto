<?php
if (!defined('ABSPATH')) exit;
get_header();
if (is_home() && !is_front_page()) {
    $title = 'Блог';
} elseif (is_search()) {
    $title = 'Поиск: ' . get_search_query();
} elseif (is_archive()) {
    $title = wp_strip_all_tags(get_the_archive_title());
} else {
    $title = 'Блог';
}
?>
<div class="mk-pagehead"><h1><?php echo esc_html($title); ?></h1></div>
<div class="mk-container mk-pagebody">
<?php if (have_posts()) : ?>
  <div class="mk-posts">
    <?php while (have_posts()) : the_post(); mk_post_card(); endwhile; ?>
  </div>
  <?php echo mk_pagination_html(); ?>
<?php else : ?>
  <p style="text-align:center">Ничего не найдено.</p>
<?php endif; ?>
</div>
<?php get_footer();
