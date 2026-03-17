<?php
/**
 * Template Name: Course – Trading Foundations
 * Template Post Type: page
 */
get_header();
?>

<!-- ═══ COURSE HERO (light variant) ══════════════════════════ -->
<section class="cdp-hero cdp-hero--light">
  <div class="container">
    <div class="cdp-hero-grid">

      <div class="cdp-hero-left">
        <nav class="cdp-breadcrumb" aria-label="Breadcrumb">
          <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>">Programs</a>
          <span aria-hidden="true">›</span>
          <span>Trading foundations</span>
        </nav>

        <h1>Trading foundations</h1>

        <p class="cdp-hero-sub">Build your trading foundations covering essential terminology and the basics of trading</p>

        <ul class="cdp-checks">
          <li>Flexible</li>
          <li>2 – 4 weeks</li>
          <li>Your own pace</li>
          <li>Mentor support</li>
        </ul>

        <a href="<?php echo esc_url( pt101_enroll_url( 256 ) ); ?>" class="btn btn-accent cdp-enroll-btn">Enroll now for $89.00</a>

        <div class="cdp-rating">
          <span class="cdp-stars" aria-label="4.9 out of 5 stars" role="img">★★★★★</span>
          <div class="cdp-trustpilot">
            <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="#00b67a"/></svg>
            <span>Trustpilot</span>
          </div>
          <span class="cdp-rating-text">4.9/5 by 1000+ students</span>
        </div>
      </div>

      <div class="cdp-hero-right">
        <div class="cdp-stat-card">
          <span class="cdp-stat-big">89.7%</span>
          <span class="cdp-stat-label">Become funded traders</span>
          <ul class="cdp-stat-list">
            <li>Education for trading careers</li>
            <li>Mentorship from top-tier professionals</li>
            <li>Apply what you learn in projects</li>
            <li>No prior experience required</li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ═══ FIRMS MARQUEE ════════════════════════════════════════ -->
<div class="firms-bar" aria-label="Partner trading firms">
  <span class="firms-bar-label">Learn from 100+ trading mentors from top firms</span>
  <div class="firms-track">
    <?php
    $firms = [ 'FTMO', 'FundedNext', 'InteractiveBrokers', 'The 5%ers', 'TradingView', 'FUNDEDNEXT', 'cTrader' ];
    foreach ( array_merge( $firms, $firms, $firms ) as $f ) :
      echo '<span class="firm-name">' . esc_html( $f ) . '</span>';
    endforeach;
    ?>
  </div>
</div>


<!-- ═══ FEATURED QUOTE ════════════════════════════════════════ -->
<section class="cdp-quote-section">
  <div class="container">
    <blockquote class="cdp-blockquote">
      <p>&ldquo;Finally, a course that explains trading without making it feel impossible. The fundamentals clicked, I understood the key terms, and I actually felt ready to trade.&rdquo;</p>
    </blockquote>
    <div class="cdp-quote-attr">
      <strong class="cdp-quote-name">James Smith</strong>
      <span class="cdp-quote-role">Funded trader</span>
      <span class="cdp-quote-firm">
        <svg width="14" height="14" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1l2.39 4.84 5.34.78-3.86 3.77.91 5.31L10 13.27l-4.78 2.43.91-5.31L2.27 6.62l5.34-.78z" fill="#f05a28"/></svg>
        FTMO
      </span>
    </div>
  </div>
</section>


<!-- ═══ TAKE THE NEXT STEP ════════════════════════════════════ -->
<section class="cdp-next-section">
  <div class="container">
    <div class="cdp-next-grid">
      <h2>Take the next step in your trading career</h2>
      <p>Our goal is to provide you with the knowledge, hands-on practice, and confidence to start trading professionally.</p>
    </div>
  </div>
</section>


