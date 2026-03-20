<?php
/**
 * Front Page Template — v5
 * Aligned to Figma design.
 */
get_header();
?>

<main id="primary">

<!-- ═══ HERO ═══════════════════════════════════════════════ -->
<section class="hero" id="home">
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="hero-glow" aria-hidden="true"></div>
  <div class="hero-content">

    <h1><?php echo esc_html( get_theme_mod( 'pt101_hero_line1', 'From learning' ) ); ?> <?php echo esc_html( get_theme_mod( 'pt101_hero_line2', 'to earning.' ) ); ?><br><?php echo esc_html( get_theme_mod( 'pt101_hero_line3', 'Get market-ready.' ) ); ?></h1>

    <?php $hero_sub = get_theme_mod( 'pt101_hero_sub', '' ); if ( $hero_sub ): ?>
    <p class="hero-sub"><?php echo esc_html( $hero_sub ); ?></p>
    <?php endif; ?>

    <div class="hero-buttons">
      <a href="<?php echo esc_url( get_theme_mod( 'pt101_hero_btn_url', '/programs' ) ); ?>" class="btn btn-accent">
        <?php echo esc_html( get_theme_mod( 'pt101_hero_btn_text', 'Choose your program' ) ); ?>
      </a>
      <a href="#features" class="btn btn-ghost">See how it works &rarr;</a>
    </div>

    <div class="hero-stats-svg">
      <img src="<?php echo esc_url( get_template_directory_uri() . '/Selling_point_1.svg' ); ?>" alt="10,000+ students worldwide · 140+ countries · 4.9/5 Trustpilot · $9M+ funded trading accounts" class="hero-stats-img" loading="eager" fetchpriority="high">
    </div>

  </div>
</section>


<!-- ═══ STUDENT PHOTOS ════════════════════════════════════ -->
<section class="students-section" aria-label="Our students">
  <span class="students-label">Our students</span>
  <div class="students-grid">
    <?php
    $students = pt101_testimonials( 4 );
    $i = 1;
    if ( $students->have_posts() ):
      while ( $students->have_posts() ): $students->the_post();
        $role = get_post_meta( get_the_ID(), 'author_role', true ) ?: 'Funded Trader';
    ?>
      <div class="student-card">
        <?php if ( has_post_thumbnail() ): the_post_thumbnail( 'large', [ 'alt' => get_the_title() ] );
        else: ?><img src="<?php echo esc_url( pt101_placeholder( $i ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy"><?php endif; ?>
        <div class="student-overlay">
          <span class="so-name"><?php echo esc_html( get_the_title() ); ?></span>
          <span class="so-role"><?php echo esc_html( $role ); ?></span>
        </div>
      </div>
    <?php $i++; endwhile; wp_reset_postdata();
    else:
      foreach ( [
        [ 1, 'James Smith',    'Funded Trader'     ],
        [ 2, 'Sarah Chen',     'Trade Analyst'     ],
        [ 3, 'Marcus Williams','Funded Trader'     ],
        [ 4, 'Priya Mehta',    'Self-funded Trader'],
      ] as [ $idx, $name, $role ] ):
    ?>
      <div class="student-card">
        <img src="<?php echo esc_url( pt101_placeholder( $idx ) ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
        <div class="student-overlay">
          <span class="so-name"><?php echo esc_html( $name ); ?></span>
          <span class="so-role"><?php echo esc_html( $role ); ?></span>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</section>


<!-- ═══ FIRMS MARQUEE ═════════════════════════════════════ -->
<div class="firms-bar" aria-label="Partner trading firms">
  <span class="firms-bar-label">Learn from 100+ trading mentors from top prop firms</span>
  <div class="firms-track">
    <?php
    $firms = [ 'FTMO', 'FundedNext', 'InteractiveBrokers', 'The 5%ers', 'TradingView', 'FUNDEDNEXT', 'cTrader' ];
    foreach ( array_merge( $firms, $firms, $firms ) as $f ):
      echo '<span class="firm-name">' . esc_html( $f ) . '</span>';
    endforeach;
    ?>
  </div>
</div>


