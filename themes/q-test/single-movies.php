<?php
get_header();
if (have_posts()) :
    while (have_posts()) :
        the_post();
        ?>
        <section>
            <h2><?php the_title(); ?></h2>
            <div><?php the_content(); ?></div>
        </section>
        <section>
            <h2>Movie Title</h2>
            <div><?php echo get_post_meta(get_the_ID(), 'movie_title', true); ?></div>
        </section>
        <?php
    endwhile;
endif;
get_footer();
