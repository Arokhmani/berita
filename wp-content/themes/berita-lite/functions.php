<?php
if (!defined('ABSPATH')) {
    exit;
}

define('BERITA_LITE_VERSION', '1.0.0');
define('BERITA_LITE_PATH', get_template_directory());
define('BERITA_LITE_URI', get_template_directory_uri());

require_once BERITA_LITE_PATH . '/inc/seo.php';
require_once BERITA_LITE_PATH . '/inc/ads.php';
require_once BERITA_LITE_PATH . '/inc/author.php';
require_once BERITA_LITE_PATH . '/inc/interactions.php';

function berita_lite_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');

    register_nav_menus([
        'primary' => __('Primary Menu', 'berita-lite'),
    ]);
}
add_action('after_setup_theme', 'berita_lite_setup');

function berita_lite_assets(): void
{
    wp_enqueue_style('berita-lite-style', get_stylesheet_uri(), [], BERITA_LITE_VERSION);
    wp_enqueue_style('berita-lite-critical', BERITA_LITE_URI . '/assets/css/critical.css', [], BERITA_LITE_VERSION);
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [], '1.11.3');

    wp_enqueue_script('berita-lite-theme', BERITA_LITE_URI . '/assets/js/theme.js', [], BERITA_LITE_VERSION, true);
    wp_localize_script('berita-lite-theme', 'beritaLite', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('berita-lite-actions'),
    ]);
}
add_action('wp_enqueue_scripts', 'berita_lite_assets');

function berita_lite_widgets_init(): void
{
    register_sidebar([
        'name'          => __('Sidebar Artikel', 'berita-lite'),
        'id'            => 'sidebar-article',
        'before_widget' => '<section class="widget">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'berita_lite_widgets_init');

function berita_lite_register_content_types(): void
{
    register_post_type('sponsored_story', [
        'label'        => __('Sponsored Stories', 'berita-lite'),
        'public'       => true,
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'author'],
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'sponsored'],
    ]);

    register_taxonomy('news_topic', ['post', 'sponsored_story'], [
        'label'        => __('News Topics', 'berita-lite'),
        'public'       => true,
        'show_in_rest' => true,
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'topic'],
    ]);
}
add_action('init', 'berita_lite_register_content_types');

