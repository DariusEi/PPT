<?php
/**
 * Template: Single Lesson page.
 *
 * Tutor LMS looks for: theme/tutor/single/lesson.php
 * The lesson page uses the course player layout (sidebar + content).
 * We wrap it in the theme shell for header/footer consistency.
 *
 * @package PropTrading101
 */

get_header();
?>

<main id="primary" class="site-main pt101-tutor-page pt101-tutor-lesson">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            // Render the Tutor LMS lesson content.
            // Tutor hooks into the_content filter to inject its lesson player UI.
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php
get_footer();
