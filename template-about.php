<?php
/**
 * Template Name: About Us
 * Template Post Type: page
 */
get_header();
?>

<!-- ═══ HERO (centred, dark bg, trust signals) ══════════════════ -->
<section class="au-hero">
  <div class="au-hero-bg-grid" aria-hidden="true"></div>
  <div class="au-hero-glow" aria-hidden="true"></div>
  <div class="container">
    <div class="au-hero-center">

      <p class="au-eyebrow">About Prop Trading 101</p>
      <h1>We built the school<br>traders actually need</h1>
      <p class="au-hero-sub">We're a team of professional traders who spent years inside prop firms and trading desks — frustrated that no education platform was built around how real markets actually work. We changed that.</p>
      <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="btn btn-accent au-hero-btn">Explore our programs</a>

      <div class="au-hero-trust">
        <div class="au-trust-stars" aria-label="4.9 out of 5 stars">
          <?php for ( $i = 0; $i < 5; $i++ ) : ?>
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 1.5l2.47 5.01 5.53.81-4 3.9.94 5.49L10 14.27l-4.94 2.44.94-5.49-4-3.9 5.53-.81L10 1.5z" fill="#7c6ef5"/></svg>
          <?php endfor; ?>
        </div>
        <p class="au-trust-label">4.9/5 by 10,000+ traders across 140 countries</p>
      </div>

      <div class="au-hero-stats">
        <img src="<?php echo esc_url( PT101_URI . '/images/Sellingpoint2.webp' ); ?>"
             alt="10,000+ Students worldwide · 140+ Countries served · 4.9/5 Trustpilot · $9M+ Funded trading accounts"
             width="1994" height="178"
             loading="lazy"
             class="au-hero-stats-img">
      </div>

    </div>
  </div>
</section>


<!-- ═══ OUR STORY ════════════════════════════════════════════ -->
<section class="au-story-section">
  <div class="container">
    <div class="au-story-grid">

      <div class="au-story-text">
        <h2>Our story</h2>
        <p>We've spent years inside prop firms, trading desks, and trading communities. We saw the same problem everywhere: traders spending thousands on courses that taught theory, not practice. Strategies that worked in backtests but fell apart in live markets.</p>
        <p>We built Prop Trading 101 to fix that. Every lesson in our curriculum was designed around what professional traders actually need — real market experience, structured mentorship, and a clear path to getting funded.</p>
        <p>Today, our graduates have earned <strong>$9M+ in funded trading accounts</strong> across 140 countries.</p>
      </div>

      <div class="au-story-img-wrap">
        <img src="<?php echo esc_url( PT101_URI . '/images/mentor-photo.png' ); ?>"
             alt="Prop Trading 101 mentor at a trading desk"
             width="600" height="446"
             loading="lazy">
      </div>

    </div>
  </div>
</section>


<!-- ═══ WHERE WE ARE NOW (stats + achievements) ══════════════ -->
<section class="au-numbers-section">
  <div class="container">
    <div class="au-numbers-grid">

      <div class="au-numbers-left">
        <h2>Where we are now</h2>
        <div class="au-numbers-list">
          <div class="au-number-item">
            <span class="au-number-val">10,000+</span>
            <span class="au-number-lbl">Traders educated worldwide</span>
          </div>
          <div class="au-number-item">
            <span class="au-number-val">140+</span>
            <span class="au-number-lbl">Countries represented</span>
          </div>
          <div class="au-number-item">
            <span class="au-number-val">$9M+</span>
            <span class="au-number-lbl">In funded trading accounts earned</span>
          </div>
        </div>
      </div>

      <div class="au-numbers-right">
        <div class="au-achievements-card">
          <p class="au-achieve-label">Platform recognition</p>
          <div class="au-achieve-row">
            <div class="au-achieve-item">
              <span class="au-achieve-num">4.9<span class="au-achieve-unit">/5</span></span>
              <span class="au-achieve-sub">Trustpilot rating</span>
            </div>
            <div class="au-achieve-item">
              <span class="au-achieve-num">89%</span>
              <span class="au-achieve-sub">Funding success rate</span>
            </div>
          </div>
          <div class="au-achieve-divider"></div>
          <div class="au-achieve-row">
            <div class="au-achieve-item">
              <span class="au-achieve-num">100+</span>
              <span class="au-achieve-sub">Professional mentors</span>
            </div>
            <div class="au-achieve-item">
              <span class="au-achieve-num">$100K</span>
              <span class="au-achieve-sub">Funded account on offer</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ═══ A NEW KIND OF TRADING EDUCATION (dark bg, 2×3 grid) ══ -->
