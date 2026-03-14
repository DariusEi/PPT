<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#0d0f1a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#primary"><?php esc_html_e( 'Skip to content', 'prop-trading-101' ); ?></a>

<header class="site-header" id="site-header" role="banner">
  <div class="container">
    <div class="header-inner">

      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" rel="home">
        <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
      </a>

      <nav aria-label="Primary navigation">
        <ul class="primary-nav">
          <!-- Programs with dropdown -->
          <li class="nav-item-dropdown" id="nav-programs">
            <button class="nav-dropdown-trigger" aria-expanded="false" aria-controls="programs-dropdown" type="button">
              Programs
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="margin-left:4px;transition:transform 0.2s;"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="nav-dropdown" id="programs-dropdown" role="region" aria-label="Programs menu">
              <div class="nav-dd-inner">

                <div class="nav-dd-section">
                  <div class="nav-dd-heading">Trading courses</div>
                  <div class="nav-dd-grid">
                    <a href="<?php echo esc_url( home_url( '/programs/trading-foundations' ) ); ?>" class="nav-dd-item">
                      <span class="nav-dd-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="#111320"><path d="M3 5a2 2 0 012-2h10a2 2 0 012 2v2H3V5zm0 4h14v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                      </span>
                      <span>Trading foundations</span>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/programs/mastering-professional-trading' ) ); ?>" class="nav-dd-item">
                      <span class="nav-dd-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="#111320"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5 8.12V12a1 1 0 00.553.894l4 2a1 1 0 00.894 0l4-2A1 1 0 0015 12V8.12l2.394-1.2a1 1 0 000-1.84l-7-3z"/></svg>
                      </span>
                      <span>Mastering professional trading</span>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/programs/market-mechanics-analysis' ) ); ?>" class="nav-dd-item">
                      <span class="nav-dd-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="#111320"><path d="M2 11a1 1 0 011-1h3a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h3a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h3a1 1 0 011 1v12a1 1 0 01-1 1h-3a1 1 0 01-1-1V4z"/></svg>
                      </span>
                      <span>Market mechanics &amp; analysis</span>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/programs/strategy-development' ) ); ?>" class="nav-dd-item">
                      <span class="nav-dd-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="#111320"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 002 2h4a2 2 0 002-2V3a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                      </span>
                      <span>Strategy development &amp; advanced technicals</span>
                    </a>
                  </div>
                </div>

                <div class="nav-dd-section">
                  <div class="nav-dd-heading">Intro to trading</div>
                  <div class="nav-dd-grid nav-dd-grid--single">
                    <a href="<?php echo esc_url( home_url( '/programs/intro' ) ); ?>" class="nav-dd-item">
                      <span class="nav-dd-icon" aria-hidden="true">
                        <svg viewBox="0 0 20 20" fill="#111320"><path d="M3 5a2 2 0 012-2h10a2 2 0 012 2v2H3V5zm0 4h14v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                      </span>
                      <span>Intro to trading</span>
                    </a>
                  </div>
                </div>

                <div class="nav-dd-footer">
                  <div>
                    <div class="nav-dd-footer-label">Can't decide?</div>
                    <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="nav-dd-footer-link">Review all programs <span aria-hidden="true">→</span></a>
                  </div>
                </div>

              </div>
            </div>
          </li>
          <?php
          wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'walker'         => new PT101_Walker(),
            'fallback_cb'    => function () {
              $links = [
                'About'          => '/about',
                'Resources'      => '/resources',
                'Getting funded' => '/getting-funded',
                'Mentors'        => '/mentors',
              ];
              foreach ( $links as $label => $url ) {
                echo '<li><a href="' . esc_url( home_url( $url ) ) . '">' . esc_html( $label ) . '</a></li>';
              }
            },
          ]);
          ?>
        </ul>
      </nav>

      <div class="header-actions">
        <a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn-hdr-login">Log In</a>
        <a href="#programs" class="btn-hdr-enroll">Enroll now &rarr;</a>
      </div>

      <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-drawer" type="button">
        <span></span><span></span><span></span>
      </button>

    </div>
  </div>
</header>

<nav class="mobile-drawer" id="mobile-drawer" aria-label="Mobile menu" aria-hidden="true">
  <div class="mobile-drawer-section-label">Programs</div>
  <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>">Review all programs →</a>
  <a href="<?php echo esc_url( home_url( '/programs/trading-foundations' ) ); ?>">Trading foundations</a>
  <a href="<?php echo esc_url( home_url( '/programs/market-mechanics-analysis' ) ); ?>">Market mechanics &amp; analysis</a>
  <a href="<?php echo esc_url( home_url( '/programs/strategy-development' ) ); ?>">Strategy development &amp; advanced technicals</a>
  <a href="<?php echo esc_url( home_url( '/programs/mastering-professional-trading' ) ); ?>">Mastering professional trading</a>
  <a href="<?php echo esc_url( home_url( '/programs/intro' ) ); ?>">Intro to trading</a>
  <div class="mobile-drawer-divider"></div>
  <ul class="mobile-drawer-links" aria-label="Site links">
    <?php
    wp_nav_menu([
      'theme_location' => 'primary',
      'container'      => false,
      'items_wrap'     => '%3$s',
      'depth'          => 1,
      'fallback_cb'    => function () {
        $links = [
          'About'          => '/about',
          'Resources'      => '/resources',
          'Getting funded' => '/getting-funded',
          'Mentors'        => '/mentors',
        ];
        foreach ( $links as $label => $url ) {
          echo '<li><a href="' . esc_url( home_url( $url ) ) . '">' . esc_html( $label ) . '</a></li>';
        }
      },
    ]);
    ?>
  </ul>
  <div class="mobile-drawer-ctas">
    <a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn btn-ghost">Log In</a>
    <a href="<?php echo esc_url( home_url( '/programs' ) ); ?>" class="btn btn-accent">Enroll now &rarr;</a>
  </div>
</nav>
