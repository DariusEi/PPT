<?php
/**
 * Template: Course lead info / sidebar card.
 *
 * Tutor LMS looks for: theme/tutor/single/course/lead-info.php
 * Wraps the enrollment/pricing card in theme-consistent markup.
 *
 * @package PropTrading101
 */

$course_id = get_the_ID();
$is_enrolled = function_exists( 'tutor_utils' ) ? tutor_utils()->is_enrolled( $course_id ) : false;
?>

<div class="pt101-course-sidebar-card">
    <?php
    // Render Tutor's default lead info (price, enroll button, course info).
    // Uses tutor_load_template to load the plugin's own lead-info template parts.
    if ( function_exists( 'tutor_load_template' ) ) {
        tutor_load_template( 'single.course.lead-info' );
    }
    ?>
</div>
