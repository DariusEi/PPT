<?php
/**
 * Prop Trading 101 — functions.php v4
 */
defined( 'ABSPATH' ) || exit;

define( 'PT101_VER', wp_get_theme()->get( 'Version' ) ?: '1.0.0' );
define( 'PT101_DIR', get_template_directory() );
define( 'PT101_URI', get_template_directory_uri() );

/* ── SETUP ─────────────────────────────────── */
function pt101_setup() {
    load_theme_textdomain( 'prop-trading-101', PT101_DIR . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','style','script' ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    register_nav_menus([
        'primary'  => __( 'Primary Menu',      'prop-trading-101' ),
        'footer-1' => __( 'Footer: Handbooks', 'prop-trading-101' ),
        'footer-2' => __( 'Footer: About Us',  'prop-trading-101' ),
        'footer-3' => __( 'Footer: Programs',  'prop-trading-101' ),
    ]);
}
add_action( 'after_setup_theme', 'pt101_setup' );

/* ── ASSETS ─────────────────────────────────── */
function pt101_assets() {
    // Theme stylesheet — PolySans fonts are self-hosted in /fonts/
    wp_enqueue_style( 'pt101-style', get_stylesheet_uri(), [], PT101_VER );

    // Main JS — in footer, no deps
    wp_enqueue_script( 'pt101-main', PT101_URI . '/js/main.js', [], PT101_VER, true );
}
add_action( 'wp_enqueue_scripts', 'pt101_assets' );

/* ── DEQUEUE WP STYLES THAT FIGHT OURS ─────── */
function pt101_dequeue() {
    // Block editor styles — unused on frontend
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'classic-theme-styles' );
    remove_action( 'wp_head', '_custom_background_cb' );
}
add_action( 'wp_enqueue_scripts', 'pt101_dequeue', 100 );

/* ── STRIP NOISY HEAD OUTPUT ─────────────────── */
// Emoji detection adds ~800 B JS + a DNS request to s.w.org — not needed
remove_action( 'wp_head',         'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
// Unnecessary discovery links
remove_action( 'wp_head', 'wp_shortlink_wp_head',        10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action( 'wp_head', 'rest_output_link_wp_head',     10 );

/* ── BLOCK ASSET SUPPRESSION ────────────────── */
add_filter( 'should_load_separate_core_block_assets', '__return_false' );
add_filter( 'use_default_gallery_style', '__return_false' );
add_filter( 'theme_mod_background_color', function () { return '0d0f1a'; } );
add_filter( 'theme_mod_background_image', '__return_empty_string' );

/* ── SECURITY ───────────────────────────────── */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
add_filter( 'the_generator', '__return_empty_string' );

/* ── RESOURCE HINTS & PRELOADS ───────────────── */
add_action( 'wp_head', function () {
    // Preconnect to Unsplash (used for hero, testimonial, and student images)
    echo '<link rel="preconnect" href="https://images.unsplash.com" crossorigin>' . "\n";
    echo '<link rel="dns-prefetch" href="//images.unsplash.com">' . "\n";

    // Preload the heaviest font weight (800 = all major headings) to reduce FOUT
    $uri = get_template_directory_uri();
    echo '<link rel="preload" href="' . esc_url( $uri . '/fonts/polysanstrial-bulkywide.otf' ) . '" as="font" type="font/otf" crossorigin>' . "\n";

    // Preload course hero image when we can determine it early
    $hero_imgs = [
        'template-course-market-mechanics'               => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=600&h=700&fit=crop&q=80',
        'template-course-mastering-professional-trading' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=700&fit=crop&crop=top&q=80',
        'template-course-strategy-development'           => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=600&h=700&fit=crop&crop=top&q=80',
    ];
    $tpl = str_replace( '.php', '', (string) get_page_template_slug() );
    if ( isset( $hero_imgs[ $tpl ] ) ) {
        echo '<link rel="preload" href="' . esc_url( $hero_imgs[ $tpl ] ) . '" as="image" fetchpriority="high">' . "\n";
    }
}, 1 );

/* ── NAV WALKER ─────────────────────────────── */
class PT101_Walker extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="nav-submenu">';
    }
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );
        $classes      = implode( ' ', array_filter( (array) $item->classes ) );
        if ( $depth === 0 ) {
            $output .= '<li class="' . esc_attr( $classes ) . '">';
            $output .= '<a href="' . esc_url( $item->url ) . '">';
            $output .= esc_html( $item->title );
            if ( $has_children ) $output .= ' <span class="nav-chevron" aria-hidden="true">▾</span>';
            $output .= '</a>';
        } else {
            $output .= '<li><a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
        }
    }
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}