<!-- ═══ FEATURES ══════════════════════════════════════════ -->
<section class="features-section" id="features">
  <div class="container">
    <div class="features-grid">

      <div class="features-left">
        <h2><?php echo esc_html( get_theme_mod( 'pt101_feat_heading', 'Your launchpad for a confident career in professional trading' ) ); ?></h2>

        <?php
        $icon_dir = get_template_directory_uri();
        $items = [
          [
            'icon'  => '<img src="' . esc_url( $icon_dir . '/fa_suitcase.svg' ) . '" width="15" height="15" alt="" aria-hidden="true">',
            'title' => 'Get market-ready',
            'body'  => 'We give you the in-demand trading skills, hands-on experience, and expert mentorship to succeed as a professional trader. Our results speak for themselves.',
          ],
          [
            'icon'  => '<img src="' . esc_url( $icon_dir . '/ri_stairs-fill.svg' ) . '" width="15" height="15" alt="" aria-hidden="true">',
            'title' => 'Receive your trader certification',
            'body'  => 'Earn an industry-recognized certification that validates your trading expertise and opens doors to funded opportunities with leading prop firms worldwide.',
          ],
          [
            'icon'  => '<img src="' . esc_url( $icon_dir . '/streamline-flex_decent-work-and-economic-growth-solid.svg' ) . '" width="15" height="15" alt="" aria-hidden="true">',
            'title' => 'Start earning after learning',
            'body'  => 'Turn your trading education into real capital. Once you complete one of our advanced programs and earn your certificate, you\'ll receive a free prop firm challenge. Test your knowledge, pass the challenge, and you\'ll unlock a funded trading account to start your professional journey.',
          ],
        ];
        foreach ( $items as $k => $item ):
          $open = ( $k === 0 ) ? 'open' : '';
        ?>
        <div class="acc-item <?php echo esc_attr( $open ); ?>">
          <button class="acc-btn" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
            <span class="acc-ico" aria-hidden="true"><?php echo $item['icon']; ?></span>
            <span class="acc-title"><?php echo esc_html( $item['title'] ); ?></span>
            <span class="acc-arrow" aria-hidden="true">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6L8 10L12 6" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
          </button>
          <div class="acc-body">
            <p><?php echo esc_html( $item['body'] ); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div>
        <div class="feat-card">
          <div class="feat-card-num"><?php echo esc_html( get_theme_mod( 'pt101_funded_pct', '89.7%' ) ); ?></div>
          <div class="feat-card-sub">Become funded traders</div>
          <div class="feat-divider"></div>
          <div class="check-list">
            <?php foreach ( [
              'Education for trading careers',
              'Mentorship from top-tier professionals',
              'Apply what you learn in projects',
            ] as $c ): ?>
            <div class="check-row">
              <span class="check-dot" aria-hidden="true">
                <svg viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.2"><polyline points="2 6 5 9 10 3"/></svg>
              </span>
              <span class="check-text"><?php echo esc_html( $c ); ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ═══ SCHEDULE ══════════════════════════════════════════ -->
<section class="schedule-section" id="schedule">
  <div class="container">
    <div class="schedule-grid">

      <div class="schedule-content">
        <h2>Tailored lessons,<br>on your schedule</h2>
        <p>We don't do lectures. Our programs are designed for traders who want the flexibility to fit learning around their lives — not the other way around. You'll gain hands-on experience by simulating the work of a trading firm. With 1-on-1 guidance from our senior mentors, you'll build real skills and confidence that showcase your market expertise.</p>
        <a href="#programs" class="arrow-cta">
          How it works &rarr;
        </a>
      </div>

      <div>
        <div class="schedule-img-wrap">
          <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=600&fit=crop&q=80" alt="Students learning together" loading="lazy">
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ═══ WORK WITH ══════════════════════════════════════════ -->
<section class="work-section" id="partners">
  <div class="container">
    <h2>Our students work with</h2>
  </div>
  <div class="logos-belt">
    <div class="logos-belt-track">
      <?php
      $logos = [ 'FTMO', 'FUNDEDNEXT', 'cTrader', 'Reckoner', 'FTMO', 'Interactive Brokers', 'The 5%ers', 'TradingView' ];
      foreach ( array_merge( $logos, $logos ) as $l ):
      ?>
        <div class="logo-chip"><span class="logo-chip-dot" aria-hidden="true"></span><?php echo esc_html( $l ); ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ═══ TESTIMONIAL ════════════════════════════════════════ -->
