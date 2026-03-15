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
    echo '<link rel="preload" href="' . esc_url( PT101_URI . '/fonts/polysanstrial-bulkywide.otf' ) . '" as="font" type="font/otf" crossorigin>' . "\n";

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
add_filter( 'woocommerce_checkout_registration_required', '__return_true' );  // every buyer needs an account
add_filter( 'woocommerce_checkout_registration_enabled',  '__return_true' );

/* ── POSTAL CODE OPTIONAL ─ */
add_filter( 'woocommerce_default_address_fields', function ( $fields ) {
    if ( isset( $fields['postcode'] ) ) {
        $fields['postcode']['required'] = false;
    }
    return $fields;
} );
// Per-country locale overrides also need this
add_filter( 'woocommerce_get_country_locale', function ( $locale ) {
    foreach ( $locale as $country => $data ) {
        if ( isset( $locale[ $country ]['postcode'] ) ) {
            $locale[ $country ]['postcode']['required'] = false;
        }
    }
    return $locale;
} );

/* ── CHECKOUT: CREATE-PASSWORD FIELD ────────────────────────────────────────
 * Flow:
 *  1. JS injects password + confirm-password fields inside the Contact Info step.
 *  2. On "Place Order" click: JS validates, then POSTs the password via AJAX to
 *     store it in the WC session before the Store API request fires.
 *  3. woocommerce_new_customer_data  → injects the stored password at creation.
 *  4. woocommerce_store_api_checkout_order_processed → fallback: sets password
 *     after the order is processed (Blocks checkout safety net).
 */

/* AJAX: store password in WC session ------------------------------------ */
add_action( 'wp_ajax_nopriv_pt101_store_checkout_pw', 'pt101_ajax_store_checkout_pw' );
add_action( 'wp_ajax_pt101_store_checkout_pw',        'pt101_ajax_store_checkout_pw' );
function pt101_ajax_store_checkout_pw() {
    check_ajax_referer( 'pt101_checkout_pw', 'nonce' );
    $pwd = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
    if ( strlen( $pwd ) < 8 ) {
        wp_send_json_error( array( 'msg' => __( 'Password must be at least 8 characters.', 'prop-trading-101' ) ) );
    }
    WC()->session->set( 'pt101_checkout_password', $pwd ); // kept raw; wp_set_password hashes it
    wp_send_json_success();
}

/* Filter: inject stored password into new-customer data ----------------- */
add_filter( 'woocommerce_new_customer_data', function ( $data ) {
    if ( ! WC()->session ) return $data;
    $pwd = WC()->session->get( 'pt101_checkout_password' );
    if ( $pwd ) {
        $data['user_pass'] = $pwd;
        // Clear immediately so the fallback action below does NOT also call
        // wp_set_password() — that would invalidate the user's auth cookies
        // and log them out right after checkout completes.
        WC()->session->set( 'pt101_checkout_password', null );
    }
    return $data;
} );

/* Fallback: Blocks checkout — only fires when woocommerce_new_customer_data
   did not run (e.g. account already existed and password needs updating).
   Session value is null if the filter already handled it, so this is a no-op
   in the normal flow. */
add_action( 'woocommerce_store_api_checkout_order_processed', function ( $order ) {
    if ( ! WC()->session ) return;
    $pwd = WC()->session->get( 'pt101_checkout_password' );
    if ( ! $pwd || ! $order->get_customer_id() ) return;
    wp_set_password( $pwd, $order->get_customer_id() );
    WC()->session->set( 'pt101_checkout_password', null );
}, 20 );

/* ── AUTO-LOGIN on thank-you page ──────────────────────────────────────
   Runs on 'wp' (before any template rendering) so WooCommerce never
   reaches the "please log in" gate.  We verify the order key from the
   URL so only the person with the real thank-you link can trigger this.
   wp_set_current_user() makes the rest of the current request think the
   user is logged in; wp_set_auth_cookie() persists it for future visits. */
add_action( 'wp', function () {
    if ( is_user_logged_in() ) return;
    if ( ! function_exists( 'is_wc_endpoint_url' ) ) return;
    if ( ! is_wc_endpoint_url( 'order-received' ) ) return;

    $order_id  = absint( get_query_var( 'order-received' ) );
    $order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
    if ( ! $order_id || ! $order_key ) return;

    $order = wc_get_order( $order_id );
    if ( ! $order || ! hash_equals( $order->get_order_key(), $order_key ) ) return;

    $customer_id = $order->get_customer_id();
    if ( ! $customer_id ) return;

    wp_set_current_user( $customer_id );
    wp_set_auth_cookie( $customer_id );
}, 1 );

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
/* ═══════════════════════════════════════════════════════════════
   CHECKOUT & ORDER-RECEIVED — Dark theme
   Uses site design tokens: --bg, --bg-card, --accent, --accent-dim,
   --text-hi, --text-mid, --text-low, --border-dark, --border-mid,
   --r-sm (8px), --r-md (14px), --r-lg (20px), --font, --t (.18s)
   ═══════════════════════════════════════════════════════════════ */

/* ── 1. PAGE SHELL ─────────────────────────────────────────── */
html body.pt101.woocommerce-checkout,
html body.pt101.woocommerce-order-received {
  background: var(--bg) !important;
  color: var(--text-hi) !important;
}
html body.pt101 #page,
html body.pt101 .site-main,
html body.pt101 main { background: transparent !important; }
html body.pt101 .entry-header,
html body.pt101 .woocommerce-page .entry-header { display: none !important; }
html body.pt101 .wp-block-group,
html body.pt101 .wp-block-group__inner-container {
  background: transparent !important;
  max-width: none !important;
  padding: 0 !important;
  margin: 0 !important;
}

/* ── 2. LAYOUT ─────────────────────────────────────────────── */
html body.pt101 .woocommerce,
html body.pt101 .wp-block-woocommerce-checkout {
  max-width: var(--max-w, 1120px);
  margin: 0 auto;
  padding: 48px 48px 80px !important;
}
html body.pt101 .wc-block-checkout__main {
  padding-right: 40px !important;
  /* Remove any inner background — cards handle their own bg */
  background: transparent !important;
}

/* ── 3. ADDRESS FORM GRID ──────────────────────────────────── */
html body.pt101 .wc-block-components-address-form {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 14px 16px !important;
  align-items: start !important;
}
html body.pt101 .wc-block-components-address-form > * { grid-column: span 1 !important; }
/* Full-width fields */
html body.pt101 .wc-block-components-address-form > .wc-block-components-country-input,
html body.pt101 .wc-block-components-address-form > .wc-block-components-address-form__address2-toggle,
html body.pt101 .wc-block-components-address-form > *:has(input[id$="address_1"]),
html body.pt101 .wc-block-components-address-form > *:has(input[id$="address_2"]),
html body.pt101 .wc-block-components-address-form > *:has(input[id$="company"]),
html body.pt101 .wc-block-components-address-form > *[class*="address_1"],
html body.pt101 .wc-block-components-address-form > *[class*="address_2"],
html body.pt101 .wc-block-components-address-form > *[class*="company"] {
  grid-column: span 2 !important;
}
/* "+ Add apartment" link */
html body.pt101 .wc-block-components-address-form__address2-toggle {
  grid-column: span 2 !important;
  background: none !important;
  border: none !important;
  padding: 0 !important;
  color: var(--accent) !important;
  font-size: .82rem !important;
  cursor: pointer;
  text-align: left !important;
}

/* ── 4. FIELD WRAPPERS ─────────────────────────────────────── */
html body.pt101 .wc-block-components-text-input,
html body.pt101 .wc-block-components-select,
html body.pt101 .wc-block-components-state-input {
  position: relative !important;
  z-index: 0 !important;
  isolation: isolate !important;
  margin: 0 !important;
}
/* Country input — no stacking isolation so its dropdown renders on top.
   Elevate z-index only when actively open/focused to avoid overlapping siblings. */
html body.pt101 .wc-block-components-country-input {
  position: relative !important;
  z-index: 1 !important;
  isolation: auto !important;
  margin: 0 !important;
}
html body.pt101 .wc-block-components-country-input:focus-within,
html body.pt101 .wc-block-components-country-input.is-active {
  z-index: 100 !important;
}

/* ── 5. FORM INPUTS ────────────────────────────────────────── */
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
  background: var(--bg-2) !important;
  color: var(--text-hi) !important;
  border: 1.5px solid var(--border-mid) !important;
  border-radius: var(--r-sm) !important;
  padding: 22px 16px 8px !important;
  font-family: var(--font) !important;
  font-size: .95rem !important;
  line-height: 1.4 !important;
  height: 52px !important;
  box-shadow: none !important;
  outline: none !important;
  transition: border-color var(--t), box-shadow var(--t) !important;
  -webkit-appearance: none !important;
  appearance: none !important;
  width: 100% !important;
  box-sizing: border-box !important;
}
html body.pt101 .wc-block-components-text-input textarea {
  height: auto !important;
  min-height: 80px !important;
}
/* Focus ring — matches site's global focus-visible style */
html body.pt101 .wc-block-components-text-input input:focus,
html body.pt101 .wc-block-components-select select:focus,
html body.pt101 .wc-block-components-country-input input:focus,
html body.pt101 .wc-block-components-state-input input:focus,
html body.pt101 input:focus,
html body.pt101 select:focus,
html body.pt101 textarea:focus {
  border-color: var(--accent) !important;
  box-shadow: 0 0 0 3px var(--accent-soft) !important;
  outline: none !important;
}
html body.pt101 input::placeholder,
html body.pt101 textarea::placeholder { color: transparent !important; }

/* Select chevron */
html body.pt101 select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23888b9e' stroke-width='1.6' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") !important;
  background-repeat: no-repeat !important;
  background-position: right 16px center !important;
  padding-right: 44px !important;
}
html body.pt101 option { background: var(--bg-card) !important; color: var(--text-hi) !important; }

