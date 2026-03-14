<?php
/**
 * Template Name: Contact Us
 * Template Post Type: page
 */
get_header();
?>

<!-- ═══ HERO ══════════════════════════════════════════════════ -->
<section class="ct-hero">
  <div class="ct-hero-glow" aria-hidden="true"></div>
  <div class="container">
    <div class="ct-hero-center">
      <p class="ct-eyebrow">Contact Us</p>
      <h1>Get in touch</h1>
      <p class="ct-hero-sub">Have a question about our programs, mentorship, or getting funded? We'd love to hear from you — we typically reply within one business day.</p>
    </div>
  </div>
</section>


<!-- ═══ FORM + INFO ══════════════════════════════════════════ -->
<section class="ct-body-section">
  <div class="container">
    <div class="ct-grid">

      <!-- Left: contact info -->
      <div class="ct-info">

        <div class="ct-info-item">
          <div class="ct-info-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M2.5 5.5A1.5 1.5 0 014 4h12a1.5 1.5 0 011.5 1.5v9A1.5 1.5 0 0116 16H4a1.5 1.5 0 01-1.5-1.5v-9z" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 5.5l7.5 5.5 7.5-5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          </div>
          <div>
            <p class="ct-info-label">Email us</p>
            <p class="ct-info-val">support@proptrading101.com</p>
          </div>
        </div>

        <div class="ct-info-item">
          <div class="ct-info-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4.5l2.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div>
            <p class="ct-info-label">Response time</p>
            <p class="ct-info-val">Within one business day</p>
          </div>
        </div>

        <div class="ct-info-item">
          <div class="ct-info-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="8" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M4 17c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          </div>
          <div>
            <p class="ct-info-label">Become a mentor</p>
            <p class="ct-info-val">Interested in teaching? Mention it in your message.</p>
          </div>
        </div>

        <div class="ct-info-divider"></div>

        <p class="ct-info-note">You can also reach us through our <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>">programs page</a> or explore our <a href="<?php echo esc_url( home_url( '/getting-funded' ) ); ?>">getting funded</a> guide for common questions.</p>

      </div>

      <!-- Right: CF7 form -->
      <div class="ct-form-wrap">
        <div class="ct-form-card">
          <?php echo do_shortcode( '[contact-form-7 id="5ab4281" title="Contact us"]' ); ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php get_footer(); ?>
