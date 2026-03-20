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
        // Keep a single source of truth for this card to avoid duplicate content blocks.
        if ( function_exists( 'tutor' ) ) {
            $default_template = trailingslashit( tutor()->path ) . 'templates/single/course/lead-info.php';
            if ( file_exists( $default_template ) ) {
                include $default_template;
            }
        }
        ?>
    </div>
</aside>