/* Country dropdown */
html body.pt101 .wc-block-components-country-input .components-popover,
html body.pt101 .wc-block-components-country-input [role="listbox"],
html body.pt101 .wc-block-components-country-input ul {
  z-index: 9999 !important;
  background: var(--bg-card) !important;
  border: 1.5px solid rgba(124,110,245,.3) !important;
  border-radius: var(--r-sm) !important;
  color: var(--text-hi) !important;
}
html body.pt101 .wc-block-components-country-input [role="option"] {
  color: var(--text-hi) !important;
}
html body.pt101 .wc-block-components-country-input [role="option"]:hover,
html body.pt101 .wc-block-components-country-input [role="option"][aria-selected="true"] {
  background: var(--accent-soft) !important;
  color: #fff !important;
}

/* ── 6. FLOATING LABELS ────────────────────────────────────── */
html body.pt101 .wc-block-components-text-input label,
html body.pt101 .wc-block-components-select label,
html body.pt101 .wc-block-components-country-input label,
html body.pt101 .wc-block-components-state-input label {
  position: absolute !important;
  top: 50% !important;
  left: 17px !important;
  transform: translateY(-50%) !important;
  color: var(--text-low) !important;
  font-size: .86rem !important;
  font-weight: 400 !important;
  pointer-events: none !important;
  transition: top .15s, font-size .15s, transform .15s, color .15s !important;
  white-space: nowrap !important;
  z-index: 1 !important;
  line-height: 1 !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
}
/* Active / filled */
html body.pt101 .wc-block-components-text-input.is-active label,
html body.pt101 .wc-block-components-select.is-active label,
html body.pt101 .wc-block-components-country-input.is-active label,
html body.pt101 .wc-block-components-state-input.is-active label {
  top: 9px !important;
  transform: none !important;
  font-size: .65rem !important;
  color: var(--text-low) !important;
  letter-spacing: .06em !important;
  text-transform: uppercase !important;
}
/* Country/state combobox always has a value — force label up */
html body.pt101 .wc-block-components-country-input label,
html body.pt101 .wc-block-components-state-input label {
  top: 9px !important;
  transform: none !important;
  font-size: .65rem !important;
  color: var(--text-low) !important;
  letter-spacing: .06em !important;
  text-transform: uppercase !important;
}

/* Checkboxes */
html body.pt101 .wc-block-components-checkbox .wc-block-components-checkbox__label,
html body.pt101 .wc-block-components-checkbox__label {
  color: var(--text-mid) !important;
  font-size: .86rem !important;
  font-weight: 400 !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
}
html body.pt101 .wc-block-components-checkbox__input {
  accent-color: var(--accent) !important;
}

/* ── 7. SECTION HEADINGS ───────────────────────────────────── */
html body.pt101 .wc-block-components-checkout-step__title,
html body.pt101 .wc-block-checkout h2,
html body.pt101 .wc-block-checkout h3,
html body.pt101 .woocommerce h2,
html body.pt101 .woocommerce h3 {
  color: var(--text-hi) !important;
  font-family: var(--font) !important;
  font-size: 1.05rem !important;
  font-weight: 700 !important;
  letter-spacing: -.025em !important;
  margin-bottom: 18px !important;
}
/* Hide step numbers — cleaner look without numbered circles */
html body.pt101 .wc-block-components-checkout-step__number {
  display: none !important;
}
/* Each checkout step — card-style container */
html body.pt101 .wc-block-components-checkout-step {
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  padding: 24px 28px !important;
  margin-bottom: 20px !important;
  overflow: visible !important;
}
html body.pt101 .wc-block-components-checkout-step:last-child {
  margin-bottom: 0 !important;
}

/* ── Merge Contact Information + Billing Address into one unified card ──
   Two layers: :has() CSS (modern browsers) + JS (guaranteed for all).
   The wp-block-* wrapper classes are only in the editor HTML; React strips
   them on the frontend, so we target by field content instead. */

/* Step that contains the email field → no bottom border/radius */
html body.pt101 .wc-block-components-checkout-step:has(input[autocomplete="email"]),
html body.pt101 .wc-block-components-checkout-step:has(input[type="email"]) {
  border-bottom-left-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
  border-bottom: none !important;
  margin-bottom: 0 !important;
}
/* Hide the standalone Country/Region step (separate card above billing form) */
html body.pt101 .wc-block-components-checkout-step:has(.wc-block-components-country-input):not(:has(.wc-block-components-address-form)) {
  display: none !important;
}
/* Step that contains the address form → no top border/radius */
html body.pt101 .wc-block-components-checkout-step:has(.wc-block-components-address-form) {
  border-top-left-radius: 0 !important;
  border-top-right-radius: 0 !important;
  border-top: 1px solid var(--border-dark) !important;
}
html body.pt101 .wc-block-components-checkout-step:has(.wc-block-components-address-form) .wc-block-components-checkout-step__title {
  display: none !important;
}

/* ── Remove WooCommerce "Secure checkout · SSL encrypted" trust line ─────── */
html body.pt101 .wc-block-components-checkout-place-order__description,
html body.pt101 .wc-block-checkout__sidebar-compatibility-notice,
html body.pt101 .wc-block-checkout__sidebar > p,
html body.pt101 .wc-block-components-payment-methods__save-card-info ~ p { display: none !important; }
/* Allow country dropdown to escape all parent containers */
html body.pt101 .wc-block-components-checkout-step__content,
html body.pt101 .wc-block-components-address-form {
  overflow: visible !important;
}

/* ── Address card (saved address summary view) ─────────────── */
html body.pt101 .wc-block-components-address-card,
html body.pt101 .wc-block-components-address-card * {
  color: var(--text-hi) !important;
}
html body.pt101 .wc-block-components-address-card {
  background: var(--bg-2) !important;
  border: 1px solid var(--border-mid) !important;
  border-radius: var(--r-sm) !important;
  padding: 16px !important;
}
html body.pt101 .wc-block-components-address-card__address-info {
  color: var(--text-mid) !important;
}
html body.pt101 .wc-block-components-address-card__edit {
  color: var(--accent) !important;
  font-weight: 600 !important;
  font-size: .85rem !important;
}

/* ── 8. HIDE EMPTY / UNNECESSARY SECTIONS ──────────────────── */
html body.pt101 .wc-block-checkout__add-note,
html body.pt101 .woocommerce-additional-fields { display: none !important; }
/* Hide empty checkout steps (e.g. shipping/payment when product is free) */
html body.pt101 .wc-block-components-checkout-step:empty,
html body.pt101 .wc-block-checkout__payment-method:empty,
html body.pt101 .wp-block-woocommerce-checkout-shipping-methods-block:empty,
html body.pt101 .wp-block-woocommerce-checkout-shipping-method-block:empty { display: none !important; }
/* Steps with no visible content — collapse them via :has() */
html body.pt101 .wc-block-components-checkout-step:has(> .wc-block-components-checkout-step__content:empty) {
  display: none !important;
}

/* ── 9. ORDER SUMMARY SIDEBAR ──────────────────────────────── */
html body.pt101 .wc-block-checkout__sidebar {
  position: sticky !important;
  top: 88px !important;
}
html body.pt101 .wc-block-checkout__sidebar,
html body.pt101 .wp-block-woocommerce-checkout-order-summary-block {
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  padding: 24px !important;
  overflow: hidden !important;
}
html body.pt101 .wc-block-components-order-summary { background: transparent !important; }
html body.pt101 .wc-block-components-order-summary__title,
html body.pt101 .wc-block-checkout__sidebar h2 {
  color: var(--text-hi) !important;
  font-size: 1rem !important;
  font-weight: 700 !important;
  margin-bottom: 16px !important;
}
html body.pt101 .wc-block-components-order-summary-item { padding: 14px 0 !important; }
html body.pt101 .wc-block-components-order-summary-item__name {
  color: var(--text-hi) !important;
  font-size: .9rem !important;
  font-weight: 600 !important;
  line-height: 1.35 !important;
}
html body.pt101 .wc-block-components-order-summary-item__description {
  color: var(--text-low) !important;
  font-size: .78rem !important;
  line-height: 1.4 !important;
  display: -webkit-box !important;
  -webkit-line-clamp: 2 !important;
  -webkit-box-orient: vertical !important;
  overflow: hidden !important;
}
html body.pt101 .wc-block-components-order-summary-item__quantity {
  background: var(--accent) !important;
  color: #fff !important;
  border: 2px solid var(--bg) !important;
  border-radius: 50% !important;
  font-size: .7rem !important;
  font-weight: 700 !important;
  min-width: 20px !important;
  min-height: 20px !important;
}
html body.pt101 .wc-block-components-order-summary-item__total-price {
  color: var(--text-hi) !important;
  font-weight: 600 !important;
}
/* Totals rows */
html body.pt101 .wc-block-components-totals-item {
  border-top: 1px solid var(--border-dark) !important;
  padding: 12px 0 !important;
}
html body.pt101 .wc-block-components-totals-item__label,
html body.pt101 .wc-block-components-totals-item__value,
html body.pt101 .wc-block-components-totals-item span {
  color: var(--text-mid) !important;
  font-size: .88rem !important;
}
html body.pt101 .wc-block-components-totals-footer-item {
  border-top: 2px solid var(--border-mid) !important;
  padding-top: 14px !important;
  margin-top: 4px !important;
}
html body.pt101 .wc-block-components-totals-footer-item .wc-block-components-totals-item__label,
html body.pt101 .wc-block-components-totals-footer-item .wc-block-components-totals-item__value {
  color: var(--text-hi) !important;
  font-size: 1.05rem !important;
  font-weight: 700 !important;
}
/* Coupon toggle */
html body.pt101 .wc-block-components-totals-coupon details > summary {
  color: var(--accent) !important;
  font-size: .82rem !important;
  cursor: pointer;
}
html body.pt101 .wc-block-components-totals-coupon__button {
  background: var(--accent) !important;
  color: #fff !important;
  border: none !important;
  border-radius: var(--r-sm) !important;
  font-weight: 600 !important;
}
html body.pt101 .wc-block-components-totals-coupon input {
  height: 42px !important;
  padding: 0 14px !important;
}

