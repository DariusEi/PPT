<?php
/**
 * Template Name: Course – Intro to Trading (Free)
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
          <span>Intro to trading</span>
        </nav>

        <span class="cdp-free-badge" aria-label="Free course">&#10003; Free course</span>

        <h1>Intro to trading</h1>

        <p class="cdp-hero-sub">Kick-start your trading journey with our completely free introductory course — no experience needed, no credit card required.</p>

        <ul class="cdp-checks">
          <li>Free forever</li>
          <li>~2 weeks</li>
          <li>Your own pace</li>
          <li>Community access</li>
        </ul>

        <a href="<?php echo esc_url( pt101_enroll_url( 158 ) ); ?>" class="btn btn-accent cdp-enroll-btn">Enroll for free &rarr;</a>

        <div class="cdp-rating">
          <span class="cdp-stars" aria-label="4.8 out of 5 stars" role="img">★★★★★</span>
          <div class="cdp-trustpilot">
            <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="#00b67a"/></svg>
            <span>Trustpilot</span>
          </div>
          <span class="cdp-rating-text">4.8/5 by 3,000+ students</span>
        </div>
      </div>

      <div class="cdp-hero-right">
        <div class="cdp-stat-card">
          <span class="cdp-stat-big">FREE</span>
          <span class="cdp-stat-label">No credit card required</span>
          <ul class="cdp-stat-list">
            <li>Start your trading journey today</li>
            <li>Beginner-friendly content</li>
            <li>Step up to paid programs anytime</li>
            <li>100% online, learn at your pace</li>
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
      <p>&ldquo;I had zero trading experience before this course. Within two weeks I understood how markets work, how to read a chart, and I felt genuinely confident opening my first demo account. The best free resource I&rsquo;ve found.&rdquo;</p>
    </blockquote>
    <div class="cdp-quote-attr">
      <strong class="cdp-quote-name">Sophie Williams</strong>
      <span class="cdp-quote-role">Student &rarr; now enrolled in Trading Foundations</span>
    </div>
  </div>
</section>


<!-- ═══ START YOUR JOURNEY ════════════════════════════════════ -->
<section class="cdp-next-section">
  <div class="container">
    <div class="cdp-next-grid">
      <h2>Your trading journey starts here</h2>
      <p>We believe everyone deserves access to quality trading education. This free course gives you the essential building blocks to understand markets and take your first confident steps toward trading professionally.</p>
    </div>
  </div>
</section>


<!-- ═══ PROGRAM OUTLINE ═══════════════════════════════════════ -->
<section class="cdp-outline-section" id="curriculum">
  <div class="container">

    <h2 class="cdp-outline-heading">Program outline</h2>
    <p class="cdp-outline-sub">Five focused modules designed to take you from complete beginner to confident learner, ready to progress to our paid programs.</p>

    <div class="cdp-modules" id="cdp-modules">
      <?php
      $modules = [
        [
          'num'   => 'Module 1',
          'title' => 'What is trading?',
          'body'  => 'Get a clear, jargon-free introduction to what trading actually is. You\'ll learn how financial markets work, why people trade, and the difference between investing and active trading. By the end of this module you\'ll understand the big picture — where markets come from, how prices are set, and where you fit into the global financial system.',
          'open'  => true,
        ],
        [
          'num'   => 'Module 2',
          'title' => 'Financial markets overview',
          'body'  => 'Explore the main financial markets — Forex, stocks, commodities, and indices — and learn what makes each one unique. You\'ll discover when markets are open, what drives prices, and which instruments are most suitable for beginner traders. Understanding the landscape of available markets is the first step toward choosing the right one for you.',
          'open'  => false,
        ],
        [
          'num'   => 'Module 3',
          'title' => 'Reading your first chart',
          'body'  => 'Charts are how traders see the market. This module introduces you to price charts, candlestick patterns, and the most common chart types. You\'ll learn how to spot an uptrend and a downtrend, what support and resistance mean, and how professional traders use charts to make decisions — no prior experience required.',
          'open'  => false,
        ],
        [
          'num'   => 'Module 4',
          'title' => 'Key concepts every trader must know',
          'body'  => 'Build your trading vocabulary with the terms you\'ll encounter every day: pips, lots, leverage, margin, spread, and liquidity. Understanding these concepts isn\'t just about definitions — you\'ll learn exactly how each one affects your trades so you\'re never caught off guard when managing a live position.',
          'open'  => false,
        ],
        [
          'num'   => 'Module 5',
          'title' => 'Setting up your first demo account',
          'body'  => 'Put everything into practice. This final module guides you step by step through choosing a platform, setting up a free demo account, and placing your very first trade — with zero financial risk. You\'ll leave this course with hands-on experience and a clear path forward into our paid Trading Foundations program.',
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
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=480&h=480&fit=crop&crop=face&q=80"
                 alt="Sophie Williams" loading="lazy">
            <div class="testi-avatar-label">
              <span class="tav-name">Sophie Williams</span>
              <span class="tav-role">Junior Trader</span>
            </div>
          </div>

          <div class="testi-quote-block">
            <div class="testi-stars" aria-hidden="true">
              <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
            </div>
            <p class="testi-quote">I had zero trading experience before this course. Within two weeks I understood how markets work, how to read a chart, and I felt genuinely confident opening my first demo account. This free course changed everything for me.</p>
            <div class="testi-author">
              <div class="testi-name">Sophie Williams</div>
              <div class="testi-role-tag">Now enrolled in Trading Foundations</div>
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
  <h2>Your trading journey<br>starts today &mdash; for free!</h2>
  <a href="<?php echo esc_url( pt101_enroll_url( 158 ) ); ?>" class="btn btn-accent">Start learning for free</a>
  <div class="cta-trust-row">
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><path d="M6.5 1l1.3 2.8 3 .4-2.2 2.1.5 3-2.6-1.4L4 9.3l.5-3L2.2 4.2l3-.4z" stroke="#888b9e" stroke-width="1.1" stroke-linejoin="round"/></svg>
      100% free, always
    </span>
    <span class="cta-trust-pipe">|</span>
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><circle cx="6.5" cy="6.5" r="5" stroke="#888b9e" stroke-width="1.1"/><path d="M4.5 6.5l1.5 1.5L8.5 5" stroke="#888b9e" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
      No credit card required
    </span>
    <span class="cta-trust-pipe">|</span>
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><path d="M2 9.5c0-2.2 2-3.5 4.5-3.5S11 7.3 11 9.5" stroke="#888b9e" stroke-width="1.1" stroke-linecap="round"/><circle cx="6.5" cy="4" r="2" stroke="#888b9e" stroke-width="1.1"/></svg>
      10,000+ active students
    </span>
  </div>
</section>

<?php get_footer(); ?>
