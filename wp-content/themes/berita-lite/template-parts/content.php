<?php
$authorId = (int) get_the_author_meta('ID');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <a href="<?php the_permalink(); ?>" class="post-card__thumb">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium_large'); ?>
        <?php else : ?>
            <div class="post-card__placeholder"></div>
        <?php endif; ?>
    </a>
    <div class="post-card__body">
        <span class="entry-cat"><?php echo esc_html(get_the_category()[0]->name ?? __('Berita', 'berita-lite')); ?></span>
        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="entry-meta">
            <span><?php echo esc_html(get_the_author()); ?></span>
            <?php echo wp_kses_post(berita_lite_verified_badge($authorId)); ?>
            <span><?php echo esc_html(get_the_date()); ?></span>
        </div>
        <div><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></div>
    </div>
</article>
