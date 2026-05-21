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
<div class="topbar">
    <div class="container topbar__inner">
        <span><?php echo esc_html(wp_date('l, d F Y')); ?></span>
        <span><?php esc_html_e('Portal berita ringan & cepat', 'berita-lite'); ?></span>
    </div>
</div>
<header class="site-header">
    <div class="container">
        <?php berita_lite_render_ad('header_banner'); ?>
        <div class="site-header__bar">
            <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>">
                <span class="site-title__name"><?php bloginfo('name'); ?></span>
                <span class="site-title__tagline"><?php bloginfo('description'); ?></span>
            </a>
            <nav aria-label="<?php esc_attr_e('Primary', 'berita-lite'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'menu',
                    'fallback_cb'    => 'wp_page_menu',
                ]);
                ?>
            </nav>
        </div>
        <?php
        $trending = new WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => 5,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ]);
        if ($trending->have_posts()) :
            ?>
            <div class="trending-strip" aria-label="<?php esc_attr_e('Trending news', 'berita-lite'); ?>">
                <span class="trending-strip__label"><?php esc_html_e('Trending', 'berita-lite'); ?></span>
                <div class="trending-strip__items">
                    <?php
                    while ($trending->have_posts()) :
                        $trending->the_post();
                        ?>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php
            wp_reset_postdata();
        endif;
        ?>
        <?php berita_lite_render_ad('top_banner'); ?>
    </div>
</header>
<main class="container">
    <?php berita_lite_breadcrumbs(); ?>
