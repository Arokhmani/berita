<?php
get_header();
?>
<div class="layout">
    <section>
        <?php
        $featured = new WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => 1,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ]);
        if ($featured->have_posts()) :
            ?>
            <div class="hero-news">
                <?php while ($featured->have_posts()) : $featured->the_post(); ?>
                    <article class="hero-news__main">
                        <a href="<?php the_permalink(); ?>" class="hero-news__thumb">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php else : ?>
                                <div class="hero-news__placeholder"></div>
                            <?php endif; ?>
                        </a>
                        <div class="hero-news__body">
                            <span class="entry-cat"><?php echo esc_html(get_the_category()[0]->name ?? __('Berita', 'berita-lite')); ?></span>
                            <h1 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                            <div class="entry-meta">
                                <span><?php echo esc_html(get_the_author()); ?></span>
                                <span><?php echo esc_html(get_the_date()); ?></span>
                            </div>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 26)); ?></p>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        endif;
        ?>

        <?php
        $headline = new WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => 4,
            'offset'              => 1,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        ]);
        if ($headline->have_posts()) :
            ?>
            <div class="headline-grid">
                <?php while ($headline->have_posts()) : $headline->the_post(); ?>
                    <article class="headline-grid__item">
                        <a href="<?php the_permalink(); ?>" class="headline-grid__thumb">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <div class="hero-news__placeholder"></div>
                            <?php endif; ?>
                        </a>
                        <div class="headline-grid__body">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="entry-meta">
                                <span><?php echo esc_html(get_the_date()); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        endif;
        ?>

        <h2 class="section-title"><?php esc_html_e('Berita Terbaru', 'berita-lite'); ?></h2>
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
