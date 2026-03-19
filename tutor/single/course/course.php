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
            // Render the default Tutor LMS course content
            tutor_course_content();
        endwhile;
    endif;
    ?>
  </div>
</main>

<?php
get_footer();