/* ── 10. PAYMENT BLOCK ─────────────────────────────────────── */
html body.pt101 .wp-block-woocommerce-checkout-payment-block {
  display: block !important;
  visibility: visible !important;
}
html body.pt101 .wc-block-components-payment-methods,
html body.pt101 .wc-block-checkout__payment-method {
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-md) !important;
  padding: 20px !important;
  color: var(--text-hi) !important;
}
html body.pt101 .wc-block-components-payment-methods *,
html body.pt101 .wc-block-checkout__payment-method * {
  color: var(--text-mid) !important;
}
html body.pt101 .wc-block-components-payment-method-label__name,
html body.pt101 .wc-block-components-radio-control__label,
html body.pt101 .wc-block-components-payment-methods label {
  color: var(--text-hi) !important;
  font-size: .9rem !important;
}
html body.pt101 .wc-block-components-radio-control__input {
  accent-color: var(--accent) !important;
}
html body.pt101 .wc-block-components-radio-control-accordion-content {
  background: var(--bg) !important;
  border-radius: var(--r-sm) !important;
  color: var(--text-mid) !important;
  font-size: .85rem !important;
  padding: 12px !important;
}
html body.pt101 .wc-block-components-payment-method-label {
  color: var(--text-mid) !important;
}
html body.pt101 .wc-block-gateway-container {
  color: var(--text-hi) !important;
}

/* ── Express checkout moved into payment block ─────────────── */
/* Hide the original "Express Checkout" top-level title & outer wrapper border */
html body.pt101 .wp-block-woocommerce-checkout-express-payment-block .wc-block-components-express-payment__title-container {
  display: none !important;
}
/* Remove the "Or continue below" rule that used to separate express from the form */
html body.pt101 .wc-block-components-express-payment-continue-rule {
  display: none !important;
}
/* Style express buttons inside the payment card */
html body.pt101 .wp-block-woocommerce-checkout-payment-block .wp-block-woocommerce-checkout-express-payment-block {
  border-bottom: 1px solid var(--border-dark) !important;
  margin-bottom: 16px !important;
  padding-bottom: 16px !important;
}
html body.pt101 .wp-block-woocommerce-checkout-payment-block .wc-block-components-express-payment {
  margin: 0 !important;
}

/* ── Course switcher (sidebar) ─────────────────────────────── */
html body.pt101 .pt101-switcher {
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  padding: 20px 24px 24px !important;
  margin-bottom: 16px !important;
}
html body.pt101 .pt101-switcher__title {
  color: var(--text-low) !important;
  font-size: .72rem !important;
  font-weight: 700 !important;
  letter-spacing: .1em !important;
  text-transform: uppercase !important;
  margin: 0 0 14px !important;
}
html body.pt101 .pt101-switcher__grid {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 8px !important;
}
html body.pt101 .pt101-switcher__btn {
  background: rgba(255,255,255,.04) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-sm) !important;
  color: var(--text-mid) !important;
  cursor: pointer !important;
  font-family: var(--font) !important;
  font-size: .8rem !important;
  font-weight: 500 !important;
  line-height: 1.3 !important;
  padding: 10px 12px !important;
  text-align: left !important;
  transition: background var(--t), border-color var(--t), color var(--t) !important;
  width: 100% !important;
}
html body.pt101 .pt101-switcher__btn:hover {
  border-color: var(--accent) !important;
  color: var(--text-hi) !important;
}
html body.pt101 .pt101-switcher__btn.active {
  background: rgba(124,110,245,.13) !important;
  border-color: var(--accent) !important;
  color: #fff !important;
  font-weight: 600 !important;
}
html body.pt101 .pt101-switcher__btn-name { display: block !important; }
html body.pt101 .pt101-switcher__btn-price {
  color: var(--text-low) !important;
  display: block !important;
  font-size: .72rem !important;
  margin-top: 3px !important;
}
html body.pt101 .pt101-switcher__btn.active .pt101-switcher__btn-price {
  color: var(--accent) !important;
}
html body.pt101 .pt101-switcher--busy {
  opacity: .6 !important;
  pointer-events: none !important;
}

/* ── 11. TERMS / PRIVACY (default WC text — hidden, replaced by custom checkboxes) ── */
html body.pt101 .wc-block-checkout__terms,
html body.pt101 .wc-block-checkout__privacy-policy {
  display: none !important;
}

/* ── CONSENT CHECKBOXES ────────────────────────────────────── */
html body.pt101 .pt101-consent {
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  padding: 24px 28px !important;
  margin-bottom: 20px !important;
}
html body.pt101 .pt101-consent__item {
  display: flex !important;
  gap: 14px !important;
  align-items: flex-start !important;
  cursor: pointer !important;
  padding: 0 !important;
  margin-bottom: 20px !important;
}
html body.pt101 .pt101-consent__item:last-child {
  margin-bottom: 0 !important;
}
html body.pt101 .pt101-consent__item input[type="checkbox"] {
  flex-shrink: 0 !important;
  width: 20px !important;
  height: 20px !important;
  margin-top: 2px !important;
  accent-color: var(--accent) !important;
  border: 1.5px solid var(--border-mid) !important;
  border-radius: 4px !important;
  background: var(--bg-2) !important;
  cursor: pointer !important;
  -webkit-appearance: auto !important;
  appearance: auto !important;
}
html body.pt101 .pt101-consent__item span {
  color: var(--text-mid) !important;
  font-size: .76rem !important;
  line-height: 1.55 !important;
  text-transform: none !important;
  letter-spacing: normal !important;
}
html body.pt101 .pt101-consent,
html body.pt101 .pt101-consent * {
  text-transform: none !important;
  letter-spacing: normal !important;
}
html body.pt101 .pt101-consent__item strong {
  color: var(--text-hi) !important;
  font-weight: 700 !important;
}
html body.pt101 .pt101-consent__item a {
  color: var(--accent) !important;
  text-decoration: underline !important;
}
html body.pt101 .pt101-consent__item a:hover {
  color: #fff !important;
}
/* Error state — only when JS adds this class after failed submit */
html body.pt101 .pt101-consent__item.pt101-consent__error {
  outline: 2px solid #e05a5a !important;
  outline-offset: 4px !important;
  border-radius: 4px !important;
}
html body.pt101 .pt101-consent__item.pt101-consent__error input[type="checkbox"] {
  outline: 2px solid #e05a5a !important;
}
/* ── 11b. CREATE PASSWORD SECTION ─────────────────────────── */
/* Standalone card (strategy 1: after contact step) */
html body.pt101 .pt101-password-card.wc-block-components-checkout-step {
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  padding: 24px 28px !important;
  margin-bottom: 16px !important;
}
html body.pt101 .pt101-password-card .wc-block-components-checkout-step__title {
  margin-bottom: 18px !important;
}
/* Inline section (strategy 2: inside contact step content) */
html body.pt101 .pt101-password-section {
  margin-top: 20px !important;
  padding-top: 20px !important;
  border-top: 1px solid var(--border-dark) !important;
}
html body.pt101 .pt101-password-section__heading {
  color: var(--text-mid) !important;
  font-size: .72rem !important;
  font-weight: 600 !important;
  letter-spacing: .07em !important;
  text-transform: uppercase !important;
  margin: 0 0 14px !important;
}
html body.pt101 .pt101-password-row {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 14px !important;
}
html body.pt101 .pt101-pw-hint {
  color: var(--text-low) !important;
  font-size: .73rem !important;
  margin: 10px 0 0 !important;
  line-height: 1.5 !important;
}
html body.pt101 .pt101-pw-error {
  color: #e05a5a !important;
  font-size: .8rem !important;
  margin: 8px 0 0 !important;
  line-height: 1.4 !important;
}
@media (max-width: 480px) {
  html body.pt101 .pt101-password-row {
    grid-template-columns: 1fr !important;
  }
}

/* Remove red border from consent container itself */
html body.pt101 .pt101-consent {
  outline: none !important;
}

/* ── 12. PLACE ORDER ───────────────────────────────────────── */
html body.pt101 .wc-block-components-checkout-return-to-cart-button { display: none !important; }
html body.pt101 .wc-block-checkout__actions_row,
html body.pt101 .wc-block-checkout__actions {
  margin-top: 20px !important;
  padding: 24px 28px !important;
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  border-top: none !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: stretch !important;
}
html body.pt101 .wc-block-components-checkout-place-order-button,
html body.pt101 #place_order {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 100% !important;
  padding: 16px 32px !important;
  min-height: 52px !important;
  background: var(--accent) !important;
  color: #fff !important;
  font-family: var(--font) !important;
  font-size: 1rem !important;
  font-weight: 600 !important;
  letter-spacing: -.01em !important;
  border: none !important;
  border-radius: 100px !important;
  cursor: pointer !important;
  transition: background var(--t), transform .14s, box-shadow var(--t) !important;
  box-shadow: 0 8px 28px var(--accent-glow) !important;
  -webkit-appearance: none !important;
}
html body.pt101 .wc-block-components-checkout-place-order-button:hover,
html body.pt101 #place_order:hover {
  background: var(--accent-dim) !important;
  transform: translateY(-2px) !important;
  box-shadow: 0 12px 36px var(--accent-glow) !important;
}
html body.pt101 .wc-block-components-checkout-place-order-button:active,
html body.pt101 #place_order:active {
  transform: translateY(0) !important;
}