<section class="testi-section" aria-label="Student testimonials">
  <div class="testi-wrap">

    <?php
    $all_testis = [
      [
        'name'  => 'James Smith',
        'role'  => 'Trade Desk Analyst',
        'quote' => 'The programs exceeded my expectations. The course material was well-structured and engaging, and I always had access to mentors and fellow learners. The peer and senior review process made learning practical and effective.',
        'img'   => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=480&h=480&fit=crop&crop=face&q=80',
      ],
      [
        'name'  => 'Sarah Chen',
        'role'  => 'Funded Prop Trader',
        'quote' => 'Within 6 months of graduating I passed my FTMO challenge and got funded. The curriculum is the most practical I have found — no fluff, just real strategies that work in live markets. Worth every penny.',
        'img'   => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=480&h=480&fit=crop&crop=face&q=80',
      ],
      [
        'name'  => 'Marcus Williams',
        'role'  => 'Self-funded Trader',
        'quote' => 'I tried other courses before this one. None of them came close. The mentors here have actually worked at prop firms and know what is expected. The certification gave my career a real credibility boost.',
        'img'   => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=480&h=480&fit=crop&crop=face&q=80',
      ],
    ];
    // Override with WP testimonials only if there are 2 or more
    $wp_testis = pt101_testimonials( 3 );
    $wp_list   = [];
    if ( $wp_testis->have_posts() ):
      while ( $wp_testis->have_posts() ): $wp_testis->the_post();
        $wp_list[] = [
          'name'  => get_the_title(),
          'role'  => get_post_meta( get_the_ID(), 'author_role', true ) ?: 'Trade Desk Analyst',
          'quote' => get_the_content(),
          'img'   => get_the_post_thumbnail_url( null, 'large' ) ?: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=480&h=480&fit=crop&crop=face&q=80',
        ];
      endwhile;
      wp_reset_postdata();
      if ( count( $wp_list ) >= 2 ) $all_testis = $wp_list;
    endif;
    $total = count( $all_testis );
    ?>

    <div class="testi-carousel" id="testi-carousel">
      <?php foreach ( $all_testis as $idx => $t ): ?>
      <div class="testi-card" data-slide="<?php echo $idx; ?>" style="<?php echo $idx === 0 ? 'display:block' : 'display:none'; ?>">
        <div class="testi-body-row">

          <div class="testi-avatar-wrap">
            <img src="<?php echo esc_url( $t['img'] ); ?>" alt="<?php echo esc_attr( $t['name'] ); ?>" loading="lazy">
            <div class="testi-avatar-label">
              <span class="tav-name"><?php echo esc_html( $t['name'] ); ?></span>
              <span class="tav-role"><?php echo esc_html( $t['role'] ); ?></span>
            </div>
          </div>

          <div class="testi-quote-block">
            <div class="testi-stars" aria-hidden="true">
              <?php for ( $s = 0; $s < 5; $s++ ): ?><span>★</span><?php endfor; ?>
            </div>
            <p class="testi-quote"><?php echo esc_html( $t['quote'] ); ?></p>
            <div class="testi-author">
              <div class="testi-name"><?php echo esc_html( $t['name'] ); ?></div>
              <div class="testi-role-tag"><?php echo esc_html( $t['role'] ); ?></div>
            </div>
          </div>

        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Carousel nav + dots -->
    <?php if ( $total > 1 ): ?>
    <div class="testi-nav">
      <div class="testi-dots" aria-label="Carousel pagination">
        <?php for ( $d = 0; $d < $total; $d++ ): ?>
        <button class="testi-dot<?php echo $d === 0 ? ' active' : ''; ?>" data-dot="<?php echo $d; ?>" aria-label="Review <?php echo $d+1; ?>" type="button"></button>
        <?php endfor; ?>
      </div>
      <div class="testi-arrows">
        <button id="testi-prev" aria-label="Previous review" type="button">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button id="testi-next" aria-label="Next review" type="button">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>
    <?php endif; ?>

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
    <p class="testi-foot">*Students who passed a prop firm's trading challenge and earned a funded account within 6 months of graduation.</p>

  </div>
