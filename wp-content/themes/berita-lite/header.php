<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="container">
        <?php berita_lite_render_ad('header_banner'); ?>
        <div class="site-header__bar">
            <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
            <nav aria-label="<?php esc_attr_e('Primary', 'berita-lite'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'menu',
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>
        </div>
        <?php berita_lite_render_ad('top_banner'); ?>
    </div>
</header>
<main class="container">
    <?php berita_lite_breadcrumbs(); ?>