/* ── 13. NOTICES ────────────────────────────────────────────── */
html body.pt101 .woocommerce-error,
html body.pt101 .woocommerce-message,
html body.pt101 .woocommerce-info,
html body.pt101 .wc-block-components-notice {
  border-radius: var(--r-sm) !important;
  font-size: .86rem !important;
  padding: 12px 16px !important;
  list-style: none !important;
  margin: 0 0 16px !important;
}
html body.pt101 .woocommerce-error   { background: rgba(224,90,90,.08) !important; border-left: 3px solid #e05a5a !important; color: #fbbcbc !important; }
html body.pt101 .woocommerce-message { background: rgba(45,196,124,.06) !important; border-left: 3px solid #2dc47c !important; color: #a3f0cc !important; }
html body.pt101 .woocommerce-info    { background: var(--accent-soft) !important; border-left: 3px solid var(--accent) !important; color: var(--text-hi) !important; }

/* ── 14. SPINNER ────────────────────────────────────────────── */
html body.pt101 .wc-block-components-spinner::after { border-color: var(--accent) transparent transparent !important; }

/* ── 15. RESPONSIVE ────────────────────────────────────────── */

/* Tablet */
@media (max-width: 1024px) {
  html body.pt101 .wc-block-checkout__main { padding-right: 24px !important; }
  html body.pt101 .woocommerce,
  html body.pt101 .wp-block-woocommerce-checkout { padding: 40px 24px 72px !important; }
}

/* Mobile */
@media (max-width: 768px) {
  /* Page container */
  html body.pt101 .woocommerce,
  html body.pt101 .wp-block-woocommerce-checkout { padding: 24px 16px 64px !important; }

  /* Main + sidebar stack vertically */
  html body.pt101 .wc-block-checkout__main { padding-right: 0 !important; }
  html body.pt101 .wc-block-checkout__sidebar {
    margin-top: 20px !important;
    position: static !important;
  }
  /* Hide the WC Blocks mobile order summary duplicate — WC Blocks renders
     the order summary both inside .wc-block-checkout__main (toggler/panel)
     and as .wc-block-checkout__sidebar below the form. Keep only the sidebar. */
  html body.pt101 .wc-block-checkout__sidebar-toggler,
  html body.pt101 .wc-block-checkout__sidebar-toggler-open,
  html body.pt101 .wc-block-checkout__order-summary-step,
  html body.pt101 .wc-block-checkout__order-summary-heading,
  html body.pt101 .wc-block-checkout__main .wp-block-woocommerce-checkout-order-summary-block { display: none !important; }

  /* Card containers — reduce padding */
  html body.pt101 .wc-block-components-checkout-step {
    padding: 20px 18px !important;
    margin-bottom: 14px !important;
    border-radius: var(--r-md) !important;
  }

  /* Address form — true single column.
     The desktop "span 2" rules for specific classes have higher specificity
     than "> *", so they win even on mobile and force an implicit 2-column
     grid (causing city/postcode/state to share a row). Override each one
     explicitly inside this media query so they all become span 1. */
  html body.pt101 .wc-block-components-address-form {
    grid-template-columns: 1fr !important;
    gap: 12px !important;
  }
  html body.pt101 .wc-block-components-address-form > *,
  html body.pt101 .wc-block-components-address-form > .wc-block-components-country-input,
  html body.pt101 .wc-block-components-address-form > .wc-block-components-state-input,
  html body.pt101 .wc-block-components-address-form > .wc-block-components-address-form__address2-toggle,
  html body.pt101 .wc-block-components-address-form > *:has(input[id$="address_1"]),
  html body.pt101 .wc-block-components-address-form > *:has(input[id$="address_2"]),
  html body.pt101 .wc-block-components-address-form > *:has(input[id$="company"]),
  html body.pt101 .wc-block-components-address-form > *:has(input[id$="city"]),
  html body.pt101 .wc-block-components-address-form > *:has(input[id$="postcode"]),
  html body.pt101 .wc-block-components-address-form > *:has(input[id$="phone"]),
  html body.pt101 .wc-block-components-address-form > *[class*="address_1"],
  html body.pt101 .wc-block-components-address-form > *[class*="address_2"],
  html body.pt101 .wc-block-components-address-form > *[class*="company"],
  html body.pt101 .wc-block-components-address-form > *[class*="city"],
  html body.pt101 .wc-block-components-address-form > *[class*="postcode"],
  html body.pt101 .wc-block-components-address-form > *[class*="phone"] {
    grid-column: span 1 !important;
  }

  /* Sidebar — reduce padding/radius */
  html body.pt101 .wc-block-checkout__sidebar,
  html body.pt101 .wp-block-woocommerce-checkout-order-summary-block {
    padding: 20px 18px !important;
    border-radius: var(--r-md) !important;
  }

  /* Course switcher — mobile: single-column rows */
  html body.pt101 .pt101-switcher {
    padding: 16px 18px 20px !important;
    border-radius: var(--r-md) !important;
    margin-bottom: 14px !important;
  }
  html body.pt101 .pt101-switcher__grid {
    grid-template-columns: 1fr !important;
    gap: 7px !important;
  }
  html body.pt101 .pt101-switcher__btn {
    padding: 11px 14px !important;
    font-size: .85rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
  }
  html body.pt101 .pt101-switcher__btn-price {
    margin-top: 0 !important;
    font-size: .8rem !important;
  }

  /* Place Order card */
  html body.pt101 .wc-block-checkout__actions_row,
  html body.pt101 .wc-block-checkout__actions {
    padding: 20px 18px !important;
    margin-top: 14px !important;
    border-radius: var(--r-md) !important;
  }
  html body.pt101 .wc-block-components-checkout-place-order-button,
  html body.pt101 #place_order {
    padding: 14px 24px !important;
    font-size: .95rem !important;
    min-height: 48px !important;
  }

  /* Consent checkboxes */
  html body.pt101 .pt101-consent {
    padding: 20px 18px !important;
    margin-bottom: 14px !important;
    border-radius: var(--r-md) !important;
  }
  html body.pt101 .pt101-consent__item {
    gap: 12px !important;
  }
  html body.pt101 .pt101-consent__item span {
    font-size: .75rem !important;
  }

  /* Address card (saved address) */
  html body.pt101 .wc-block-components-address-card {
    padding: 14px !important;
  }

  /* Form inputs — slightly shorter on mobile */
  html body.pt101 .wc-block-components-text-input input,
  html body.pt101 .wc-block-components-country-input input,
  html body.pt101 .wc-block-components-state-input input,
  html body.pt101 input[type="text"],
  html body.pt101 input[type="email"],
  html body.pt101 input[type="tel"],
  html body.pt101 select {
    height: 50px !important;
    font-size: .9rem !important;
  }

  /* Headings */
  html body.pt101 .wc-block-components-checkout-step__title,
  html body.pt101 .wc-block-checkout h2 { font-size: .95rem !important; }

  /* Classic checkout columns */
  html body.pt101 .woocommerce-checkout .col2-set { display: block !important; }
  html body.pt101 .woocommerce-checkout .col2-set .col-1,
  html body.pt101 .woocommerce-checkout .col2-set .col-2 {
    width: 100% !important;
    float: none !important;
    margin-bottom: 20px !important;
  }

  /* Thank-you: next steps — single column */
  html body.pt101 .pt101-next-steps {
    padding: 20px 18px !important;
    border-radius: var(--r-md) !important;
  }
  html body.pt101 .pt101-next-steps__grid { grid-template-columns: 1fr !important; }
  html body.pt101 .pt101-next-steps__actions {
    flex-direction: column !important;
  }
  html body.pt101 .pt101-btn-primary,
  html body.pt101 .pt101-btn-secondary {
    width: 100% !important;
    text-align: center !important;
    padding: 14px 20px !important;
  }

  /* Thank-you: success banner */
  html body.pt101 .woocommerce-thankyou-order-received {
    padding: 36px 20px 32px !important;
    border-radius: var(--r-md) !important;
  }
  html body.pt101 .pt101-success-icon {
    width: 52px !important;
    height: 52px !important;
    font-size: 1.4rem !important;
  }
}

/* Small phones */
@media (max-width: 480px) {
  html body.pt101 .woocommerce,
  html body.pt101 .wp-block-woocommerce-checkout { padding: 16px 12px 56px !important; }

  /* Cards — tighter padding */
  html body.pt101 .wc-block-components-checkout-step {
    padding: 16px 14px !important;
    margin-bottom: 12px !important;
    border-radius: var(--r-sm) !important;
  }
  html body.pt101 .wc-block-checkout__sidebar,
  html body.pt101 .wp-block-woocommerce-checkout-order-summary-block {
    padding: 16px 14px !important;
    border-radius: var(--r-sm) !important;
  }

  /* Course switcher — small mobile */
  html body.pt101 .pt101-switcher {
    padding: 14px 14px 16px !important;
    border-radius: var(--r-sm) !important;
    margin-bottom: 12px !important;
  }
  html body.pt101 .pt101-switcher__grid {
    grid-template-columns: 1fr !important;
    gap: 6px !important;
  }
  html body.pt101 .pt101-switcher__btn {
    padding: 10px 12px !important;
    font-size: .82rem !important;
  }
  html body.pt101 .wc-block-checkout__actions_row,
  html body.pt101 .wc-block-checkout__actions {
    padding: 16px 14px !important;
    border-radius: var(--r-sm) !important;
  }
  html body.pt101 .pt101-consent {
    padding: 16px 14px !important;
    border-radius: var(--r-sm) !important;
  }
  html body.pt101 .pt101-consent__item span {
    font-size: .73rem !important;
  }
  html body.pt101 .pt101-next-steps {
    padding: 16px 14px !important;
    border-radius: var(--r-sm) !important;
  }

  html body.pt101 .woocommerce-thankyou-order-received {
    padding: 32px 16px 28px !important;
    font-size: 1.15rem !important;
  }
}

/* ── Hide login notice + form on order-received (CSS fallback for auto-login) ── */
html body.pt101.woocommerce-order-received .woocommerce-info,
html body.pt101.woocommerce-order-received .woocommerce-form-login-toggle,
html body.pt101.woocommerce-order-received .woocommerce-form.woocommerce-form-login,
html body.pt101.woocommerce-order-received .u-column1.col-1 { display: none !important; }

/* ══ ORDER RECEIVED ════════════════════════════════════════════ */
html body.pt101.woocommerce-order-received .woocommerce { max-width: 860px !important; padding-top: 40px !important; }

/* Success Banner */
html body.pt101 .woocommerce-thankyou-order-received {
  display: block !important;
  text-align: center !important;
  padding: 48px 32px 40px !important;
  margin: 0 0 28px !important;
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  font-size: clamp(1.25rem, 3vw, 1.6rem) !important;
  font-weight: 800 !important;
  color: var(--text-hi) !important;
  letter-spacing: -.025em !important;
  line-height: 1.3 !important;
  position: relative !important;
  overflow: hidden !important;
}
html body.pt101 .woocommerce-thankyou-order-received::before {
  content: '' !important;
  position: absolute !important;
  top: -80px !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  width: 300px !important;
  height: 300px !important;
  background: radial-gradient(circle, var(--accent-soft) 0%, transparent 70%) !important;
  pointer-events: none !important;
}
html body.pt101 .pt101-success-icon {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 64px !important;
  height: 64px !important;
  background: var(--accent) !important;
  border-radius: 50% !important;
  font-size: 1.8rem !important;
  margin: 0 auto 18px !important;
  box-shadow: 0 8px 28px var(--accent-glow) !important;
  position: relative !important;
  z-index: 1 !important;
}
html body.pt101 .pt101-success-sub {
  display: block !important;
  font-size: .9rem !important;
  font-weight: 400 !important;
  color: var(--text-mid) !important;
  margin-top: 8px !important;
  letter-spacing: 0 !important;
}

/* Order Meta Bar — hidden (layout too fragile at varying viewport widths) */
html body.pt101 .woocommerce-order-overview,
html body.pt101 .woocommerce-thankyou-order-details { display: none !important; }

/* Order Details & Billing Address — hidden on thank you page */
html body.pt101 .woocommerce-order-details,
html body.pt101 .woocommerce-customer-details {
  display: none !important;
}

/* Next Steps (JS-injected) */
html body.pt101 .pt101-next-steps {
  background: var(--bg-card) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-lg) !important;
  padding: 28px 24px !important;
  margin-top: 24px !important;
}
html body.pt101 .pt101-next-steps__heading {
  color: var(--text-hi) !important; font-size: 1.05rem !important; font-weight: 700 !important;
  margin: 0 0 20px !important; letter-spacing: -.025em !important;
}
html body.pt101 .pt101-next-steps__grid {
  display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 14px !important; margin-bottom: 24px !important;
}
html body.pt101 .pt101-step-item {
  display: flex !important; gap: 12px !important; align-items: flex-start !important;
  background: rgba(255,255,255,.03) !important;
  border: 1px solid var(--border-dark) !important;
  border-radius: var(--r-sm) !important; padding: 16px !important;
}
html body.pt101 .pt101-step-num {
  flex-shrink: 0 !important;
  width: 28px !important; height: 28px !important; border-radius: 50% !important;
  background: var(--accent) !important; color: #fff !important;
  display: flex !important; align-items: center !important; justify-content: center !important;
  font-size: .72rem !important; font-weight: 700 !important;
}
html body.pt101 .pt101-step-item strong {
  display: block !important; color: var(--text-hi) !important;
  font-size: .88rem !important; font-weight: 600 !important; margin-bottom: 2px !important;
}
html body.pt101 .pt101-step-item p {
  margin: 0 !important; color: var(--text-low) !important; font-size: .8rem !important; line-height: 1.5 !important;
}
html body.pt101 .pt101-next-steps__actions {
  display: flex !important; gap: 12px !important; flex-wrap: wrap !important;
}
html body.pt101 .pt101-btn-primary {
  display: inline-flex !important; align-items: center !important; justify-content: center !important;
  padding: 12px 26px !important; background: var(--accent) !important;
  color: #fff !important; font-family: var(--font) !important; font-size: .88rem !important;
  font-weight: 600 !important; border-radius: 100px !important; text-decoration: none !important;
  box-shadow: 0 8px 28px var(--accent-glow) !important;
  transition: background var(--t), transform .14s !important;
  white-space: nowrap !important;
}
html body.pt101 .pt101-btn-primary:hover { background: var(--accent-dim) !important; transform: translateY(-2px) !important; color: #fff !important; }
html body.pt101 .pt101-btn-secondary {
  display: inline-flex !important; align-items: center !important; justify-content: center !important;
  padding: 12px 26px !important;
  background: rgba(255,255,255,.07) !important;
  border: 1px solid var(--border-dark) !important;
  color: var(--text-hi) !important; font-family: var(--font) !important; font-size: .88rem !important;
  font-weight: 600 !important; border-radius: 100px !important; text-decoration: none !important;
  transition: background var(--t), color var(--t) !important;
  white-space: nowrap !important;
}
html body.pt101 .pt101-btn-secondary:hover { background: rgba(255,255,255,.12) !important; color: #fff !important; }

/* Generic text */
html body.pt101 .woocommerce-order-received p { color: var(--text-mid) !important; }
html body.pt101 .woocommerce-order-received h2 { color: var(--text-hi) !important; }
html body.pt101 .woocommerce-order-received h1 {
  font-size: clamp(1.6rem, 4vw, 2.2rem) !important; font-weight: 800 !important;
  color: var(--text-hi) !important; margin-bottom: 12px !important;
}
</style>
    <?php
}, 999 );