<!-- ═══ PROGRAM OUTLINE ═══════════════════════════════════════ -->
<section class="cdp-outline-section" id="curriculum">
  <div class="container">

    <h2 class="cdp-outline-heading">Program outline</h2>
    <p class="cdp-outline-sub">Our program, built by industry experts, focuses on helping you become market-ready and start trading with confidence right after completion.</p>

    <div class="cdp-modules" id="cdp-modules">
      <?php
      $modules = [
        [
          'num'   => 'Module 1',
          'title' => 'What is forex?',
          'body'  => 'The module introduces the fundamentals of Forex trading. You\'ll start with the definition of Forex (FX) and the foreign exchange market. Next, you\'ll learn why Forex is traded, including its liquidity, 24/5 market accessibility, and efficiency. Finally, the course covers the major market participants, including banks, brokers, and traders, and their roles in the FX market.',
          'open'  => true,
        ],
        [
          'num'   => 'Module 2',
          'title' => 'Essential terminology',
          'body'  => 'Master the core vocabulary every trader needs. You\'ll cover pips, lots, spreads, leverage, margin, and slippage — understanding not just the definitions but how each concept directly affects your trades. A strong grasp of terminology gives you the foundation to learn faster and communicate with confidence in any trading environment.',
          'open'  => false,
        ],
        [
          'num'   => 'Module 3',
          'title' => 'Getting started',
          'body'  => 'Learn how to choose a broker, set up your trading account, and navigate a trading platform step by step. You\'ll place your first simulated trades, understand order types (Market, Limit, Stop), and build the practical habits that separate disciplined traders from beginners.',
          'open'  => false,
        ],
        [
          'num'   => 'Module 4',
          'title' => 'Reading charts and price action',
          'body'  => 'Charts are the language of trading. This module walks you through candlestick patterns, chart types, and timeframes. You\'ll learn to spot trends, draw support and resistance levels, and interpret price action — giving you the tools to analyse any market with clarity.',
          'open'  => false,
        ],
        [
          'num'   => 'Module 5',
          'title' => 'Risk management basics',
          'body'  => 'Protecting your capital is the single most important skill in trading. You\'ll learn position sizing, how to set stop-loss and take-profit levels, and the risk-to-reward ratios that keep you profitable over the long term. This module sets the mindset and habits every serious trader must build from day one.',
          'open'  => false,
        ],
      ];
      foreach ( $modules as $mod ) :
        $open_class = $mod['open'] ? ' open' : '';
      ?>
      <div class="cdp-module<?php echo $open_class; ?>">
        <button class="cdp-module-btn" aria-expanded="<?php echo $mod['open'] ? 'true' : 'false'; ?>" type="button">
          <span class="cdp-module-num"><?php echo esc_html( $mod['num'] ); ?></span>
          <span class="cdp-module-title"><?php echo esc_html( $mod['title'] ); ?></span>
          <span class="cdp-module-arrow" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
        </button>
        <div class="cdp-module-body">
          <p><?php echo esc_html( $mod['body'] ); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="cdp-curriculum-row">
      <button class="cdp-curriculum-btn" id="cdp-curriculum-toggle" type="button">
        View full curriculum <span aria-hidden="true">→</span>
      </button>
    </div>

  </div>
</section>


<!-- ═══ TECH-DRIVEN APPROACH ══════════════════════════════════ -->
<section class="cdp-tech-section">
  <div class="container">
    <div class="cdp-tech-inner">
      <h2>A unique tech-driven approach to learning professional trading</h2>
      <p>Learning to trade online can be tough when you&rsquo;re on your own. That&rsquo;s why we built our platform that combines expert mentor support with structured learning in sprints. We&rsquo;re also adding adaptive AI features to personalise your journey, so it adjusts to your pace, needs, and progress as you work toward becoming market-ready.</p>
      <a href="<?php echo esc_url( home_url( '/how-it-works' ) ); ?>" class="cdp-arrow-cta">How it works &rarr;</a>
    </div>
  </div>
</section>


