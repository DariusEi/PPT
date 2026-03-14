<?php
/**
 * The main template file — fallback for all pages.
 */
get_header();
?>

<main id="primary" class="site-main">
    <div class="container section-padding">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        else :
            echo '<p>' . esc_html__( 'Nothing found here.', 'prop-trading-101' ) . '</p>';
        endif;
        ?>
    </div>
</main>

<?php get_footer(); ?>