/* ── CHECKOUT PAGE: JS ENHANCEMENT ─────────────────────────────
 * Hides empty checkout steps, injects password field + consent checkboxes.
 */
add_action( 'wp_footer', function () {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) return;
    if ( is_wc_endpoint_url( 'order-received' ) ) return;
    $terms_url   = get_permalink( wc_terms_and_conditions_page_id() ) ?: home_url( '/terms-and-conditions' );
    $privacy_url = get_privacy_policy_url() ?: home_url( '/privacy-policy' );
    ?>
<script>
(function(){
  var TERMS_URL    = <?php echo wp_json_encode( esc_url( $terms_url ) ); ?>;
  var PRIVACY_URL  = <?php echo wp_json_encode( esc_url( $privacy_url ) ); ?>;
  var PT101_AJAX   = <?php echo wp_json_encode( esc_url( admin_url( 'admin-ajax.php' ) ) ); ?>;
  var PT101_NONCE  = <?php echo wp_json_encode( wp_create_nonce( 'pt101_checkout_pw' ) ); ?>;
  var PT101_GUEST  = <?php echo is_user_logged_in() ? 'false' : 'true'; ?>; // show pw field only for guests

  <?php
  // Current product in cart (used to pre-select the active course)
  $pt101_current_pid = 0;
  if ( function_exists( 'WC' ) && WC()->cart ) {
      foreach ( WC()->cart->get_cart() as $_item ) {
          $pt101_current_pid = (int) $_item['product_id'];
          break;
      }
  }
  $pt101_courses = [
      [ 'id' => 158, 'name' => 'Intro to Trading',        'price' => 'Free' ],
      [ 'id' => 256, 'name' => 'Trading Foundations',     'price' => '$89'  ],
      [ 'id' => 253, 'name' => 'Market Mechanics',        'price' => '$299' ],
      [ 'id' => 254, 'name' => 'Strategy Development',    'price' => '$399' ],
      [ 'id' => 207, 'name' => 'Mastering Pro Trading',   'price' => '$499' ],
  ];
  ?>
  var PT101_SWAP_NONCE      = <?php echo wp_json_encode( wp_create_nonce( 'pt101_swap_course' ) ); ?>;
  var PT101_CURRENT_PRODUCT = <?php echo (int) $pt101_current_pid; ?>;
  var PT101_COURSES         = <?php echo wp_json_encode( $pt101_courses ); ?>;

  /* ── Password field injection ──────────────────────────── */
  function buildPasswordCard(){
    var card = document.createElement('div');
    card.id = 'pt101-password-wrap';
    card.className = 'wc-block-components-checkout-step pt101-password-card';
    card.innerHTML = [
      '<h2 class="wc-block-components-checkout-step__title">Create your password</h2>',
      '<div class="wc-block-components-checkout-step__content">',
        '<div class="pt101-password-row">',
          '<div class="wc-block-components-text-input pt101-pw-field">',
            '<input type="password" id="pt101-password" autocomplete="new-password" placeholder=" " />',
            '<label for="pt101-password">Password</label>',
          '</div>',
          '<div class="wc-block-components-text-input pt101-pw-field">',
            '<input type="password" id="pt101-password-confirm" autocomplete="new-password" placeholder=" " />',
            '<label for="pt101-password-confirm">Confirm Password</label>',
          '</div>',
        '</div>',
        '<p class="pt101-pw-hint">Minimum 8 characters — you\'ll use this to log in to your learning dashboard.</p>',
        '<p class="pt101-pw-error" id="pt101-pw-error" style="display:none;"></p>',
      '</div>'
    ].join('');

    /* Floating label toggle */
    card.querySelectorAll('input').forEach(function(inp){
      function upd(){
        inp.closest('.wc-block-components-text-input')
           .classList.toggle('is-active', inp.value.length > 0 || document.activeElement === inp);
      }
      inp.addEventListener('input', upd);
      inp.addEventListener('focus', upd);
      inp.addEventListener('blur',  upd);
    });
    return card;
  }

  function injectPasswordField(){
    if(!PT101_GUEST) return;
    if(document.getElementById('pt101-password-wrap')) return;

    /* Strategy 1: insert AFTER the billing address block so the merged
       Contact Info + Billing card remains unbroken */
    var billingBlock = document.querySelector('.wp-block-woocommerce-checkout-billing-address-block');
    if(billingBlock && billingBlock.parentNode){
      billingBlock.parentNode.insertBefore(buildPasswordCard(), billingBlock.nextSibling);
      return;
    }

    /* Strategy 2: insert after the step that contains the email field */
    var emailInput = document.querySelector('input[autocomplete="email"], input[type="email"]');
    if(emailInput){
      var contactStep = emailInput.closest('.wc-block-components-checkout-step');
      if(contactStep && contactStep.parentNode){
        contactStep.parentNode.insertBefore(buildPasswordCard(), contactStep.nextSibling);
        return;
      }
    }

    /* Strategy 3: insert before the actions row (last resort) */
    var actionsRow = document.querySelector('.wc-block-checkout__actions_row, .wc-block-checkout__actions');
    if(actionsRow && actionsRow.parentNode){
      actionsRow.parentNode.insertBefore(buildPasswordCard(), actionsRow);
    }
  }

  /* ── Consent + password click handler ─────────────────── */
  var _pwStored = false;

  function bindSubmitHandler(){
    var submitBtn = document.querySelector('.wc-block-components-checkout-place-order-button')
                 || document.querySelector('#place_order');
    if(!submitBtn || submitBtn._pt101bound) return;
    submitBtn._pt101bound = true;

    submitBtn.addEventListener('click', function(e){

      /* 1. Terms consent gate */
      var cb = document.getElementById('pt101-terms-agree');
      if(cb && !cb.checked){
        e.preventDefault(); e.stopImmediatePropagation();
        cb.closest('.pt101-consent__item').classList.add('pt101-consent__error');
        cb.focus();
        return false;
      }

      /* 2. Password gate (guests only) */
      var pwField = document.getElementById('pt101-password');
      if(pwField){
        /* Second click after AJAX — let checkout through */
        if(_pwStored){ _pwStored = false; return; }

        var pw  = pwField.value;
        var pw2 = (document.getElementById('pt101-password-confirm') || {}).value || '';
        var errEl = document.getElementById('pt101-pw-error');

        function showPwErr(msg){
          if(errEl){ errEl.textContent = msg; errEl.style.display = 'block';
            errEl.scrollIntoView({behavior:'smooth', block:'nearest'}); }
        }

        if(!pw || pw.length < 8){
          e.preventDefault(); e.stopImmediatePropagation();
          showPwErr('Password must be at least 8 characters.');
          pwField.focus(); return false;
        }
        if(pw !== pw2){
          e.preventDefault(); e.stopImmediatePropagation();
          showPwErr('Passwords do not match.');
          document.getElementById('pt101-password-confirm').focus(); return false;
        }

        /* Valid — store in session then re-trigger */
        e.preventDefault(); e.stopImmediatePropagation();
        if(errEl) errEl.style.display = 'none';
        var _btn = this;
        _btn.disabled = true;

        fetch(PT101_AJAX, {
          method: 'POST',
          headers: {'Content-Type':'application/x-www-form-urlencoded'},
          body: 'action=pt101_store_checkout_pw'
              + '&nonce='    + encodeURIComponent(PT101_NONCE)
              + '&password=' + encodeURIComponent(pw)
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
          _btn.disabled = false;
          if(data.success){
            _pwStored = true;
            _btn.click(); // re-fire; this time _pwStored=true so we skip the gate
          } else {
            showPwErr((data.data && data.data.msg) || 'Password error. Please try again.');
          }
        })
        .catch(function(){
          _btn.disabled = false;
          showPwErr('Connection error. Please try again.');
        });
        return false;
      }

    }, true); // capture phase — fires before React's onClick
  }

  /* ── Merge Contact Info + Billing into one unified card (JS) ── */
  var _merged = false;
  function mergeContactAndBilling(){
    if(_merged) return;

    // Find the step that holds the email input (Contact Info)
    var emailEl = document.querySelector(
      '.wc-block-checkout__main input[autocomplete="email"],' +
      '.wc-block-checkout__main input[type="email"]'
    );
    if(!emailEl) return;
    var contactStep = emailEl.closest('.wc-block-components-checkout-step');
    if(!contactStep) return;

    // Find the step that holds the address form (Billing Address)
    var addrEl = document.querySelector(
      '.wc-block-checkout__main .wc-block-components-address-form'
    );
    if(!addrEl) return;
    var billingStep = addrEl.closest('.wc-block-components-checkout-step');
    if(!billingStep || billingStep === contactStep) return;

    // Apply merge — use setProperty with 'important' to beat specificity
    var s = contactStep.style;
    s.setProperty('border-bottom-left-radius',  '0',     'important');
    s.setProperty('border-bottom-right-radius', '0',     'important');
    s.setProperty('border-bottom',              'none',  'important');
    s.setProperty('margin-bottom',              '0',     'important');

    var b = billingStep.style;
    b.setProperty('border-top-left-radius',  '0', 'important');
    b.setProperty('border-top-right-radius', '0', 'important');
    b.setProperty('border-top', '1px solid var(--border-dark)', 'important');

    // Hide "Billing address" heading — redundant inside merged card
    var title = billingStep.querySelector('.wc-block-components-checkout-step__title');
    if(title) title.style.setProperty('display', 'none', 'important');

    _merged = true;
  }

  /* ── Remove "Secure checkout · SSL encrypted" trust line (run once) ── */
  var _secureHidden = false;
  function removeSecureText(){
    if(_secureHidden) return;
    var phrases = ['Secure checkout','SSL encrypted','30-day guarantee','30 day guarantee'];
    var main = document.querySelector('.wc-block-checkout__main') || document.body;
    main.querySelectorAll('p, span, div').forEach(function(el){
      if(el.children.length > 0) return;
      var t = el.textContent || '';
      if(phrases.some(function(p){ return t.indexOf(p) !== -1; })){
        el.style.display = 'none';
        _secureHidden = true;
      }
    });
  }

  /* ── Course switcher widget ─────────────────────────────── */
  var _switcherBuilt = false;
  var _activeProductId = PT101_CURRENT_PRODUCT;

  function buildCourseSwitcher() {
    if (_switcherBuilt) return;
    var sidebar = document.querySelector('.wc-block-checkout__sidebar');
    if (!sidebar) return;

    var wrap = document.createElement('div');
    wrap.id = 'pt101-course-switcher';
    wrap.className = 'pt101-switcher';

    var btnsHtml = PT101_COURSES.map(function(c) {
      var active = c.id === _activeProductId ? ' active' : '';
      return '<button type="button" class="pt101-switcher__btn' + active + '" data-pid="' + c.id + '">' +
        '<span class="pt101-switcher__btn-name">' + c.name + '</span>' +
        '<span class="pt101-switcher__btn-price">' + c.price + '</span>' +
        '</button>';
    }).join('');

    wrap.innerHTML =
      '<p class="pt101-switcher__title">Choose your course</p>' +
      '<div class="pt101-switcher__grid">' + btnsHtml + '</div>';

    sidebar.insertBefore(wrap, sidebar.firstChild);

    wrap.addEventListener('click', function(e) {
      var btn = e.target.closest('.pt101-switcher__btn');
      if (!btn) return;
      var pid = parseInt(btn.getAttribute('data-pid'), 10);
      if (pid === _activeProductId) return;
      swapCourse(pid);
    });

    _switcherBuilt = true;
  }

  function swapCourse(productId) {
    var switcher = document.getElementById('pt101-course-switcher');
    if (switcher) switcher.classList.add('pt101-switcher--busy');

    var body = new URLSearchParams();
    body.append('action', 'pt101_swap_course');
    body.append('nonce', PT101_SWAP_NONCE);
    body.append('product_id', String(productId));

    fetch(PT101_AJAX, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        window.location.reload();
      } else {
        if (switcher) switcher.classList.remove('pt101-switcher--busy');
      }
    })
    .catch(function() {
      if (switcher) switcher.classList.remove('pt101-switcher--busy');
    });
  }

  /* ── Move Express Checkout (Apple/Google Pay) into Payment block ── */
  var _expressMoved = false;
  function moveExpressPayment(){
    if(_expressMoved) return;
    var express = document.querySelector('.wp-block-woocommerce-checkout-express-payment-block');
    var payment = document.querySelector('.wp-block-woocommerce-checkout-payment-block');
    if(!express || !payment) return;
    // Already inside payment block — nothing to do
    if(payment.contains(express)) { _expressMoved = true; return; }
    payment.insertBefore(express, payment.firstChild);
    // Hide the "Or continue below" divider that sat between express and the form
    var divider = document.querySelector('.wc-block-components-express-payment__title-container');
    if(!divider) {
      // Fallback: the separator is sometimes a standalone element before the payment block
      var prev = payment.previousElementSibling;
      if(prev && prev.classList.contains('wc-block-components-express-payment-continue-rule')){
        prev.style.display = 'none';
      }
    }
    _expressMoved = true;
  }

  /* ── Main cleanup / injection ──────────────────────────── */
  function cleanup(){
    /* Hide empty checkout steps */
    document.querySelectorAll('.wc-block-components-checkout-step').forEach(function(step){
      var content = step.querySelector('.wc-block-components-checkout-step__content');
      if(!content) return;
      var visible = Array.from(content.children).filter(function(c){
        var s = window.getComputedStyle(c);
        return s.display !== 'none' && s.visibility !== 'hidden' && c.offsetHeight > 0;
      });
      if(visible.length === 0) step.style.display = 'none';
    });

    /* Build course switcher in the sidebar */
    buildCourseSwitcher();

    /* Move Express Checkout into the Payment block */
    moveExpressPayment();

    /* Merge Contact Info + Billing into one card (JS fallback for :has()) */
    mergeContactAndBilling();

    /* Remove trust/secure text */
    removeSecureText();

    /* Inject password field */
    injectPasswordField();

    /* Inject consent checkboxes — replace the default terms text */
    if(!document.getElementById('pt101-consent')){
      var termsBlock   = document.querySelector('.wc-block-checkout__terms');
      var privacyBlock = document.querySelector('.wc-block-checkout__privacy-policy');
      var target = termsBlock || privacyBlock;
      if(!target){
        target = document.querySelector('.wc-block-checkout__actions_row')
              || document.querySelector('.wc-block-checkout__actions');
      }
      if(target){
        if(termsBlock)   termsBlock.style.display   = 'none';
        if(privacyBlock) privacyBlock.style.display = 'none';

        var consent = document.createElement('div');
        consent.id = 'pt101-consent';
        consent.className = 'pt101-consent';
        consent.innerHTML = [
          '<label class="pt101-consent__item pt101-consent__item--required">',
            '<input type="checkbox" id="pt101-terms-agree" name="pt101_terms_consent" value="1" required>',
            '<span>By signing up, you confirm that you have read and agree to Prop Trading 101\'s ',
              '<a href="' + TERMS_URL + '" target="_blank">Terms &amp; Conditions</a> and ',
              '<a href="' + PRIVACY_URL + '" target="_blank">Privacy Policy</a>. ',
              'These explain how we operate and how your personal data is processed and handled. ',
              '<strong>(Required)</strong>',
            '</span>',
          '</label>',
          '<label class="pt101-consent__item">',
            '<input type="checkbox" id="pt101-marketing-agree" name="pt101_marketing_consent" value="1">',
            '<span>I provide my consent to receive marketing communications such as ',
              'coupons, news, promotions and product updates via electronic channels ',
              '(e.g. email, Whatsapp, or SMS messages). I understand I can withdraw ',
              'my consent at any time by unsubscribing from any such communication ',
              'or by updating my profile page preferences.</span>',
          '</label>'
        ].join('');

        target.parentNode.insertBefore(consent, target);
      }
    }

    /* Bind submit handler (consent + password) */
    bindSubmitHandler();
  }

  function tryCleanup(){
    cleanup();
    if(document.getElementById('pt101-password-wrap') && document.getElementById('pt101-consent')){
      obs.disconnect();
    }
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){ setTimeout(tryCleanup, 400); });
  } else {
    setTimeout(tryCleanup, 400);
  }
  /* Observe document.body so we never miss the checkout rendering regardless
     of whether .wc-block-checkout__main exists at script load time */
  var obs = new MutationObserver(function(){ setTimeout(tryCleanup, 150); });
  obs.observe(document.body, {childList:true, subtree:true});
  setTimeout(function(){ obs.disconnect(); }, 30000);
})();
</script>
    <?php
}, 999 );

