<?php
get_header();
?>
<div class="layout">
    <section>
        <?php while (have_posts()) : the_post(); ?>
            <?php $authorId = (int) get_the_author_meta('ID'); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('single'); ?> itemscope itemtype="https://schema.org/NewsArticle">
                <h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>
                <div class="entry-meta">
                    <span itemprop="author"><?php echo esc_html(get_the_author()); ?></span>
                    <?php echo wp_kses_post(berita_lite_verified_badge($authorId)); ?>
                    <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date()); ?></time>
                </div>
                <div class="article-actions" style="margin:12px 0;">
                    <div class="social-share">
                        <button class="btn-icon js-share" data-network="facebook" data-url="<?php the_permalink(); ?>"><i class="bi bi-facebook"></i> <span><?php esc_html_e('Share', 'berita-lite'); ?></span></button>
                        <button class="btn-icon js-share" data-network="x" data-url="<?php the_permalink(); ?>"><i class="bi bi-twitter-x"></i></button>
                        <button class="btn-icon js-share" data-network="whatsapp" data-url="<?php the_permalink(); ?>"><i class="bi bi-whatsapp"></i></button>
                    </div>
                    <button class="btn-icon js-post-like" data-post="<?php the_ID(); ?>"><i class="bi bi-hand-thumbs-up"></i> <span class="js-like-count"><?php echo esc_html((string) (int) get_post_meta(get_the_ID(), 'berita_likes', true)); ?></span></button>
                    <?php $donation = (string) get_user_meta($authorId, 'berita_donation_url', true); ?>
                    <?php if ($donation !== '') : ?>
                        <a class="btn-icon" href="<?php echo esc_url($donation); ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-heart-fill"></i> <?php esc_html_e('Donasi', 'berita-lite'); ?></a>
                    <?php endif; ?>
                </div>
                <div itemprop="articleBody"><?php the_content(); ?></div>
            </article>
            <?php berita_lite_render_author_box($authorId); ?>
            <?php comments_template(); ?>
        <?php endwhile; ?>
    </section>
    <?php get_sidebar(); ?>
</div>
<?php
get_footer();

