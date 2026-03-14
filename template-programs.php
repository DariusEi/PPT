<?php
/**
 * Template Name: Programs Page
 * Template Post Type: page
 */
get_header();

$fallback_programs = [
  [
    'tab'          => 'beginner',
    'title'        => 'Market mechanics & analysis',
    'duration'     => '2 - 4 months',
    'duration_pct' => 35,
    'level'        => 'No prior experience',
    'for'          => 'Ideal for: complete beginners with no trading background',
    'url'          => home_url( '/market-mechanics-analysis' ),
    'price'        => '$299.00',
    'badge'        => '',
    'accent'       => '#7c6ef5',
    'skills'       => ['Trading mechanics','Core technical tools','Risk management fundamentals','Charting essentials','Types of market analysis'],
    'topics'       => [
      'Choosing a broker, order types, trading platforms',
      'The three types of analysis: technical, fundamental',
      'Types of charts, reading candlesticks, timeframes',
      'Support and resistance, trend lines, moving averages, Fibonacci',
      'Risk management, position sizing, stop loss and take profit',
    ],
  ],
  [
    'tab'          => 'advanced',
    'title'        => 'Mastering professional trading',
    'duration'     => '3 - 6 months',
    'duration_pct' => 75,
    'level'        => 'With prior experience',
    'for'          => 'Ideal for: traders ready to go professional and get funded',
    'url'          => home_url( '/mastering-professional-trading' ),
    'price'        => '$499.00',
    'badge'        => 'Most popular',
    'accent'       => '#e8503a',
    'skills'       => ['Professional trading','Trading system development','Comprehensive fundamental analysis','Charting essentials','Risk and money management'],
    'topics'       => [
      'Economic indicators, trading the news, intermarket analysis',
      'MTFA, Elliott wave theory, harmonic price patterns',
      'Developing a trading plan, journal and review process',
      'Position sizing, risk of ruin calculation, mindset',
      'Market sentiment and evaluation process',
    ],
  ],
  [
    'tab'          => 'advanced',
    'title'        => 'Strategy development & advanced technicals',
    'duration'     => '2 - 4 months',
    'duration_pct' => 55,
    'level'        => 'With prior experience',
    'for'          => 'Ideal for: traders who already know the basics and want an edge',
    'url'          => home_url( '/strategy-development-advanced-technicals' ),
    'price'        => '$399.00',
    'badge'        => 'Best value',
    'accent'       => '#00b67a',
    'skills'       => ['Technical indicators','Chart patterns and price action','Introduction to trading psychology','Trading divergences','Market environment'],
    'topics'       => [
      'Oscillators, momentum indicators, volume, pivot points',
      'Chart patterns, trading breakouts and fakeouts, Heikin Ashi',
      'Understanding and trading divergences',
      "The trader's mindset and common trading biases",
      'Currency crosses and market environment',
    ],
  ],
  [
    'tab'          => 'beginner intro',
    'title'        => 'Trading foundations',
    'duration'     => '2 - 4 weeks',
    'duration_pct' => 15,
    'level'        => 'No prior experience',
    'for'          => 'Ideal for: complete beginners wanting a structured starting point',
    'url'          => home_url( '/trading-foundations' ),
    'price'        => '$89.00',
    'badge'        => '',
    'accent'       => '#7c6ef5',
    'skills'       => ['What is forex','Essential terminology','Getting started'],
    'topics'       => [
      'Definition of forex, why trade, the major market participants',
      'Currency pairs, pips, lot sizes, leverage, cost of trading',
      'Reading a price quote, going long and short, demo accounts',
    ],
  ],
  [
    'tab'          => 'intro',
    'title'        => 'Intro to trading',
    'duration'     => '1 - 2 weeks',
    'duration_pct' => 8,
    'level'        => 'No prior experience',
    'for'          => 'Ideal for: anyone curious about trading with zero commitment',
    'url'          => home_url( '/intro-to-trading' ),
    'price'        => 'Free',
    'badge'        => 'Free',
    'accent'       => '#00b67a',
    'skills'       => ['What is trading','Market overview','First steps'],
    'topics'       => [
      'What financial markets are and why they exist',
      'Stocks, forex, crypto and commodities — key differences',
      'How to open a demo account and place your first trade',
    ],
  ],
];