</section>


<!-- ═══ PROGRAMS ═══════════════════════════════════════════ -->
<section class="programs-section" id="programs">
  <div class="container">
    <div class="programs-head">
      <h2>Choose your program</h2>
      <p>Created by experts who've spent years inside brokerages and prop firms, our programs focus on what truly matters for aspiring traders.</p>
    </div>
    <div class="programs-list">
      <?php
      $progs = pt101_programs();
      if ( $progs->have_posts() ):
        while ( $progs->have_posts() ): $progs->the_post();
          $badge = get_post_meta( get_the_ID(), 'program_badge', true ) ?: '';
          $duration = get_post_meta( get_the_ID(), 'program_duration', true ) ?: '';
      ?>
          <a href="<?php the_permalink(); ?>" class="prog-row">
            <span class="prog-title"><?php echo esc_html( get_the_title() ); ?></span>
            <span class="prog-meta">
              <?php if ( $duration ): ?><span class="prog-duration"><?php echo esc_html( $duration ); ?></span><?php endif; ?>
              <?php if ( $badge ): ?><span class="prog-badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
              <svg class="prog-arrow" width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9h8M9 5l4 4-4 4" stroke="#111320" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
          </a>
      <?php endwhile; wp_reset_postdata();
      else:
        foreach ( [
          [ 'Trading foundations (terminology and basics)', 'Intro program', '4 weeks',  'Build a solid understanding of trading terminology, order types, chart reading and how markets move.',         '/intro-to-trading' ],
          [ 'Market mechanics &amp; analysis',              '',              '6 weeks',  'Dive deep into technical and fundamental analysis, price action, indicators and market psychology.',            '/market-mechanics-analysis' ],
          [ 'Strategy development &amp; advanced technicals', '',            '8 weeks',  'Develop and backtest your own edge. Risk management, entries, exits and trade journaling.',                    '/strategy-development-advanced-technicals' ],
          [ 'Mastering professional trading',               '',              '10 weeks', 'Simulate a real prop firm environment. Live markets, peer review, senior mentor sessions and final assessment.', '/mastering-professional-trading' ],
        ] as [ $name, $badge, $duration, $desc, $slug ] ):
      ?>
          <a href="<?php echo esc_url( home_url( $slug ) ); ?>" class="prog-row">
            <div class="prog-copy">
              <div class="prog-top">
                <span class="prog-title"><?php echo $name; ?></span>
                <?php if ( $badge ): ?><span class="prog-badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
              </div>
              <p class="prog-desc"><?php echo esc_html( $desc ); ?></p>
            </div>
            <span class="prog-meta">
              <span class="prog-duration"><?php echo esc_html( $duration ); ?></span>
              <svg class="prog-arrow" width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9h8M9 5l4 4-4 4" stroke="#888b9e" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
          </a>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>


<!-- ═══ CTA ════════════════════════════════════════════════ -->
<section class="cta-section">
  <h2>Invest in the skills and confidence to start earning!</h2>
  <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="btn btn-accent">
    <?php echo esc_html( get_theme_mod( 'pt101_cta_btn', 'Choose your program' ) ); ?>
  </a>
  <div class="cta-trust-row">
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M6.5 1l1.3 2.8 3 .4-2.2 2.1.5 3-2.6-1.4L4 9.3l.5-3L2.2 4.2l3-.4z" stroke="#888b9e" stroke-width="1.1" stroke-linejoin="round"/></svg>
      No credit card required
    </span>
    <span class="cta-trust-pipe">|</span>
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="6.5" cy="6.5" r="5" stroke="#888b9e" stroke-width="1.1"/><path d="M4.5 6.5l1.5 1.5L8.5 5" stroke="#888b9e" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Cancel anytime
    </span>
    <span class="cta-trust-pipe">|</span>
    <span class="cta-trust-item">
      <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 9.5c0-2.2 2-3.5 4.5-3.5S11 7.3 11 9.5" stroke="#888b9e" stroke-width="1.1" stroke-linecap="round"/><circle cx="6.5" cy="4" r="2" stroke="#888b9e" stroke-width="1.1"/></svg>
      10,000+ active students
    </span>
  </div>
</section>

</main>

<?php get_footer(); ?>
