<?php
if (!defined('ABSPATH')) {
    exit;
}

function berita_lite_ajax_post_like(): void
{
    check_ajax_referer('berita-lite-actions', 'nonce');
    $postId = (int) ($_POST['postId'] ?? 0);
    if ($postId <= 0) {
        wp_send_json_error(['message' => 'Invalid post'], 400);
    }

    $cookieKey = 'berita_lite_liked_' . $postId;
    if (!empty($_COOKIE[$cookieKey])) {
        $likes = (int) get_post_meta($postId, 'berita_likes', true);
        wp_send_json_success(['likes' => $likes, 'alreadyLiked' => true]);
    }

    $likes = (int) get_post_meta($postId, 'berita_likes', true);
    $likes++;
    update_post_meta($postId, 'berita_likes', $likes);
    setcookie($cookieKey, '1', time() + WEEK_IN_SECONDS, COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), true);

    wp_send_json_success(['likes' => $likes]);
}
add_action('wp_ajax_berita_lite_post_like', 'berita_lite_ajax_post_like');
add_action('wp_ajax_nopriv_berita_lite_post_like', 'berita_lite_ajax_post_like');

function berita_lite_ajax_comment_reaction(): void
{
    check_ajax_referer('berita-lite-actions', 'nonce');
    $commentId = (int) ($_POST['commentId'] ?? 0);
    $emoji     = sanitize_text_field((string) ($_POST['emoji'] ?? '👍'));
    if ($commentId <= 0 || $emoji === '') {
        wp_send_json_error(['message' => 'Invalid reaction'], 400);
    }
    $allowed = ['👍', '❤️', '😂', '😮', '😢', '😡'];
    if (!in_array($emoji, $allowed, true)) {
        wp_send_json_error(['message' => 'Emoji not allowed'], 400);
    }
    $metaKey = 'berita_reaction_' . md5($emoji);
    $count   = (int) get_comment_meta($commentId, $metaKey, true);
    $count++;
    update_comment_meta($commentId, $metaKey, $count);
    wp_send_json_success(['count' => $count, 'emoji' => $emoji]);
}
add_action('wp_ajax_berita_lite_comment_reaction', 'berita_lite_ajax_comment_reaction');
add_action('wp_ajax_nopriv_berita_lite_comment_reaction', 'berita_lite_ajax_comment_reaction');

function berita_lite_get_comment_reactions(int $commentId): array
{
    $result = [];
    foreach (['👍', '❤️', '😂', '😮', '😢', '😡'] as $emoji) {
        $count = (int) get_comment_meta($commentId, 'berita_reaction_' . md5($emoji), true);
        if ($count > 0) {
            $result[$emoji] = $count;
        }
    }
    return $result;
}

function berita_lite_notify_comment_reply(int $commentId, int $approved, array $commentData): void
{
    if ($approved !== 1 || empty($commentData['comment_parent'])) {
        return;
    }
    $parent = get_comment((int) $commentData['comment_parent']);
    if (!$parent instanceof WP_Comment || empty($parent->comment_author_email)) {
        return;
    }
    $postTitle = get_the_title((int) $commentData['comment_post_ID']);
    $link      = get_comment_link($commentId);
    wp_mail(
        $parent->comment_author_email,
        sprintf(__('Balasan komentar di %s', 'berita-lite'), $postTitle),
        sprintf(__('Komentar Anda mendapat balasan: %s', 'berita-lite'), $link)
    );
}
add_action('comment_post', 'berita_lite_notify_comment_reply', 10, 3);