/* ── Validate terms consent on server side ────────────────────── */
add_action( 'woocommerce_checkout_process', function () {
    if ( empty( $_POST['pt101_terms_consent'] ) ) {
        wc_add_notice( __( 'Please accept the Terms & Conditions and Privacy Policy to proceed.', 'prop-trading-101' ), 'error' );
    }
});
/* Save marketing consent as order/user meta */
add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
    if ( ! empty( $_POST['pt101_marketing_consent'] ) ) {
        update_post_meta( $order_id, '_pt101_marketing_consent', 'yes' );
        $order = wc_get_order( $order_id );
        if ( $order && $order->get_customer_id() ) {
            update_user_meta( $order->get_customer_id(), 'pt101_marketing_consent', 'yes' );
        }
    }
});

/* ── THANK YOU PAGE: JS ENHANCEMENT ────────────────────────────
 * Injects success icon + "What's Next" section on order-received.
 */
add_action( 'wp_footer', function () {
    if ( ! function_exists( 'is_wc_endpoint_url' ) ) return;
    if ( ! is_wc_endpoint_url( 'order-received' ) ) return;
    $dashboard_url = home_url( '/dashboard/' );
    $courses_url   = home_url( '/programs' );
    ?>
<script>
(function(){
  var thankyou = document.querySelector('.woocommerce-thankyou-order-received');
  if (!thankyou) return;

  /* ── 1. Success icon ── */
  var icon = document.createElement('span');
  icon.className = 'pt101-success-icon';
  icon.innerHTML = '&#10003;';
  thankyou.parentNode.insertBefore(icon, thankyou);

  /* ── 2. Add sub-text inside banner ── */
  var sub = document.createElement('span');
  sub.className = 'pt101-success-sub';
  sub.textContent = 'A confirmation has been sent to your email address.';
  thankyou.appendChild(sub);

  /* ── 3. What\'s Next section ── */
  var billing = document.querySelector('.woocommerce-customer-details')
             || document.querySelector('.woocommerce-order-details');
  if (!billing) return;

  var ns = document.createElement('div');
  ns.className = 'pt101-next-steps';
  ns.innerHTML = [
    '<h3 class="pt101-next-steps__heading">What happens next?</h3>',
    '<div class="pt101-next-steps__grid">',
      '<div class="pt101-step-item">',
        '<span class="pt101-step-num">1</span>',
        '<div><strong>Check your email</strong>',
        '<p>Your receipt and access instructions have been sent to your inbox.</p></div>',
      '</div>',
      '<div class="pt101-step-item">',
        '<span class="pt101-step-num">2</span>',
        '<div><strong>Access your course</strong>',
        '<p>Head to your dashboard to start learning immediately.</p></div>',
      '</div>',
      '<div class="pt101-step-item">',
        '<span class="pt101-step-num">3</span>',
        '<div><strong>Join our community</strong>',
        '<p>Connect with fellow traders in our private members group.</p></div>',
      '</div>',
      '<div class="pt101-step-item">',
        '<span class="pt101-step-num">4</span>',
        '<div><strong>Get funded</strong>',
        '<p>Complete the program and move toward your prop firm challenge.</p></div>',
      '</div>',
    '</div>',
    '<div class="pt101-next-steps__actions">',
      '<a href="<?php echo esc_url( $dashboard_url ); ?>" class="pt101-btn-primary">Go to Dashboard &rarr;</a>',
      '<a href="<?php echo esc_url( $courses_url ); ?>" class="pt101-btn-secondary">Browse More Courses</a>',
    '</div>'
  ].join('');

  billing.parentNode.insertBefore(ns, billing.nextSibling);
})();
</script>
    <?php
}, 10 );

