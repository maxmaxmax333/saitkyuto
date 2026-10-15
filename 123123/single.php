<?php
if (!defined('ABSPATH')) exit;
get_header();
while (have_posts()) : the_post();
    $cats = get_the_category();
    ?>
    <div class="mk-pagehead"><h1><?php the_title(); ?></h1></div>
    <article class="mk-single">
      <div class="mk-pmeta"><?php echo $cats ? esc_html($cats[0]->name) . ' · ' : ''; ?><?php echo esc_html(get_the_date()); ?></div>
      <?php if (has_post_thumbnail()) { the_post_thumbnail('large'); } ?>
      <div class="mk-entry"><?php the_content(); ?></div>
      <p style="text-align:center;margin-top:40px"><a class="mk-btn c" href="<?php echo esc_url(mk_page_url('blog')); ?>">← Все статьи</a></p>
    </article>
    <?php
endwhile;
get_footer();