/* ── CUSTOMIZER ─────────────────────────────── */
function pt101_customizer( $wp_customize ) {
    $wp_customize->add_section( 'pt101_hero', [ 'title' => 'Hero Section', 'priority' => 30 ] );
    $fields = [
        'pt101_hero_line1'     => [ 'Hero Line 1',       'From learning'         ],
        'pt101_hero_line2'     => [ 'Hero Line 2',       'to earning.'           ],
        'pt101_hero_line3'     => [ 'Hero Line 3',       'Get market-ready.'     ],
        'pt101_hero_sub'       => [ 'Sub-headline',      'Real trading skills. Expert mentorship. Funded accounts. Join 10,000+ students.' ],
        'pt101_hero_btn_text'  => [ 'CTA Button Text',   'Choose your program'   ],
        'pt101_hero_btn_url'   => [ 'CTA Button URL',    '/programs'             ],
        'pt101_stat_students'  => [ 'Stat: Students',    '10,000+'               ],
        'pt101_stat_countries' => [ 'Stat: Countries',   '140+'                  ],
        'pt101_stat_rating'    => [ 'Stat: Rating',      '4.9/5'                 ],
        'pt101_stat_funded'    => [ 'Stat: Funded',      '$9M+'                  ],
    ];
    foreach ( $fields as $id => [ $label, $default ] ) {
        $wp_customize->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( $id, [ 'label' => $label, 'section' => 'pt101_hero', 'type' => 'text' ] );
    }

    $wp_customize->add_section( 'pt101_feat', [ 'title' => 'Features Section', 'priority' => 35 ] );
    $wp_customize->add_setting( 'pt101_feat_heading', [ 'default' => 'Your launchpad for a confident career in professional trading', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'pt101_feat_heading', [ 'label' => 'Features Heading', 'section' => 'pt101_feat', 'type' => 'textarea' ] );
    $wp_customize->add_setting( 'pt101_funded_pct', [ 'default' => '89.7%', 'sanitize_callback' => 'sanitize_text_field' ] );
    $wp_customize->add_control( 'pt101_funded_pct', [ 'label' => 'Funded % Stat', 'section' => 'pt101_feat', 'type' => 'text' ] );

    $wp_customize->add_section( 'pt101_cta', [ 'title' => 'CTA Section', 'priority' => 60 ] );
    foreach ([
        'pt101_cta_h2'  => [ 'CTA Heading',    'Invest in the skills and confidence to start earning.' ],
        'pt101_cta_sub' => [ 'CTA Sub-text',   'Join 10,000+ students already on their way to funded accounts.' ],
        'pt101_cta_btn' => [ 'CTA Button',     'Choose your program' ],
    ] as $id => [ $label, $default ] ) {
        $wp_customize->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'sanitize_text_field' ] );
        $wp_customize->add_control( $id, [ 'label' => $label, 'section' => 'pt101_cta', 'type' => 'text' ] );
    }
}
add_action( 'customize_register', 'pt101_customizer' );

