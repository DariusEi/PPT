<?php
/**
 * Template: Tutor LMS Single Lesson.
 *
 * @package PropTrading101
 */

get_header();
?>

<main id="primary" class="site-main pt101-tutor-page pt101-tutor-page--lesson">
    <section class="container pt101-tutor-container pt101-tutor-container--player" aria-label="Course lesson">
        <div class="pt101-tutor-shell pt101-tutor-shell--lesson">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    tutor_lesson_content();
                endwhile;
            endif;
            ?>
        </div>
    </section>
</main>

<?php
get_footer();
