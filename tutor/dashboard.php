<?php
/**
 * Template: Tutor LMS Dashboard.
 *
 * Tutor LMS looks for: theme/tutor/dashboard.php
 * Wraps the dashboard inside theme layout with proper header/footer.
 *
 * @package PropTrading101
 */

get_header();
?>

<main id="primary" class="site-main pt101-tutor-page pt101-tutor-dashboard">
    <?php
    // Render Tutor LMS dashboard (sidebar + content)
    tutor_load_template( 'dashboard.dashboard' );
    ?>
</main>

<?php
get_footer();
