<?php
/**
 * Template: Tutor LMS Single Lesson (default passthrough).
 *
 * Keep Tutor LMS default layout/structure by loading plugin template directly.
 *
 * @package PropTrading101
 */

if ( function_exists( 'tutor' ) ) {
    $default_template = trailingslashit( tutor()->path ) . 'templates/single/lesson.php';
    if ( file_exists( $default_template ) ) {
        include $default_template;
        return;
    }
}

// Fallback if Tutor path changes.
get_header();
if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        tutor_lesson_content();
    endwhile;
endif;
get_footer();
