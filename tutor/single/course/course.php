<?php
/**
 * Template: Tutor LMS Single Course.
 *
 * @package PropTrading101
 */

get_header();
?>

<main id="primary" class="site-main pt101-tutor-page pt101-tutor-page--course">
    <section class="container pt101-tutor-container" aria-label="Course overview">
        <div class="pt101-tutor-shell pt101-tutor-shell--course">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    tutor_course_content();
                endwhile;
            endif;
            ?>
        </div>
    </section>
</main>

<?php
get_footer();
