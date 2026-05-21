<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<aside>
    <?php if (is_single()) : ?>
        <?php $authorId = (int) get_the_author_meta('ID'); ?>
        <button class="btn-icon js-author-modal-open" type="button"><i class="bi bi-person-badge"></i> <?php esc_html_e('Profil Penulis', 'berita-lite'); ?></button>
        <div class="modal js-author-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Author profile', 'berita-lite'); ?>">
            <div class="modal__dialog">
                <button type="button" class="btn-icon js-author-modal-close"><i class="bi bi-x-lg"></i></button>
                <?php berita_lite_render_author_box($authorId); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_active_sidebar('sidebar-article')) : ?>
        <?php dynamic_sidebar('sidebar-article'); ?>
    <?php endif; ?>
</aside>

