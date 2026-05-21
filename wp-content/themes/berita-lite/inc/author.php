<?php
if (!defined('ABSPATH')) {
    exit;
}

function berita_lite_author_fields($user): void
{
    ?>
    <h2><?php esc_html_e('Berita Lite Author Profile', 'berita-lite'); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th><label for="berita_verified"><?php esc_html_e('Verified Author', 'berita-lite'); ?></label></th>
            <td><input type="checkbox" name="berita_verified" id="berita_verified" value="1" <?php checked((int) get_user_meta($user->ID, 'berita_verified', true), 1); ?>></td>
        </tr>
        <tr>
            <th><label for="berita_followers"><?php esc_html_e('Followers', 'berita-lite'); ?></label></th>
            <td><input type="number" min="0" name="berita_followers" id="berita_followers" value="<?php echo esc_attr((string) get_user_meta($user->ID, 'berita_followers', true)); ?>"></td>
        </tr>
        <tr>
            <th><label for="berita_donation_url"><?php esc_html_e('Donation URL', 'berita-lite'); ?></label></th>
            <td><input class="regular-text" type="url" name="berita_donation_url" id="berita_donation_url" value="<?php echo esc_attr((string) get_user_meta($user->ID, 'berita_donation_url', true)); ?>"></td>
        </tr>
        <tr>
            <th><label for="berita_social_links"><?php esc_html_e('Social Links', 'berita-lite'); ?></label></th>
            <td>
                <textarea class="large-text" rows="4" name="berita_social_links" id="berita_social_links"><?php echo esc_textarea((string) get_user_meta($user->ID, 'berita_social_links', true)); ?></textarea>
                <p class="description"><?php esc_html_e('Satu link per baris', 'berita-lite'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'berita_lite_author_fields');
add_action('edit_user_profile', 'berita_lite_author_fields');

function berita_lite_save_author_fields(int $userId): void
{
    if (!current_user_can('edit_user', $userId)) {
        return;
    }
    update_user_meta($userId, 'berita_verified', isset($_POST['berita_verified']) ? 1 : 0);
    update_user_meta($userId, 'berita_followers', max(0, (int) ($_POST['berita_followers'] ?? 0)));
    update_user_meta($userId, 'berita_donation_url', esc_url_raw((string) ($_POST['berita_donation_url'] ?? '')));
    update_user_meta($userId, 'berita_social_links', sanitize_textarea_field((string) ($_POST['berita_social_links'] ?? '')));
}
add_action('personal_options_update', 'berita_lite_save_author_fields');
add_action('edit_user_profile_update', 'berita_lite_save_author_fields');

function berita_lite_is_verified(int $authorId): bool
{
    return (int) get_user_meta($authorId, 'berita_verified', true) === 1;
}

function berita_lite_verified_badge(int $authorId): string
{
    if (!berita_lite_is_verified($authorId)) {
        return '';
    }
    return '<span class="verified-badge" title="' . esc_attr__('Verified', 'berita-lite') . '"><i class="bi bi-patch-check-fill"></i></span>';
}

function berita_lite_author_social_links(int $authorId): array
{
    $raw = (string) get_user_meta($authorId, 'berita_social_links', true);
    if ($raw === '') {
        return [];
    }
    $links = array_filter(array_map('trim', explode("\n", $raw)));
    return array_values(array_filter(array_map('esc_url', $links)));
}

function berita_lite_render_author_box(int $authorId): void
{
    $author = get_userdata($authorId);
    if (!$author instanceof WP_User) {
        return;
    }
    $followers = (int) get_user_meta($authorId, 'berita_followers', true);
    $links     = berita_lite_author_social_links($authorId);
    ?>
    <section class="author-box" aria-label="<?php esc_attr_e('Author profile', 'berita-lite'); ?>">
        <div>
            <strong><?php echo esc_html($author->display_name); ?></strong> <?php echo wp_kses_post(berita_lite_verified_badge($authorId)); ?>
            <p><?php echo esc_html(get_the_author_meta('description', $authorId)); ?></p>
        </div>
        <div class="entry-meta">
            <span><?php echo esc_html((string) count_user_posts($authorId, 'post')); ?> <?php esc_html_e('posts', 'berita-lite'); ?></span>
            <span><?php echo esc_html((string) $followers); ?> <?php esc_html_e('followers', 'berita-lite'); ?></span>
        </div>
        <?php if ($links !== []) : ?>
            <div class="author-social">
                <?php foreach ($links as $link) : ?>
                    <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-link-45deg"></i><span class="screen-reader-text"><?php esc_html_e('Social link', 'berita-lite'); ?></span></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php
}