/* Load from CPT if posts exist */
$programs = [];
$wp_query = pt101_programs();
if ( $wp_query->have_posts() ) {
  while ( $wp_query->have_posts() ) {
    $wp_query->the_post();
    $programs[] = [
      'tab'          => get_post_meta( get_the_ID(), 'program_tab',          true ) ?: 'advanced',
      'title'        => get_the_title(),
      'duration'     => get_post_meta( get_the_ID(), 'program_duration',     true ) ?: '',
      'duration_pct' => (int)( get_post_meta( get_the_ID(), 'program_duration_pct', true ) ?: 50 ),
      'level'        => get_post_meta( get_the_ID(), 'program_level',        true ) ?: '',
      'for'          => get_post_meta( get_the_ID(), 'program_for',          true ) ?: '',
      'price'        => get_post_meta( get_the_ID(), 'program_price',        true ) ?: '',
      'badge'        => get_post_meta( get_the_ID(), 'program_badge',        true ) ?: '',
      'accent'       => get_post_meta( get_the_ID(), 'program_accent',       true ) ?: '#7c6ef5',
      'skills'       => array_filter( explode( "\n", get_post_meta( get_the_ID(), 'program_skills', true ) ?: '' ) ),
      'topics'       => array_filter( explode( "\n", get_post_meta( get_the_ID(), 'program_topics', true ) ?: '' ) ),
      'url'          => get_permalink(),
    ];
  }
  wp_reset_postdata();
} else {
  $programs = $fallback_programs;
}

$tabs = [
  'advanced' => 'Advanced programs',
  'beginner' => 'Beginner programs',
  'intro'    => 'Intro programs',
];
?>

