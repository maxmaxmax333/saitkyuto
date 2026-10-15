<?php if (!defined('ABSPATH')) exit; ?>
</main>
<footer class="mk-footer">
  <div class="mk-container mk-fgrid">
    <div>
      <h4><?php echo esc_html(mk_opt('brand')); ?></h4>
      <p>Краткое описание вашей компании. Замените этот текст в админке (Внешний вид → Настройки сайта) или прямо здесь.</p>
    </div>
    <div>
      <h4>Навигация</h4>
      <?php
      wp_nav_menu(array(
          'theme_location' => 'footer',
          'container'      => false,
          'menu_class'     => 'mk-fmenu',
          'fallback_cb'    => 'mk_fallback_menu',
      ));
      ?>
    </div>
    <div>
      <h4>Контакты</h4>
      <p>📍 <?php echo esc_html(mk_opt('address')); ?></p>
      <p>📞 <a href="tel:<?php echo esc_attr(mk_phone_raw()); ?>"><?php echo esc_html(mk_opt('phone')); ?></a></p>
      <p>✉️ <a href="mailto:<?php echo esc_attr(mk_opt('email')); ?>"><?php echo esc_html(mk_opt('email')); ?></a></p>
      <p>🕐 <?php echo esc_html(mk_opt('hours_short')); ?></p>
    </div>
    <div>
      <h4>Мы в соцсетях</h4>
      <?php echo mk_social_html(); ?>
    </div>
  </div>
  <div class="mk-copy">© <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html(mk_opt('brand')); ?>. Все права защищены.</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>