/* ── CUSTOM POST TYPES ──────────────────────── */
function pt101_cpts() {
    register_post_type( 'pt101_program', [
        'labels'    => [ 'name' => 'Programs', 'singular_name' => 'Program', 'add_new_item' => 'Add Program' ],
        'public'    => true, 'has_archive' => false, 'show_in_rest' => true,
        'supports'  => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'menu_icon' => 'dashicons-welcome-learn-more',
        'rewrite'   => [ 'slug' => 'program-detail' ],
    ]);
    register_post_type( 'pt101_testimonial', [
        'labels'    => [ 'name' => 'Testimonials', 'singular_name' => 'Testimonial' ],
        'public'    => false, 'show_ui' => true, 'show_in_rest' => true,
        'supports'  => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
        'menu_icon' => 'dashicons-format-quote',
    ]);
}
add_action( 'init', 'pt101_cpts' );

/* ── BODY CLASS ─────────────────────────────── */
add_filter( 'body_class', function ( $classes ) {
    $classes[] = 'pt101';
    return $classes;
} );

/* ── HELPERS ────────────────────────────────── */
function pt101_programs( $limit = -1 ) {
    $key    = 'pt101_programs_' . $limit;
    $cached = wp_cache_get( $key, 'pt101' );
    if ( false !== $cached ) return $cached;
    $q = new WP_Query( [
        'post_type'              => 'pt101_program',
        'posts_per_page'         => $limit,
        'post_status'            => 'publish',
        'orderby'                => 'menu_order',
        'order'                  => 'ASC',
        'no_found_rows'          => true,   // skip COUNT(*) — no pagination needed
        'update_post_term_cache' => false,  // no taxonomy data used
    ] );
    wp_cache_set( $key, $q, 'pt101', 5 * MINUTE_IN_SECONDS );
    return $q;
}
function pt101_testimonials( $limit = 1 ) {
    $key    = 'pt101_testimonials_' . $limit;
    $cached = wp_cache_get( $key, 'pt101' );
    if ( false !== $cached ) return $cached;
    $q = new WP_Query( [
        'post_type'              => 'pt101_testimonial',
        'posts_per_page'         => $limit,
        'post_status'            => 'publish',
        'no_found_rows'          => true,
        'update_post_term_cache' => false,
    ] );
    wp_cache_set( $key, $q, 'pt101', 5 * MINUTE_IN_SECONDS );
    return $q;
}

/* ── CACHE INVALIDATION ──────────────────────── */
add_action( 'save_post', function ( $post_id, $post ) {
    if ( 'pt101_program' === $post->post_type ) {
        foreach ( [ -1, 3, 6 ] as $n ) {
            wp_cache_delete( 'pt101_programs_' . $n, 'pt101' );
        }
    }
    if ( 'pt101_testimonial' === $post->post_type ) {
        foreach ( [ 1, 2, 3, 4 ] as $n ) {
            wp_cache_delete( 'pt101_testimonials_' . $n, 'pt101' );
        }
    }
}, 10, 2 );
function pt101_placeholder( $i = 1 ) {
    $imgs = [
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=560&fit=crop&crop=face&q=80',
        'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=560&fit=crop&crop=face&q=80',
        'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&h=560&fit=crop&crop=face&q=80',
        'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=560&fit=crop&crop=face&q=80',
    ];
    return $imgs[ ( $i - 1 ) % count( $imgs ) ];
}

/* ── FOOTER FALLBACK MENUS ──────────────────── */
function pt101_footer_handbooks() {
    foreach ( [
        'Economic calendar'                          => '#',
        'Market mechanics & analysis'                => '#',
        'Strategy development & advanced technicals' => '#',
        'Mastering professional trading'             => '#',
    ] as $label => $url ) {
        echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
    }
}
function pt101_footer_about() {
    foreach ( [
        'About us'     => '/about',
        'Contact us'   => '/contact',
        'How it works' => '/how-it-works',
        'FAQ'          => '/faq',
        'Blog'         => '/blog',
    ] as $label => $url ) {
        echo '<li><a href="' . esc_url( home_url( $url ) ) . '">' . esc_html( $label ) . '</a></li>';
    }
}
function pt101_footer_programs() {
    foreach ( [
        'Trading foundations'                        => '/trading-foundations',
        'Market mechanics & analysis'                => '/market-mechanics-analysis',
        'Strategy development & advanced technicals' => '/strategy-development-advanced-technicals',
        'Mastering professional trading'             => '/mastering-professional-trading',
    ] as $label => $url ) {
        echo '<li><a href="' . esc_url( home_url( $url ) ) . '">' . esc_html( $label ) . '</a></li>';
    }
}

