<?php
$authorId = (int) get_the_author_meta('ID');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="entry-meta">
        <span><?php echo esc_html(get_the_author()); ?></span>
        <?php echo wp_kses_post(berita_lite_verified_badge($authorId)); ?>
        <span><?php echo esc_html(get_the_date()); ?></span>
    </div>
    <div><?php the_excerpt(); ?></div>
</article>