<section class="au-why-section">
  <div class="container">

    <div class="au-why-intro">
      <h2>A new kind of trading education</h2>
      <p>Not theory-first. Not content-first. Built entirely around one outcome: getting you funded.</p>
    </div>

    <div class="au-why-grid">

      <div class="au-why-item">
        <div class="au-why-icon" aria-hidden="true">
          <img src="<?php echo esc_url( PT101_URI . '/fa_suitcase.svg' ); ?>" width="22" height="22" alt="">
        </div>
        <h3>Built by prop firm traders</h3>
        <p>Our curriculum was designed by professionals who worked at the firms you want to join — so every lesson maps directly to what they look for in funded traders.</p>
      </div>

      <div class="au-why-item">
        <div class="au-why-icon" aria-hidden="true">
          <img src="<?php echo esc_url( PT101_URI . '/ri_stairs-fill.svg' ); ?>" width="22" height="22" alt="">
        </div>
        <h3>Real mentors, real accounts</h3>
        <p>Every mentor has a live trading track record. Not influencers — professionals with funded accounts and years of real market experience who work directly with you.</p>
      </div>

      <div class="au-why-item">
        <div class="au-why-icon" aria-hidden="true">
          <img src="<?php echo esc_url( PT101_URI . '/streamline-flex_decent-work-and-economic-growth-solid.svg' ); ?>" width="22" height="22" alt="">
        </div>
        <h3>Learn → Certify → Get funded</h3>
        <p>We're the only platform that gives qualifying graduates a free prop firm challenge. Your education ends with a funded trading account, not just a certificate.</p>
      </div>

      <div class="au-why-item">
        <div class="au-why-icon" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true"><rect x="3" y="9" width="3" height="9" rx="1" fill="currentColor"/><rect x="9.5" y="4" width="3" height="14" rx="1" fill="currentColor"/><rect x="16" y="7" width="3" height="11" rx="1" fill="currentColor"/></svg>
        </div>
        <h3>Practice-based curriculum</h3>
        <p>You learn by doing — working with real charts, live market scenarios, and trade reviews, not just watching videos. Skills that hold up when money is on the line.</p>
      </div>

      <div class="au-why-item">
        <div class="au-why-icon" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M11 7v4.5l3 2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h3>Study at your own pace</h3>
        <p>Access all content on your schedule. No deadlines, no fixed cohorts. Learn from anywhere while building the skills and discipline that funded trading demands.</p>
      </div>

      <div class="au-why-item">
        <div class="au-why-icon" aria-hidden="true">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true"><circle cx="8" cy="7.5" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M2 19c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="16.5" cy="8.5" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M20 19c0-2.761-1.567-5-3.5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <h3>10,000+ trader community</h3>
        <p>Join a global network of traders at every level. Share ideas, get feedback, and stay accountable with peers who are on the same journey toward funded trading.</p>
      </div>

    </div>

  </div>
</section>


<!-- ═══ FROM DAY ONE TO FUNDED ════════════════════════════════ -->
<section class="au-process-section">
  <div class="container">

    <h2 class="au-process-heading">From day one to funded trader</h2>

    <div class="au-process-steps">

      <div class="au-step">
        <div class="au-step-num" aria-hidden="true">01</div>
        <h3>Choose a program</h3>
        <p>Pick the program that matches your current level — from Trading Foundations through to Mastering Professional Trading.</p>
      </div>

      <div class="au-step-arrow" aria-hidden="true">
        <svg width="32" height="16" viewBox="0 0 32 16" fill="none"><path d="M0 8h28M22 2l8 6-8 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>

      <div class="au-step">
        <div class="au-step-num" aria-hidden="true">02</div>
        <h3>Learn, practise and get certified</h3>
        <p>Complete the curriculum, pass your assessments with 80%+, and earn an industry-recognised trader certification.</p>
      </div>

      <div class="au-step-arrow" aria-hidden="true">
        <svg width="32" height="16" viewBox="0 0 32 16" fill="none"><path d="M0 8h28M22 2l8 6-8 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>

      <div class="au-step">
        <div class="au-step-num" aria-hidden="true">03</div>
        <h3>Get funded for free</h3>
        <p>Qualifying graduates receive a free prop firm challenge. Pass it and you'll unlock a $100K funded trading account.</p>
      </div>

    </div>

  </div>
</section>


<!-- ═══ MENTORS CTA ════════════════════════════════════════════ -->
<section class="au-team-section">
  <div class="container">
    <div class="au-team-inner">
      <div class="au-team-text">
        <h2>Mentors who've<br>been where you are</h2>
        <p>Our mentors are professional traders with real funded accounts and years of live market experience. They teach the way they wish they had been taught — practical, honest, and results-focused.</p>
        <a href="<?php echo esc_url( home_url( '/mentors' ) ); ?>" class="arrow-cta">
          Become a mentor &rarr;
        </a>
      </div>
      <div class="au-team-stats">
        <div class="au-team-stat">
          <span class="au-stat-num">100+</span>
          <span class="au-stat-lbl">Trading mentors</span>
        </div>
        <div class="au-team-stat">
          <span class="au-stat-num">4.9<span class="au-stat-unit">/5</span></span>
          <span class="au-stat-lbl">Trustpilot rating</span>
        </div>
        <div class="au-team-stat">
          <span class="au-stat-num">89%</span>
          <span class="au-stat-lbl">Funding rate</span>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══ CTA ══════════════════════════════════════════════════ -->
<section class="au-cta-section">
  <div class="container">
    <h2>Invest in the skills and confidence<br>to start earning!</h2>
    <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="btn btn-accent au-cta-btn">Choose your program</a>
  </div>
</section>

<?php get_footer(); ?>
