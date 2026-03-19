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
$is_enrolled = tutor_utils()->is_enrolled( $course_id );
?>

<div class="pt101-course-sidebar-card">
    <?php
    // Render Tutor's default lead info (price, enroll button, course info)
    tutor_course_lead_info();
    ?>
</div>
