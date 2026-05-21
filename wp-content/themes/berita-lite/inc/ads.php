<?php
if (!defined('ABSPATH')) {
    exit;
}

function berita_lite_ad_placements(): array
{
    return [
        'header_banner' => __('Header Banner', 'berita-lite'),
        'top_banner'    => __('Top Banner', 'berita-lite'),
        'native'        => __('Native In Content', 'berita-lite'),
        'inline'        => __('Inline Paragraph', 'berita-lite'),
        'anchor'        => __('Anchor Sticky Bottom', 'berita-lite'),
        'parallax'      => __('Parallax', 'berita-lite'),
    ];
}

function berita_lite_register_ad_settings(): void
{
    register_setting('berita_lite_ads_group', 'berita_lite_ads', [
        'type'              => 'array',
        'sanitize_callback' => 'berita_lite_sanitize_ads',
        'default'           => [],
    ]);
    register_setting('berita_lite_ads_group', 'berita_lite_inline_interval', [
        'type'              => 'integer',
        'sanitize_callback' => static function ($value): int {
            return max(2, (int) $value);
        },
        'default'           => 4,
    ]);
}
add_action('admin_init', 'berita_lite_register_ad_settings');

function berita_lite_sanitize_ads($input): array
{
    if (!is_array($input)) {
        return [];
    }
    $clean = [];
    foreach (berita_lite_ad_placements() as $key => $label) {
        $clean[$key] = isset($input[$key]) ? wp_unslash((string) $input[$key]) : '';
    }
    return $clean;
}

function berita_lite_ad_admin_menu(): void
{
    add_theme_page(
        __('Ad Manager', 'berita-lite'),
        __('Ad Manager', 'berita-lite'),
        'manage_options',
        'berita-lite-ads',
        'berita_lite_render_ad_admin_page'
    );
}
add_action('admin_menu', 'berita_lite_ad_admin_menu');

function berita_lite_render_ad_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $ads      = get_option('berita_lite_ads', []);
    $interval = (int) get_option('berita_lite_inline_interval', 4);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Berita Lite Ad Manager', 'berita-lite'); ?></h1>
        <p><?php esc_html_e('Satu baris = satu iklan: start|end|HTML. Contoh: 2026-01-01|2026-12-31|<div>Ad Script/Markup</div>', 'berita-lite'); ?></p>
        <form method="post" action="options.php">
            <?php settings_fields('berita_lite_ads_group'); ?>
            <table class="form-table" role="presentation">
                <?php foreach (berita_lite_ad_placements() as $key => $label) : ?>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
                        <td>
                            <textarea class="large-text code" rows="6" id="<?php echo esc_attr($key); ?>" name="berita_lite_ads[<?php echo esc_attr($key); ?>]"><?php echo esc_textarea($ads[$key] ?? ''); ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th scope="row"><label for="berita_lite_inline_interval"><?php esc_html_e('Jarak inline ads (paragraf)', 'berita-lite'); ?></label></th>
                    <td><input id="berita_lite_inline_interval" name="berita_lite_inline_interval" type="number" min="2" value="<?php echo esc_attr((string) $interval); ?>"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function berita_lite_parse_ad_lines(string $slotText): array
{
    $lines = array_filter(array_map('trim', explode("\n", $slotText)));
    $now   = current_time('timestamp');
    $ads   = [];
    foreach ($lines as $line) {
        $parts = array_map('trim', explode('|', $line, 3));
        $start = $parts[0] ?? '';
        $end   = $parts[1] ?? '';
        $html  = $parts[2] ?? '';
        if ($html === '') {
            continue;
        }
        $startTs = $start ? strtotime($start . ' 00:00:00') : 0;
        $endTs   = $end ? strtotime($end . ' 23:59:59') : PHP_INT_MAX;
        if (($startTs === false || $endTs === false) || $now < $startTs || $now > $endTs) {
            continue;
        }
        $ads[] = ['html' => $html];
    }
    return $ads;
}

function berita_lite_get_random_ad(string $placement): string
{
    $settings = get_option('berita_lite_ads', []);
    $slot     = (string) ($settings[$placement] ?? '');
    $ads      = berita_lite_parse_ad_lines($slot);
    if ($ads === []) {
        return '';
    }
    return $ads[array_rand($ads)]['html'] ?? '';
}

function berita_lite_render_ad(string $placement, string $extraClass = ''): void
{
    $html = berita_lite_get_random_ad($placement);
    if ($html === '') {
        return;
    }
    echo '<div class="ad-slot ad-slot--' . esc_attr($placement . ($extraClass ? ' ' . $extraClass : '')) . '">' . do_shortcode($html) . '</div>';
}

function berita_lite_inject_ads_to_content(string $content): string
{
    if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $native = berita_lite_get_random_ad('native');
    if ($native !== '') {
        $content = '<div class="ad-slot ad-slot--native">' . do_shortcode($native) . '</div>' . $content;
    }

    $inlineHtml = berita_lite_get_random_ad('inline');
    if ($inlineHtml === '') {
        return $content;
    }

    $parts    = explode('</p>', $content);
    $interval = max(2, (int) get_option('berita_lite_inline_interval', 4));
    $count    = 0;
    foreach ($parts as $idx => $part) {
        if (trim($part) === '') {
            continue;
        }
        $count++;
        $parts[$idx] = $part . '</p>';
        if ($count % $interval === 0) {
            $parts[$idx] .= '<div class="ad-slot ad-slot--inline">' . do_shortcode($inlineHtml) . '</div>';
        }
    }
    return implode('', $parts);
}
add_filter('the_content', 'berita_lite_inject_ads_to_content', 20);

function berita_lite_render_footer_ads(): void
{
    berita_lite_render_ad('parallax', 'ad-slot--parallax');
    berita_lite_render_ad('anchor', 'ad-slot--anchor');
}
add_action('wp_footer', 'berita_lite_render_footer_ads', 5);

