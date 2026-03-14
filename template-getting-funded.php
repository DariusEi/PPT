<?php
/**
 * Template Name: Getting Funded
 * Template Post Type: page
 */
get_header();
?>

<!-- ═══ HERO ═════════════════════════════════════════════════ -->
<section class="gf-hero">
  <div class="container">
    <div class="gf-hero-grid">

      <div class="gf-hero-left">
        <h1>From learning to earning<br>as a trader</h1>
        <p class="gf-hero-sub">Turn your trading education into real capital. At Prop Trading 101, once you complete one of our programs and earn your certificate, you'll receive a free prop firm challenge. Test your knowledge, pass the challenge, and you'll unlock a funded trading account to start your professional journey.</p>
        <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="btn btn-accent gf-hero-btn">Explore our programs</a>
      </div>

      <div class="gf-hero-right">
        <div class="gf-hero-img-wrap">
          <img src="<?php echo esc_url( PT101_URI . '/images/getting-funded-hero.jpg' ); ?>"
               alt="Trader using the Prop Trading 101 platform on mobile"
               width="1136" height="1080"
               fetchpriority="high"
               class="gf-hero-img">
        </div>
      </div>

    </div>
  </div>
</section>



<!-- ═══ 100% COVERAGE ════════════════════════════════════════ -->
<section class="gf-coverage-section">
  <div class="container">

    <div class="gf-coverage-intro">
      <h2>We cover 100% of the cost for your<br>first evaluation challenge</h2>
      <p>Pass it, and you'll unlock a funded trading account to start your professional journey.</p>
    </div>

    <div class="gf-qualify-box">
      <p class="gf-qualify-tagline">No fees, no gimmicks – just skill, dedication, and a clear path to becoming a funded trader</p>

      <div class="gf-qualify-cols">

        <div class="gf-qualify-col">
          <h3>How to qualify:</h3>
          <ul class="gf-check-list">
            <li>
              <span class="gf-check-dot" aria-hidden="true">
                <svg viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              Complete one full program (intro excluded)
            </li>
            <li>
              <span class="gf-check-dot" aria-hidden="true">
                <svg viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              Achieve 80% or higher on your tests
            </li>
            <li>
              <span class="gf-check-dot" aria-hidden="true">
                <svg viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              Pass the final exam
            </li>
          </ul>
        </div>

        <div class="gf-qualify-col">
          <h3>What's included:</h3>
          <ul class="gf-check-list">
            <li>
              <span class="gf-check-dot" aria-hidden="true">
                <svg viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </span>
              A prop firm challenge that awards a $100K funded account upon passing
            </li>
          </ul>
        </div>

      </div>

      <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="gf-programs-link">
        Choose one of our programs
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M3 7h8M7 3l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
    </div>

  </div>
</section>


<!-- ═══ RISK-FREE ════════════════════════════════════════════ -->
<section class="gf-riskfree-section">
  <div class="container">
    <div class="gf-riskfree-grid">

      <div class="gf-riskfree-left">
        <h2>Begin your trading<br>journey risk-free</h2>
        <p>We've helped thousands of aspiring traders, and we're confident we can help you too. Complete the course, learn something new, and pass our program's tests, and you'll earn a prop firm challenge valued the same as the program. If, for any reason, you're not satisfied, we offer a 7-day money-back guarantee, so you can start risk-free.</p>
      </div>

      <div class="gf-riskfree-right">
        <img src="<?php echo esc_url( PT101_URI . '/images/money-back-badge.png' ); ?>"
             alt="100% money-back guarantee"
             width="880" height="912"
             loading="lazy"
             class="gf-badge-img">
      </div>

    </div>
  </div>
</section>


<!-- ═══ CTA ══════════════════════════════════════════════════ -->
<section class="gf-cta-section">
  <div class="container">
    <h2>Invest in the skills and confidence<br>to start earning!</h2>
    <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="btn btn-accent gf-cta-btn">Choose your program</a>
  </div>
</section>

<?php get_footer(); ?>
