<?php
/**
 * Template for single course page.
 *
 * Tutor LMS looks for: theme/tutor/single/course/course.php
 * Wraps Tutor output inside the theme layout (header, container, footer)
 * and adds body class hooks for dark-theme CSS targeting.
 *
 * @package PropTrading101
 */

get_header();
?>

<main id="primary" class="site-main pt101-tutor-page">
  <div class="pt101-tutor-container">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            // Render the Tutor LMS course content.
            // Tutor hooks into the_content filter to inject its course UI
            // (tabs, curriculum, sidebar, instructor info, etc.).
            the_content();
        endwhile;
    endif;
    ?>
  </div>
</main>

<?php
get_footer();
