<?php
if (!defined('ABSPATH')) {
    exit;
}

function berita_lite_print_seo_meta(): void
{
    if (is_admin()) {
        return;
    }

    global $post;
    $title = wp_get_document_title();
    $requestUri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '/';
    $url        = is_singular() ? get_permalink() : home_url($requestUri);
    $desc  = get_bloginfo('description');

    if (is_singular() && $post instanceof WP_Post) {
        $desc = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags((string) $post->post_content), 24);
    }

    $desc = esc_attr(wp_strip_all_tags($desc));
    $url  = esc_url($url);
    echo '<meta name="description" content="' . $desc . '">';
    echo '<link rel="canonical" href="' . $url . '">';
    echo '<meta property="og:title" content="' . esc_attr($title) . '">';
    echo '<meta property="og:description" content="' . $desc . '">';
    echo '<meta property="og:type" content="' . (is_singular('post') ? 'article' : 'website') . '">';
    echo '<meta property="og:url" content="' . $url . '">';
    echo '<meta name="twitter:card" content="summary_large_image">';

    if (is_singular('post') && $post instanceof WP_Post) {
        $schema = [
            '@context'         => 'https://schema.org',
            '@type'            => 'NewsArticle',
            'headline'         => get_the_title($post),
            'datePublished'    => get_the_date(DATE_W3C, $post),
            'dateModified'     => get_the_modified_date(DATE_W3C, $post),
            'author'           => [
                '@type' => 'Person',
                'name'  => get_the_author_meta('display_name', (int) $post->post_author),
            ],
            'mainEntityOfPage' => $url,
            'description'      => wp_strip_all_tags($desc),
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    }
}
add_action('wp_head', 'berita_lite_print_seo_meta', 1);

function berita_lite_breadcrumbs(): void
{
    if (is_front_page()) {
        return;
    }
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumbs', 'berita-lite') . '"><ol>';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'berita-lite') . '</a></li>';
    if (is_single()) {
        $postsPage = (int) get_option('page_for_posts');
        $postsUrl  = $postsPage > 0 ? get_permalink($postsPage) : home_url('/');
        echo '<li>/</li><li><a href="' . esc_url((string) $postsUrl) . '">' . esc_html__('Berita', 'berita-lite') . '</a></li>';
        echo '<li>/</li><li>' . esc_html(get_the_title()) . '</li>';
    } elseif (is_archive()) {
        echo '<li>/</li><li>' . esc_html(post_type_archive_title('', false)) . '</li>';
    } else {
        echo '<li>/</li><li>' . esc_html(wp_get_document_title()) . '</li>';
    }
    echo '</ol></nav>';
}