<!-- ═══ HERO ════════════════════════════════════════════════ -->
<section class="pg-hero">
  <div class="container">
    <div class="pg-hero-grid">

      <div class="pg-hero-left">
        <h1>Select the program<br>that fits your goals</h1>
        <p class="pg-hero-sub">Learn trading at your own pace with online programs that focus on practical skills and real-world experience.</p>

        <div class="pg-hero-rating">
          <div class="pg-stars" aria-label="4.9 out of 5 stars">
            <?php for ( $i = 0; $i < 5; $i++ ): ?>
              <svg width="18" height="18" viewBox="0 0 20 20"><path d="M10 1l2.39 4.84 5.34.78-3.86 3.77.91 5.31L10 13.27l-4.78 2.43.91-5.31L2.27 6.62l5.34-.78z" fill="#f05a28"/></svg>
            <?php endfor; ?>
          </div>
          <span class="pg-rating-text">4.9/5 by 1000+ students</span>
        </div>

        <div class="pg-trustpilot">
          <svg width="18" height="18" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="#00b67a"/></svg>
          <span>Trustpilot</span>
        </div>
      </div>

      <div class="pg-hero-right">
        <div class="pg-hero-img-wrap">
          <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=840&h=680&fit=crop&q=80"
               alt="Students learning trading together" loading="eager">
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ═══ PROGRAMS ════════════════════════════════════════════ -->
<section class="pg-programs-section" id="all-programs">
  <div class="container">

    <!-- Tab filter buttons -->
    <div class="pg-tabs-wrap" id="pg-tabs-wrap">
      <div class="pg-tabs" role="tablist" aria-label="Filter programs by category">
        <button class="pg-tab active" role="tab" data-tab="all"
                aria-selected="true" type="button">All programs</button>
        <?php foreach ( $tabs as $key => $label ): ?>
          <button class="pg-tab" role="tab" data-tab="<?php echo esc_attr($key); ?>"
                  aria-selected="false" type="button"><?php echo esc_html($label); ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Static section heading — never changes -->
    <div class="pg-section-head">
      <h2>Trading career programs</h2>
      <p>From your first trade to a funded account — our programs guide you every step of the way to becoming a confident, market-ready trader.</p>
    </div>

    <!-- Cards grid -->
    <div class="pg-grid" id="pg-grid">

      <?php foreach ( $programs as $prog ):
        $accent     = esc_attr( $prog['accent'] ?: '#7c6ef5' );
        $has_badge  = ! empty( $prog['badge'] );
        $is_free    = strtolower( trim( $prog['price'] ) ) === 'free';
        $view_url   = $prog['url'] ?? home_url('/programs');
        // Free courses go straight to checkout (product ID 158); paid courses go to their course page.
        $enroll_url = ( $is_free && function_exists( 'pt101_enroll_url' ) )
                      ? pt101_enroll_url( 158 )
                      : $view_url;
      ?>

      <div class="pg-card<?php echo $has_badge ? ' pg-card--has-badge' : ''; ?>"
           data-tab="<?php echo esc_attr( $prog['tab'] ); ?>">

        <div class="pg-card-top-bar" style="background:<?php echo $accent; ?>;"></div>

        <div class="pg-card-body">

          <div class="pg-card-title-row">
            <h3 class="pg-card-title"><?php echo esc_html( $prog['title'] ); ?></h3>
            <?php if ( $has_badge ): ?>
              <span class="pg-badge" style="background:<?php echo $accent; ?>;"><?php echo esc_html( $prog['badge'] ); ?></span>
            <?php endif; ?>
          </div>

          <div class="pg-card-duration-block">
            <div class="pg-card-duration-row">
              <span class="pg-card-duration"><?php echo esc_html( $prog['duration'] ); ?></span>
              <span class="pg-card-level"><?php echo esc_html( $prog['level'] ); ?></span>
            </div>
            <div class="pg-duration-track">
              <div class="pg-duration-fill" style="width:<?php echo (int)$prog['duration_pct']; ?>%;background:<?php echo $accent; ?>;"></div>
            </div>
          </div>

          <?php if ( !empty( $prog['for'] ) ): ?>
          <p class="pg-card-for">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><circle cx="6" cy="3.5" r="1.8" stroke="currentColor" stroke-width="1.1"/><path d="M1.5 10.5c0-2 2-3.2 4.5-3.2s4.5 1.2 4.5 3.2" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/></svg>
            <?php echo esc_html( $prog['for'] ); ?>
          </p>
          <?php endif; ?>

          <?php if ( !empty( $prog['skills'] ) ): ?>
          <div class="pg-card-block">
            <div class="pg-block-label">You'll learn</div>
            <div class="pg-skill-tags">
              <?php foreach ( $prog['skills'] as $s ): ?>
                <span class="pg-skill-tag"><?php echo esc_html( trim($s) ); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if ( !empty( $prog['topics'] ) ): ?>
          <div class="pg-card-block">
            <div class="pg-block-label">Topics</div>
            <ul class="pg-topics">
              <?php foreach ( $prog['topics'] as $t ): ?>
                <li><?php echo esc_html( trim($t) ); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

        </div><!-- .pg-card-body -->

        <div class="pg-card-footer">
          <a href="<?php echo esc_url($enroll_url); ?>" class="btn btn-accent pg-enroll-btn">
            <?php echo $is_free ? 'Enroll for free' : 'Enroll now for ' . esc_html( $prog['price'] ); ?>
          </a>
          <a href="<?php echo esc_url($view_url); ?>" class="pg-view-btn">View program</a>
        </div>

      </div><!-- .pg-card -->

      <?php endforeach; ?>

    </div><!-- #pg-grid -->

  </div>
</section>


<!-- ═══ CTA BAND ════════════════════════════════════════════ -->
<section class="pg-cta-band">
  <div class="container">
    <div class="pg-cta-inner">
      <div class="pg-cta-text">
        <h2>Not sure where to start?</h2>
        <p>Take our free intro program — no credit card needed. Get a feel for how we teach before committing to a full course.</p>
      </div>
      <div class="pg-cta-actions">
        <a href="<?php echo esc_url( home_url('/programs') ); ?>" class="btn btn-accent">Start for free →</a>
        <span class="pg-cta-guarantee">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><path d="M6.5 1l1.4 2.8 3.1.45-2.25 2.2.53 3.1L6.5 8.1l-2.78 1.46.53-3.1L2 4.25l3.1-.45z" stroke="rgba(240,240,245,0.45)" stroke-width="1.1" stroke-linejoin="round"/></svg>
          30-day money-back guarantee
        </span>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
