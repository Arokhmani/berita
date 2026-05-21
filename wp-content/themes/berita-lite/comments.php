<?php
if (post_password_required()) {
    return;
}

if (!function_exists('berita_lite_comment_item')) {
    function berita_lite_comment_item($comment, $args, $depth): void
    {
        ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            <article class="comment-body">
                <strong><?php comment_author(); ?></strong>
                <div><?php comment_text(); ?></div>
                <div class="entry-meta"><?php echo esc_html(get_comment_date()); ?></div>
                <div class="comment-reactions">
                    <?php foreach (['👍', '❤️', '😂', '😮', '😢', '😡'] as $emoji) : ?>
                        <button class="btn-icon js-comment-reaction" data-comment="<?php comment_ID(); ?>" data-emoji="<?php echo esc_attr($emoji); ?>">
                            <?php echo esc_html($emoji); ?>
                            <span class="js-comment-count">
                                <?php
                                $counts = berita_lite_get_comment_reactions((int) $comment->comment_ID);
                                echo esc_html((string) ($counts[$emoji] ?? 0));
                                ?>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php
                comment_reply_link(array_merge($args, [
                    'depth'      => $depth,
                    'max_depth'  => $args['max_depth'],
                    'reply_text' => __('Balas', 'berita-lite'),
                ]));
                ?>
            </article>
        </li>
        <?php
    }
}
?>
<section id="comments" class="single" aria-label="<?php esc_attr_e('Comments', 'berita-lite'); ?>">
    <?php if (have_comments()) : ?>
        <h2><?php comments_number(__('Komentar', 'berita-lite'), __('1 Komentar', 'berita-lite'), __('% Komentar', 'berita-lite')); ?></h2>
        <ol>
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'avatar_size' => 32,
                'short_ping'  => true,
                'callback'    => 'berita_lite_comment_item',
            ]);
            ?>
        </ol>
        <?php the_comments_navigation(); ?>
    <?php endif; ?>
    <?php comment_form(); ?>
</section>
