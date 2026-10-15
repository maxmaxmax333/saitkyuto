<?php
if (!defined('ABSPATH')) exit;

function mk_defaults() {
    return array(
        'brand'       => 'Мой сайт',
        'phone'       => '+7 (000) 000-00-00',
        'email'       => 'info@example.ru',
        'address'     => 'г. Москва, ул. Примерная, д. 1, офис 10',
        'hours'       => "Пн–Пт 10:00–19:00\nСб 11:00–16:00\nВс — выходной",
        'hours_short' => 'Пн–Пт 10–19, Сб 11–16',
        'route'       => 'Как проехать: 5 минут пешком от метро.',
        'vk'          => '',
        'telegram'    => '',
        'whatsapp'    => '',
        'youtube'     => '',
    );
}

function mk_opt($key) {
    $d = mk_defaults();
    $o = get_option('mk_opts', array());
    if (!is_array($o)) $o = array();
    if (isset($o[$key]) && $o[$key] !== '') return $o[$key];
    return isset($d[$key]) ? $d[$key] : '';
}

function mk_phone_raw() {
    return preg_replace('/[^\d+]/', '', mk_opt('phone'));
}

add_action('admin_init', function () {
    register_setting('mk_group', 'mk_opts', array('sanitize_callback' => 'mk_sanitize_opts'));
});

function mk_sanitize_opts($in) {
    $out = array();
    $urls = array('vk', 'telegram', 'whatsapp', 'youtube');
    foreach (array_keys(mk_defaults()) as $k) {
        if (!isset($in[$k])) continue;
        if (in_array($k, $urls, true)) {
            $out[$k] = esc_url_raw(trim($in[$k]));
        } elseif ($k === 'hours' || $k === 'route') {
            $out[$k] = sanitize_textarea_field($in[$k]);
        } elseif ($k === 'email') {
            $out[$k] = sanitize_email($in[$k]);
        } else {
            $out[$k] = sanitize_text_field($in[$k]);
        }
    }
    return $out;
}

add_action('admin_menu', function () {
    add_theme_page('Настройки сайта', 'Настройки сайта', 'manage_options', 'mk-settings', 'mk_settings_page');
});

function mk_settings_page() {
    if (!current_user_can('manage_options')) return;
    $labels = array(
        'brand' => 'Название сайта/магазина',
        'phone' => 'Телефон',
        'email' => 'Email',
        'address' => 'Адрес (для карты)',
        'hours' => 'Режим работы (полный)',
        'hours_short' => 'Режим работы (кратко)',
        'route' => 'Текст под картой',
        'vk' => 'ВКонтакте',
        'telegram' => 'Telegram',
        'whatsapp' => 'WhatsApp',
        'youtube' => 'YouTube',
    );
    $wc = class_exists('WooCommerce');
    $seo = defined('WPSEO_VERSION');
    ?>
    <div class="wrap">
      <h1>Настройки сайта</h1>
      <h2>Плагины</h2>
      <ul style="list-style:disc;margin-left:20px">
        <li>WooCommerce (обязателен): <strong><?php echo $wc ? 'установлен ✔' : 'не активен ✘'; ?></strong>
          <?php if (!$wc) : ?> — <a href="<?php echo esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')); ?>">установить</a><?php endif; ?></li>
        <li>Yoast SEO (по желанию): <strong><?php echo $seo ? 'установлен ✔' : 'не установлен'; ?></strong></li>
      </ul>
      <h2>Контакты, карта, соцсети</h2>
      <form method="post" action="options.php">
        <?php settings_fields('mk_group'); ?>
        <table class="form-table">
        <?php foreach ($labels as $k => $label) : ?>
          <tr><th><label for="mk_<?php echo esc_attr($k); ?>"><?php echo esc_html($label); ?></label></th>
          <td>
          <?php if ($k === 'hours' || $k === 'route') : ?>
            <textarea class="large-text" rows="3" id="mk_<?php echo esc_attr($k); ?>" name="mk_opts[<?php echo esc_attr($k); ?>]"><?php echo esc_textarea(mk_opt($k)); ?></textarea>
          <?php else : ?>
            <input class="regular-text" style="width:100%;max-width:520px" type="text" id="mk_<?php echo esc_attr($k); ?>" name="mk_opts[<?php echo esc_attr($k); ?>]" value="<?php echo esc_attr(mk_opt($k)); ?>">
          <?php endif; ?>
          </td></tr>
        <?php endforeach; ?>
        </table>
        <?php submit_button('Сохранить'); ?>
      </form>
      <hr>
      <h2>Пересоздать контент</h2>
      <p>Создаст (или обновит) страницы, категории, товары, посты и меню заново. Используйте после правок в <code>content.php</code> и <code>setup.php</code>.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="mk_run_setup">
        <?php wp_nonce_field('mk_run_setup'); ?>
        <?php submit_button('Запустить настройку сайта', 'secondary'); ?>
      </form>
    </div>
    <?php
}