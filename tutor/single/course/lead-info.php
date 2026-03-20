<?php
/**
 * Template: Tutor LMS Course Lead Info.
 *
 * @package PropTrading101
 */
?>

<aside class="pt101-course-sidebar-card" aria-label="Course enrollment">
    <div class="pt101-course-sidebar-card__inner">
        <?php
        // Load Tutor's original template directly to avoid helper recursion loops.
        if ( function_exists( 'tutor' ) ) {
            $default_template = trailingslashit( tutor()->path ) . 'templates/single/course/lead-info.php';
            if ( file_exists( $default_template ) ) {
                include $default_template;
            }
        }
        ?>
    </div>
</aside>
