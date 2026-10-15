<?php
if (!defined('ABSPATH')) exit;
get_header();
while (have_posts()) : the_post();
    $kit = (int) get_post_meta(get_the_ID(), '_mk_kit', true);
    if ($kit) {
        remove_filter('the_content', 'wpautop');
        remove_filter('the_content', 'wptexturize');
        echo '<div class="mk-kitpage">';
        the_content();
        echo '</div>';
    } else {
        ?>
        <div class="mk-pagehead"><h1><?php the_title(); ?></h1></div>
        <div class="mk-container mk-pagebody"><div class="mk-entry"><?php the_content(); ?></div></div>
        <?php
    }
endwhile;
get_footer();