/* ── Course switcher: swap cart product via AJAX ────────────────
 * Empties the cart and adds the requested course. Called from JS
 * on the checkout page when the user picks a different course.
 */
add_action( 'wp_ajax_nopriv_pt101_swap_course', 'pt101_ajax_swap_course' );
add_action( 'wp_ajax_pt101_swap_course',        'pt101_ajax_swap_course' );
function pt101_ajax_swap_course() {
    check_ajax_referer( 'pt101_swap_course', 'nonce' );
    $product_id  = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    $allowed_ids = [ 158, 207, 253, 254, 256 ];
    if ( ! in_array( $product_id, $allowed_ids, true ) ) {
        wp_send_json_error( 'Invalid product' );
    }
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        wp_send_json_error( 'Cart unavailable' );
    }
    WC()->cart->empty_cart();
    $key = WC()->cart->add_to_cart( $product_id, 1 );
    if ( ! $key ) {
        wp_send_json_error( 'Could not add product to cart' );
    }
    wp_send_json_success( [ 'product_id' => $product_id ] );
}

/* ── LOGIN PAGE CSS ─────────────────────────────────────────────
 * Styles for template-login.php — dark, centred card layout.
 */
add_action( 'wp_head', function () {
    if ( ! is_page_template( 'template-login.php' ) ) return;
    ?>
<style id="pt101-login-styles">
.pt101-login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
  background: #0d0f1a;
}
.pt101-login-wrap {
  width: 100%;
  max-width: 420px;
  display: flex;
  flex-direction: column;
  gap: 28px;
}
.pt101-login-brand {
  text-align: center;
}
.pt101-login-logo {
  font-size: 1.25rem;
  font-weight: 700;
  color: #f0efea;
  text-decoration: none;
  letter-spacing: -.01em;
}
.pt101-login-tagline {
  margin: 8px 0 0;
  font-size: .875rem;
  color: rgba(240,239,234,.55);
}
.pt101-login-card {
  background: #13162b;
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 14px;
  padding: 36px 32px;
}
.pt101-login-heading {
  font-size: 1.25rem;
  font-weight: 700;
  color: #f0efea;
  margin: 0 0 24px;
  letter-spacing: -.02em;
}
.pt101-login-error {
  background: rgba(248,113,113,.12);
  border: 1px solid rgba(248,113,113,.3);
  color: #f87171;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: .875rem;
  margin-bottom: 20px;
}

/* WP login form overrides */
.pt101-login-card .login-username,
.pt101-login-card .login-password {
  margin-bottom: 16px;
}
.pt101-login-card label {
  display: block;
  font-size: .8125rem;
  font-weight: 600;
  color: rgba(240,239,234,.7);
  margin-bottom: 6px;
}
.pt101-login-card input[type="text"],
.pt101-login-card input[type="password"] {
  width: 100%;
  box-sizing: border-box;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 8px;
  padding: 11px 14px;
  font-size: .9375rem;
  color: #f0efea;
  outline: none;
  transition: border-color .18s;
  font-family: inherit;
}
.pt101-login-card input[type="text"]:focus,
.pt101-login-card input[type="password"]:focus {
  border-color: #7c6ef5;
  box-shadow: 0 0 0 3px rgba(124,110,245,.18);
}
.pt101-login-card .login-remember {
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pt101-login-card .login-remember label {
  margin: 0;
  font-size: .875rem;
  color: rgba(240,239,234,.55);
  font-weight: 400;
}
.pt101-login-card input[type="checkbox"] {
  accent-color: #7c6ef5;
  width: 16px;
  height: 16px;
}
.pt101-login-card input[type="submit"],
.pt101-login-card .wp-submit input {
  width: 100%;
  background: #7c6ef5;
  border: none;
  border-radius: 8px;
  padding: 13px 20px;
  font-size: .9375rem;
  font-weight: 700;
  color: #fff;
  cursor: pointer;
  transition: background .18s;
  font-family: inherit;
}
.pt101-login-card input[type="submit"]:hover {
  background: #6a5de0;
}
.pt101-login-forgot {
  margin: 16px 0 0;
  text-align: center;
  font-size: .8125rem;
}
.pt101-login-forgot a {
  color: rgba(240,239,234,.5);
  text-decoration: none;
  transition: color .18s;
}
.pt101-login-forgot a:hover { color: #f0efea; }

.pt101-login-enroll {
  text-align: center;
  font-size: .875rem;
  color: rgba(240,239,234,.45);
  margin: 0;
}
.pt101-login-enroll a {
  color: #7c6ef5;
  text-decoration: none;
  font-weight: 600;
}
.pt101-login-enroll a:hover { color: #f0efea; }

@media (max-width: 480px) {
  .pt101-login-card { padding: 28px 20px; }
}
</style>
    <?php
} );

/* ── STUDENT PORTAL: REDIRECTS ─────────────────────────────────
 * 1. /my-account (plain) → /dashboard/ (Tutor LMS)
 * 2. After any WP login  → /dashboard/
 */
add_action( 'template_redirect', function () {
    if ( ! function_exists( 'is_account_page' ) ) return;
    if ( is_account_page() && ! is_wc_endpoint_url() && is_user_logged_in() ) {
        wp_safe_redirect( home_url( '/dashboard/' ), 302 );
        exit;
    }
} );

add_filter( 'login_redirect', function ( $url, $requested, $user ) {
    if ( $user instanceof WP_User ) {
        return home_url( '/dashboard/' );
    }
    return $url;
}, 10, 3 );

/* ── TUTOR LMS: DARK THEME OVERRIDES ───────────────────────────
 * Overrides Tutor LMS default white UI to match the site's dark
 * design tokens. Targets dashboard, course player, and sidebar.
 */
add_action( 'wp_head', function () {
    if ( ! function_exists( 'tutor' ) ) return;
    ?>
<style id="pt101-tutor-overrides">
/* ── Design tokens (match style.css) ── */
:root {
  --tutor-bg:        #0d0f1a;
  --tutor-surface:   #13162b;
  --tutor-border:    rgba(255,255,255,.08);
  --tutor-text:      #f0efea;
  --tutor-muted:     rgba(240,239,234,.55);
  --tutor-accent:    #7c6ef5;
  --tutor-accent-h:  #6a5de0;
  --tutor-radius:    10px;
}

/* ── Global wrappers ── */
.tutor-wrap,
.tutor-dashboard,
.tutor-page-wrap,
body.tutor-dashboard-page {
  background: var(--tutor-bg) !important;
  color: var(--tutor-text) !important;
}

/* ── Dashboard layout ── */
.tutor-dashboard-sidebar {
  background: var(--tutor-surface) !important;
  border-right: 1px solid var(--tutor-border) !important;
}
.tutor-dashboard-content {
  background: var(--tutor-bg) !important;
}

/* ── Sidebar nav ── */
.tutor-dashboard-menu li a,
.tutor-dashboard-menu li button {
  color: var(--tutor-muted) !important;
  border-radius: 8px !important;
  transition: background .18s, color .18s !important;
}
.tutor-dashboard-menu li a:hover,
.tutor-dashboard-menu li.tutor-is-active a {
  background: rgba(124,110,245,.15) !important;
  color: var(--tutor-text) !important;
}
.tutor-dashboard-menu li.tutor-is-active a {
  font-weight: 600 !important;
}

/* ── Cards ── */
.tutor-card,
.tutor-course-card,
.tutor-dashboard-card {
  background: var(--tutor-surface) !important;
  border: 1px solid var(--tutor-border) !important;
  border-radius: var(--tutor-radius) !important;
  color: var(--tutor-text) !important;
  box-shadow: none !important;
}
.tutor-card-header,
.tutor-card-footer {
  background: transparent !important;
  border-color: var(--tutor-border) !important;
}

/* ── Typography ── */
.tutor-wrap h1,.tutor-wrap h2,.tutor-wrap h3,
.tutor-wrap h4,.tutor-wrap h5,.tutor-wrap h6 {
  color: var(--tutor-text) !important;
}
.tutor-wrap p,
.tutor-wrap span,
.tutor-wrap li,
.tutor-wrap label {
  color: var(--tutor-muted) !important;
}
.tutor-wrap strong,
.tutor-course-card__title,
.tutor-dashboard-title {
  color: var(--tutor-text) !important;
}

/* ── Progress bar ── */
.tutor-progress-bar {
  background: rgba(255,255,255,.1) !important;
  border-radius: 99px !important;
  overflow: hidden !important;
}
.tutor-progress-bar__fill,
.tutor-progress-bar > span {
  background: var(--tutor-accent) !important;
  border-radius: 99px !important;
}

/* ── Buttons ── */
.tutor-btn-primary,
.tutor-btn.tutor-btn-primary {
  background: var(--tutor-accent) !important;
  border-color: var(--tutor-accent) !important;
  color: #fff !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  transition: background .18s !important;
}
.tutor-btn-primary:hover {
  background: var(--tutor-accent-h) !important;
  border-color: var(--tutor-accent-h) !important;
}
.tutor-btn-outline-primary {
  border-color: var(--tutor-accent) !important;
  color: var(--tutor-accent) !important;
  border-radius: 8px !important;
  background: transparent !important;
}
.tutor-btn-outline-primary:hover {
  background: var(--tutor-accent) !important;
  color: #fff !important;
}

/* ── Form inputs ── */
.tutor-wrap input[type="text"],
.tutor-wrap input[type="email"],
.tutor-wrap input[type="password"],
.tutor-wrap textarea,
.tutor-wrap select {
  background: rgba(255,255,255,.06) !important;
  border: 1px solid var(--tutor-border) !important;
  color: var(--tutor-text) !important;
  border-radius: 8px !important;
}
.tutor-wrap input::placeholder,
.tutor-wrap textarea::placeholder {
  color: var(--tutor-muted) !important;
}

/* ── Course player ── */
.tutor-course-player,
.tutor-single-course-segment {
  background: var(--tutor-bg) !important;
}
.tutor-course-player-header {
  background: var(--tutor-surface) !important;
  border-bottom: 1px solid var(--tutor-border) !important;
}
.tutor-course-sidebar-list,
.tutor-course-topics-list {
  background: var(--tutor-surface) !important;
  border-left: 1px solid var(--tutor-border) !important;
}
.tutor-course-topic-header {
  background: rgba(255,255,255,.04) !important;
  color: var(--tutor-text) !important;
  border-bottom: 1px solid var(--tutor-border) !important;
}
.tutor-course-topic-header:hover {
  background: rgba(124,110,245,.12) !important;
}
.tutor-course-lesson-item,
.tutor-course-content-list-item {
  color: var(--tutor-muted) !important;
  border-bottom: 1px solid var(--tutor-border) !important;
}
.tutor-course-lesson-item.is-active,
.tutor-course-lesson-item:hover {
  background: rgba(124,110,245,.12) !important;
  color: var(--tutor-text) !important;
}
.tutor-lesson-icon,
.tutor-icon-filter {
  color: var(--tutor-accent) !important;
  filter: none !important;
}

/* ── Tables ── */
.tutor-table thead th,
.tutor-table th {
  background: var(--tutor-surface) !important;
  color: var(--tutor-muted) !important;
  border-color: var(--tutor-border) !important;
}
.tutor-table td {
  background: var(--tutor-bg) !important;
  border-color: var(--tutor-border) !important;
  color: var(--tutor-text) !important;
}
.tutor-table tr:hover td {
  background: rgba(255,255,255,.03) !important;
}

/* ── Badges / tags ── */
.tutor-badge,
.tutor-status-badge {
  border-radius: 99px !important;
  font-size: .75rem !important;
  font-weight: 600 !important;
}
.tutor-badge-success { background: rgba(52,211,153,.15) !important; color: #34d399 !important; }
.tutor-badge-warning { background: rgba(251,191,36,.15)  !important; color: #fbbf24 !important; }
.tutor-badge-danger  { background: rgba(248,113,113,.15) !important; color: #f87171 !important; }

/* ── Tabs ── */
.tutor-tabs-nav li a,
.tutor-nav-tab {
  color: var(--tutor-muted) !important;
  border-color: transparent !important;
}
.tutor-tabs-nav li.tutor-is-active a,
.tutor-nav-tab.is-active {
  color: var(--tutor-text) !important;
  border-bottom-color: var(--tutor-accent) !important;
}

/* ── Dropdown / modal overlays ── */
.tutor-dropdown-content,
.tutor-modal-overlay + .tutor-modal {
  background: var(--tutor-surface) !important;
  border: 1px solid var(--tutor-border) !important;
  border-radius: var(--tutor-radius) !important;
  color: var(--tutor-text) !important;
  box-shadow: 0 8px 32px rgba(0,0,0,.5) !important;
}

/* ── Scrollbars ── */
.tutor-wrap ::-webkit-scrollbar { width: 6px; }
.tutor-wrap ::-webkit-scrollbar-track { background: var(--tutor-bg); }
.tutor-wrap ::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,.15);
  border-radius: 3px;
}
</style>
    <?php
}, 100 );
