<?php
get_header();
?>
<div class="layout">
    <section>
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', get_post_type()); ?>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <article class="post-card"><?php esc_html_e('Belum ada konten.', 'berita-lite'); ?></article>
        <?php endif; ?>
    </section>
    <?php get_sidebar(); ?>
</div>
<?php
get_footer();