/* ── AUTO-CREATE COURSE PAGES ───────────────── */
function pt101_ensure_course_pages() {
    $pages = [
        [
            'title'    => 'Market Mechanics & Analysis',
            'slug'     => 'market-mechanics-analysis',
            'template' => 'template-course-market-mechanics.php',
        ],
        [
            'title'    => 'Mastering Professional Trading',
            'slug'     => 'mastering-professional-trading',
            'template' => 'template-course-mastering-professional-trading.php',
        ],
        [
            'title'    => 'Strategy Development & Advanced Technicals',
            'slug'     => 'strategy-development-advanced-technicals',
            'template' => 'template-course-strategy-development.php',
        ],
        [
            'title'    => 'Trading Foundations',
            'slug'     => 'trading-foundations',
            'template' => 'template-course-trading-foundations.php',
        ],
        [
            'title'    => 'Intro to Trading',
            'slug'     => 'intro-to-trading',
            'template' => 'template-course-intro-to-trading.php',
        ],
        [
            'title'    => 'Mentors',
            'slug'     => 'mentors',
            'template' => 'template-mentors.php',
        ],
        [
            'title'    => 'Getting Funded',
            'slug'     => 'getting-funded',
            'template' => 'template-getting-funded.php',
        ],
        [
            'title'    => 'About Us',
            'slug'     => 'about',
            'template' => 'template-about.php',
        ],
        [
            'title'    => 'Contact Us',
            'slug'     => 'contact',
            'template' => 'template-contact.php',
        ],
    ];

    foreach ( $pages as $p ) {
        $existing = get_page_by_path( $p['slug'] );
        if ( $existing ) {
            if ( get_post_meta( $existing->ID, '_wp_page_template', true ) !== $p['template'] ) {
                update_post_meta( $existing->ID, '_wp_page_template', $p['template'] );
            }
            continue;
        }
        $id = wp_insert_post( [
            'post_title'  => $p['title'],
            'post_name'   => $p['slug'],
            'post_status' => 'publish',
            'post_type'   => 'page',
        ] );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_wp_page_template', $p['template'] );
        }
    }
}
add_action( 'after_switch_theme', 'pt101_ensure_course_pages' );
add_action( 'init', function () {
    if ( get_option( 'pt101_course_pages_v6' ) ) return;
    pt101_ensure_course_pages();
    update_option( 'pt101_course_pages_v6', '1' );
}, 20 );

/* ── WOOCOMMERCE: DIRECT ENROLL URL ─────────── */
/**
 * Returns a URL that adds a product to cart and redirects straight to checkout.
 * Usage: pt101_enroll_url( 158 )
 */
function pt101_enroll_url( $product_id ) {
    return add_query_arg( [
        'pt101_enroll' => absint( $product_id ),
        'nonce'        => wp_create_nonce( 'pt101_enroll_' . $product_id ),
    ], home_url( '/' ) );
}

add_action( 'template_redirect', function () {
    if ( empty( $_GET['pt101_enroll'] ) ) return;
    $product_id = absint( $_GET['pt101_enroll'] );
    $nonce      = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'pt101_enroll_' . $product_id ) ) return;
    if ( ! function_exists( 'WC' ) ) return;

    WC()->cart->empty_cart();
    WC()->cart->add_to_cart( $product_id, 1 );
    wp_safe_redirect( wc_get_checkout_url() );
    exit;
} );

