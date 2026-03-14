<?php
/**
 * Template Name: Mentors Page
 * Template Post Type: page
 */
get_header();
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section class="mtr-hero">
  <div class="container">
    <div class="mtr-hero-grid">

      <div class="mtr-hero-left">
        <h1>Become one of<br>our mentors</h1>
        <p class="mtr-hero-sub">Enjoy weekly payouts and earn up to 20% commission on every sale, including every 1-on-1 mentorship session you do.</p>
        <a href="#apply" class="btn btn-accent mtr-hero-btn">Become a mentor</a>
        <p class="mtr-hero-footnote">Interested in educating your audience? <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">We can help</a></p>
      </div>

      <div class="mtr-hero-right">
        <div class="mtr-illustration-box">
          <img src="<?php echo esc_url( PT101_URI . '/images/mentor-illustration.png' ); ?>"
               alt="Mentor growing trading knowledge illustration"
               width="560" height="420"
               fetchpriority="high">
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ═══ STATS ════════════════════════════════════════════════ -->
<section class="mtr-stats-section">
  <div class="container">
    <h2 class="mtr-stats-heading">Get paid for helping others learn<br>and trade smarter</h2>

    <div class="mtr-stats-strip">
      <img src="<?php echo esc_url( PT101_URI . '/images/partners-strip.png' ); ?>"
           alt="10 000+ Students worldwide · 140+ Countries served · 4.9/5 Trustpilot · $1M+ Funded trading accounts"
           width="1994" height="178"
           loading="lazy">
    </div>
  </div>
</section>


<!-- ═══ FEATURES ═════════════════════════════════════════════ -->
<section class="mtr-features-section">
  <div class="container">

    <!-- Feature row 1: text + photo -->
    <div class="mtr-feature-row">
      <div class="mtr-feature-text">
        <h2>Flexible and fast<br>payouts</h2>
        <p>With our mentorship program, earn 20% commission on every sale you generate. The more your audience learns and grows, the more you earn, rewarding your efforts with every engaged student.</p>
      </div>
      <div class="mtr-feature-photo">
        <img src="<?php echo esc_url( PT101_URI . '/images/mentor-photo.png' ); ?>"
             alt="Mentor celebrating trading success at laptop"
             width="600" height="446"
             loading="lazy">
      </div>
    </div>

    <!-- Feature row 2: two columns -->
    <div class="mtr-feature-2col">
      <div class="mtr-feature-col">
        <h2>1-on-1 mentorship<br>calls</h2>
        <p>Build your reputation and brand as a professional trader by offering short 1-on-1 mentorship calls. Earn more by working directly with your audience. We take a no-nonsense approach to mentor pay: you put in the work, you get rewarded.</p>
      </div>
      <div class="mtr-feature-col">
        <h2>Fair opportunity for all<br>partners</h2>
        <p>We keep it simple and fair: last-click attribution ensures commissions go to the right owner, and all codes are updated for promos with equal value.</p>
      </div>
    </div>

  </div>
</section>


<!-- ═══ CTA ══════════════════════════════════════════════════ -->
<section class="mtr-cta-section" id="apply">
  <div class="container">
    <h2>Invest in the skills and confidence<br>to start earning!</h2>
    <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn-accent mtr-cta-btn">Become a mentor</a>
  </div>
</section>

<?php get_footer(); ?>