<!-- ═══ OUR STUDENTS WORK WITH ════════════════════════════════ -->
<section class="cdp-logos-section" aria-label="Our students work with">
  <div class="container">
    <h2>Our students work with</h2>
  </div>
  <div class="logos-belt">
    <div class="logos-belt-track">
      <?php
      $logos = [ 'FTMO', 'FUNDEDNEXT', 'cTrader', 'Reckoner', 'FTMO', 'Interactive Brokers', 'The 5%ers', 'TradingView' ];
      foreach ( array_merge( $logos, $logos ) as $l ) :
      ?>
        <div class="logo-chip cdp-logo-chip"><span class="logo-chip-dot" aria-hidden="true"></span><?php echo esc_html( $l ); ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ═══ TESTIMONIAL ═══════════════════════════════════════════ -->
<section class="testi-section" aria-label="Student testimonials">
  <div class="testi-wrap">

    <div class="testi-carousel" id="testi-carousel">
      <div class="testi-card" data-slide="0" style="display:block">
        <div class="testi-body-row">

          <div class="testi-avatar-wrap">
            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=480&h=480&fit=crop&crop=face&q=80"
                 alt="James Smith" loading="lazy">
            <div class="testi-avatar-label">
              <span class="tav-name">James Reith</span>
              <span class="tav-role">Trade Analyst</span>
            </div>
          </div>

          <div class="testi-quote-block">
            <div class="testi-stars" aria-hidden="true">
              <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
            </div>
            <p class="testi-quote">The programs exceeded my expectations. The course material was well-structured and engaging, and I always had access to mentors and fellow learners. The peer and senior review process made learning practical and effective.</p>
            <div class="testi-author">
              <div class="testi-name">James Smith</div>
              <div class="testi-role-tag">Trade Desk Analyst</div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Metrics row -->
    <div class="testi-metrics-row">
      <div class="testi-metric-cell testi-metric-first">
        <span class="tm-num" data-count="89" data-suffix="%*">89%*</span>
        <span class="tm-lbl">Funding rate</span>
      </div>
      <div class="testi-metric-cell">
        <span class="tm-num" data-count="140" data-suffix="+">140+</span>
        <span class="tm-lbl">Countries served</span>
      </div>
      <div class="testi-metric-cell">
        <span class="tm-num" data-count="10000" data-suffix="+">10,000+</span>
        <span class="tm-lbl">Active students</span>
      </div>
    </div>
    <p class="testi-foot">*Students who passed a prop firm&rsquo;s trading challenge and earned a funded account within 6 months of graduation.</p>

  </div>
</section>


<!-- ═══ CTA ════════════════════════════════════════════════════ -->
<section class="cta-section" id="enroll">
  <h2>Invest in the skills and confidence<br>to start earning!</h2>
  <a href="<?php echo esc_url( pt101_enroll_url( 256 ) ); ?>" class="btn btn-accent">Enroll now &rarr;</a>
  <div class="cta-trust-row">
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><path d="M6.5 1l1.3 2.8 3 .4-2.2 2.1.5 3-2.6-1.4L4 9.3l.5-3L2.2 4.2l3-.4z" stroke="#888b9e" stroke-width="1.1" stroke-linejoin="round"/></svg>
      No credit card required
    </span>
    <span class="cta-trust-pipe">|</span>
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><circle cx="6.5" cy="6.5" r="5" stroke="#888b9e" stroke-width="1.1"/><path d="M4.5 6.5l1.5 1.5L8.5 5" stroke="#888b9e" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Cancel anytime
    </span>
    <span class="cta-trust-pipe">|</span>
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><path d="M2 9.5c0-2.2 2-3.5 4.5-3.5S11 7.3 11 9.5" stroke="#888b9e" stroke-width="1.1" stroke-linecap="round"/><circle cx="6.5" cy="4" r="2" stroke="#888b9e" stroke-width="1.1"/></svg>
      10,000+ active students
    </span>
  </div>
</section>

<?php get_footer(); ?>