/* ── WOOCOMMERCE: ACCOUNT CREATION ON CHECKOUT ─ */
add_filter( 'woocommerce_checkout_registration_required', '__return_false' );
add_filter( 'woocommerce_checkout_registration_enabled',  '__return_true' );

/* ── WOOCOMMERCE: REDIRECT /cart → checkout (or course if empty) ── */
add_action( 'template_redirect', function () {
    if ( ! function_exists( 'is_cart' ) || ! is_cart() ) return;
    if ( WC()->cart->is_empty() ) {
        wp_safe_redirect( home_url( '/intro-to-trading' ) );
    } else {
        wp_safe_redirect( wc_get_checkout_url() );
    }
    exit;
}, 5 );

/* ── WOOCOMMERCE: INLINE DARK-THEME OVERRIDES ──
 * Output late in <head> so we beat every WC/Blocks stylesheet.
 * Only runs on checkout and order-received pages.
 */
add_action( 'wp_head', function () {
    if ( ! function_exists( 'is_checkout' ) ) return;
    if ( ! is_checkout() && ! is_wc_endpoint_url() ) return;
    ?>
<style id="pt101-wc-dark">
/* ══ 1. PAGE SHELL ══════════════════════════════════════════════ */
html body.pt101.woocommerce-checkout,
html body.pt101.woocommerce-order-received { background:#0d0f1a!important; color:#f0f0f5!important; }
html body.pt101 #page,
html body.pt101 .site-main,
html body.pt101 main { background:transparent!important; }
html body.pt101 .entry-header,
html body.pt101 .woocommerce-page .entry-header { display:none!important; }
html body.pt101 .wp-block-group,
html body.pt101 .wp-block-group__inner-container { background:transparent!important; max-width:none!important; padding:0!important; margin:0!important; }

/* ══ 2. LAYOUT ═════════════════════════════════════════════════ */
html body.pt101 .woocommerce,
html body.pt101 .wp-block-woocommerce-checkout { max-width:1160px; margin:0 auto; padding:64px 28px 100px!important; }
html body.pt101 .wc-block-checkout__main { padding-right:32px!important; }

/* ══ 3. FORM FIELDS ════════════════════════════════════════════ */
html body.pt101 .wc-block-components-text-input,
html body.pt101 .wc-block-components-select { position:relative!important; }

html body.pt101 .wc-block-components-text-input input,
html body.pt101 .wc-block-components-text-input textarea,
html body.pt101 .wc-block-components-select select,
html body.pt101 .wc-block-components-country-input input,
html body.pt101 .wc-block-components-state-input input,
html body.pt101 input[type="text"],
html body.pt101 input[type="email"],
html body.pt101 input[type="tel"],
html body.pt101 input[type="password"],
html body.pt101 select,
html body.pt101 textarea {
  background:#1a1d2e!important;
  background-color:#1a1d2e!important;
  color:#f0f0f5!important;
  border:1.5px solid rgba(255,255,255,0.12)!important;
  border-radius:10px!important;
  padding:22px 16px 8px!important;
  font-family:inherit!important;
  font-size:.97rem!important;
  line-height:1.4!important;
  box-shadow:none!important;
  outline:none!important;
  transition:border-color .18s,box-shadow .18s!important;
  -webkit-appearance:none!important;
  appearance:none!important;
  width:100%!important;
}
html body.pt101 .wc-block-components-text-input input:focus,
html body.pt101 .wc-block-components-select select:focus,
html body.pt101 input[type="text"]:focus,
html body.pt101 input[type="email"]:focus,
html body.pt101 input[type="tel"]:focus,
html body.pt101 select:focus,
html body.pt101 textarea:focus {
  border-color:#7c6ef5!important;
  box-shadow:0 0 0 3px rgba(124,110,245,.15)!important;
}
html body.pt101 input::placeholder,
html body.pt101 textarea::placeholder { color:transparent!important; }

/* select chevron */
html body.pt101 select {
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23888b9e' stroke-width='1.6' fill='none' stroke-linecap='round'/%3E%3C/svg%3E")!important;
  background-repeat:no-repeat!important;
  background-position:right 16px center!important;
  padding-right:44px!important;
}
html body.pt101 option { background:#1a1d2e!important; color:#f0f0f5!important; }

/* floating labels — default (label acts as placeholder inside field) */
html body.pt101 .wc-block-components-text-input label,
html body.pt101 .wc-block-components-select label,
html body.pt101 .wc-block-components-country-input label,
html body.pt101 .wc-block-components-state-input label {
  position:absolute!important;
  top:50%!important;
  left:17px!important;
  transform:translateY(-50%)!important;
  color:rgba(240,240,245,.32)!important;
  font-size:.88rem!important;
  font-weight:400!important;
  letter-spacing:.01em!important;
  text-transform:none!important;
  pointer-events:none!important;
  transition:top .15s,font-size .15s,transform .15s!important;
  white-space:nowrap!important;
}
/* floated (field has value or is focused) */
html body.pt101 .wc-block-components-text-input.is-active label,
html body.pt101 .wc-block-components-select.is-active label {
  top:9px!important;
  transform:none!important;
  font-size:.68rem!important;
  color:rgba(240,240,245,.38)!important;
  letter-spacing:.06em!important;
  text-transform:uppercase!important;
}

/* non-floating labels (checkboxes, payment, etc.) */
html body.pt101 .wc-block-components-checkbox .wc-block-components-checkbox__label,
html body.pt101 .wc-block-components-checkbox__label {
  color:rgba(240,240,245,.55)!important;
  font-size:.88rem!important;
  font-weight:400!important;
  text-transform:none!important;
  letter-spacing:0!important;
}

/* ══ 4. SECTION HEADINGS ═══════════════════════════════════════ */
html body.pt101 .wc-block-components-checkout-step__title,
html body.pt101 .wc-block-checkout h2,
html body.pt101 .wc-block-checkout h3,
html body.pt101 .woocommerce h2,
html body.pt101 .woocommerce h3 {
  color:#f0f0f5!important;
  font-family:inherit!important;
  font-size:1.08rem!important;
  font-weight:700!important;
  letter-spacing:-.01em!important;
  margin-bottom:18px!important;
}
html body.pt101 .wc-block-components-checkout-step__number {
  background:#7c6ef5!important; color:#fff!important; border:none!important;
  width:24px!important; height:24px!important; font-size:.75rem!important;
}
html body.pt101 .wc-block-components-checkout-step {
  background:transparent!important;
  border-bottom:1px solid rgba(255,255,255,0.06)!important;
  padding-bottom:28px!important; margin-bottom:28px!important;
}
html body.pt101 .wc-block-components-checkout-step:last-child { border-bottom:none!important; }

/* ══ 5. HIDE "ADD NOTE" ════════════════════════════════════════ */
html body.pt101 .wc-block-checkout__add-note,
html body.pt101 .woocommerce-additional-fields { display:none!important; }

/* ══ 6. ORDER SUMMARY SIDEBAR ══════════════════════════════════ */
html body.pt101 .wc-block-checkout__sidebar,
html body.pt101 .wp-block-woocommerce-checkout-order-summary-block {
  background:#13162a!important;
  border:1.5px solid rgba(255,255,255,0.07)!important;
  border-radius:16px!important;
  padding:24px!important;
  overflow:hidden!important;
}
html body.pt101 .wc-block-components-order-summary { background:transparent!important; }
html body.pt101 .wc-block-components-order-summary__title,
html body.pt101 .wc-block-checkout__sidebar h2 {
  color:#f0f0f5!important; font-size:1rem!important; font-weight:700!important;
}
html body.pt101 .wc-block-components-order-summary-item__name {
  color:#f0f0f5!important; font-size:.95rem!important; font-weight:600!important; line-height:1.3!important;
}
html body.pt101 .wc-block-components-order-summary-item__description {
  color:rgba(240,240,245,.45)!important; font-size:.78rem!important; line-height:1.4!important;
  display:-webkit-box!important; -webkit-line-clamp:2!important; -webkit-box-orient:vertical!important; overflow:hidden!important;
}
html body.pt101 .wc-block-components-order-summary-item__quantity {
  background:#7c6ef5!important; color:#fff!important;
  border:2px solid #0d0f1a!important; border-radius:50%!important;
}
html body.pt101 .wc-block-components-order-summary-item__total-price {
  color:#f0f0f5!important; font-weight:600!important;
}
/* totals */
html body.pt101 .wc-block-components-totals-item { border-top:1px solid rgba(255,255,255,0.07)!important; padding:10px 0!important; }
html body.pt101 .wc-block-components-totals-item__label,
html body.pt101 .wc-block-components-totals-item__value,
html body.pt101 .wc-block-components-totals-item span { color:rgba(240,240,245,.72)!important; font-size:.9rem!important; }
html body.pt101 .wc-block-components-totals-footer-item { border-top:1.5px solid rgba(255,255,255,0.13)!important; padding-top:14px!important; }
html body.pt101 .wc-block-components-totals-footer-item .wc-block-components-totals-item__label,
html body.pt101 .wc-block-components-totals-footer-item .wc-block-components-totals-item__value {
  color:#f0f0f5!important; font-size:1.05rem!important; font-weight:700!important;
}
/* coupon */
html body.pt101 .wc-block-components-totals-coupon details > summary { color:#7c6ef5!important; font-size:.85rem!important; cursor:pointer; }
html body.pt101 .wc-block-components-totals-coupon__button {
  background:#7c6ef5!important; color:#fff!important; border:none!important; border-radius:8px!important; font-weight:700!important;
}

/* ══ 7. PAYMENT BLOCK ══════════════════════════════════════════ */
html body.pt101 .wc-block-components-payment-methods,
html body.pt101 .wc-block-checkout__payment-method {
  background:#13162a!important; border:1.5px solid rgba(255,255,255,0.07)!important;
  border-radius:12px!important; padding:18px!important;
}
html body.pt101 .wc-block-components-payment-method-label__name,
html body.pt101 .wc-block-components-radio-control__label { color:#f0f0f5!important; font-size:.93rem!important; }
html body.pt101 .wc-block-components-radio-control__input { accent-color:#7c6ef5!important; }
html body.pt101 .wc-block-components-radio-control-accordion-content {
  background:#0d0f1a!important; border-radius:8px!important;
  color:rgba(240,240,245,.55)!important; font-size:.85rem!important; padding:12px!important;
}

/* ══ 8. TERMS / PRIVACY ════════════════════════════════════════ */
html body.pt101 .wc-block-checkout__terms p,
html body.pt101 .wc-block-checkout__privacy-policy p {
  color:rgba(240,240,245,.35)!important; font-size:.78rem!important; line-height:1.55!important;
}
html body.pt101 .wc-block-checkout__terms a,
html body.pt101 .wc-block-checkout__privacy-policy a { color:rgba(124,110,245,.8)!important; }

/* ══ 9. ACTIONS ROW ════════════════════════════════════════════ */
/* Hide Return to Cart — cart is bypassed */
html body.pt101 .wc-block-components-checkout-return-to-cart-button { display:none!important; }
html body.pt101 .wc-block-checkout__actions_row,
html body.pt101 .wc-block-checkout__actions { margin-top:24px!important; }

/* PLACE ORDER */
html body.pt101 .wc-block-components-checkout-place-order-button,
html body.pt101 #place_order {
  display:flex!important; align-items:center!important; justify-content:center!important;
  width:100%!important; padding:16px 32px!important;
  background:#7c6ef5!important; background-color:#7c6ef5!important;
  color:#fff!important; font-family:inherit!important; font-size:1.02rem!important;
  font-weight:700!important; letter-spacing:-.01em!important;
  border:none!important; border-radius:12px!important; cursor:pointer!important;
  transition:background .18s,transform .14s,box-shadow .18s!important;
  box-shadow:0 6px 28px rgba(124,110,245,.32)!important;
  -webkit-appearance:none!important;
}
html body.pt101 .wc-block-components-checkout-place-order-button:hover,
html body.pt101 #place_order:hover {
  background:#6358d4!important; transform:translateY(-1px)!important;
  box-shadow:0 10px 36px rgba(124,110,245,.42)!important;
}
html body.pt101 .wc-block-components-checkout-place-order-button:active,
html body.pt101 #place_order:active { transform:translateY(0)!important; }

/* ══ 10. NOTICES ═══════════════════════════════════════════════ */
html body.pt101 .woocommerce-error,
html body.pt101 .woocommerce-message,
html body.pt101 .woocommerce-info,
html body.pt101 .wc-block-components-notice {
  border-radius:10px!important; font-size:.88rem!important;
  padding:12px 16px!important; list-style:none!important; margin:0 0 18px!important;
}
html body.pt101 .woocommerce-error   { background:rgba(224,90,90,.1)!important; border-left:3px solid #e05a5a!important; color:#fbbcbc!important; }
html body.pt101 .woocommerce-message { background:rgba(45,196,124,.08)!important; border-left:3px solid #2dc47c!important; color:#a3f0cc!important; }
html body.pt101 .woocommerce-info    { background:rgba(124,110,245,.08)!important; border-left:3px solid #7c6ef5!important; color:#f0f0f5!important; }

/* ══ 11. ORDER RECEIVED ════════════════════════════════════════ */
html body.pt101 .woocommerce-order-details,
html body.pt101 .woocommerce-customer-details {
  background:#13162a!important; border:1.5px solid rgba(255,255,255,0.07)!important;
  border-radius:14px!important; padding:24px!important; margin-bottom:20px!important;
}
html body.pt101 .woocommerce-thankyou-order-details li,
html body.pt101 .woocommerce-order-overview__order,
html body.pt101 .woocommerce-order-received p,
html body.pt101 .woocommerce-order-received h2 { color:#f0f0f5!important; }
html body.pt101 .woocommerce-order-received h1 {
  font-size:clamp(1.6rem,4vw,2.4rem)!important; font-weight:800!important;
  color:#f0f0f5!important; margin-bottom:12px!important;
}

/* ══ 12. SPINNER ═══════════════════════════════════════════════ */
html body.pt101 .wc-block-components-spinner::after { border-color:#7c6ef5 transparent transparent!important; }

/* ══ 13. MOBILE ════════════════════════════════════════════════ */
@media(max-width:1024px){
  html body.pt101 .wc-block-checkout__main { padding-right:20px!important; }
}
@media(max-width:768px){
  html body.pt101 .wp-block-woocommerce-checkout { padding:36px 16px 80px!important; }
  html body.pt101 .wc-block-checkout__main { padding-right:0!important; }
  html body.pt101 .wc-block-checkout__sidebar { margin-top:28px!important; }
  html body.pt101 .woocommerce-checkout .col2-set { display:block!important; }
  html body.pt101 .woocommerce-checkout .col2-set .col-1,
  html body.pt101 .woocommerce-checkout .col2-set .col-2 { width:100%!important; float:none!important; margin-bottom:28px!important; }
}
@media(max-width:480px){
  html body.pt101 .wp-block-woocommerce-checkout { padding:24px 12px 64px!important; }
  html body.pt101 .wc-block-components-checkout-step__title,
  html body.pt101 .wc-block-checkout h2 { font-size:1rem!important; }
  html body.pt101 .wc-block-checkout__sidebar,
  html body.pt101 .wp-block-woocommerce-checkout-order-summary-block { padding:18px 14px!important; border-radius:12px!important; }
  html body.pt101 .wc-block-components-checkout-place-order-button { padding:14px 20px!important; font-size:.97rem!important; }
}
</style>
    <?php
}, 999 );
