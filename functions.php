<?php
/**
 * Prop Trading 101 — functions.php v4
 */
defined( 'ABSPATH' ) || exit;

define( 'PT101_VER', wp_get_theme()->get( 'Version' ) ?: '1.0.0' );
define( 'PT101_DIR', get_template_directory() );
define( 'PT101_URI', get_template_directory_uri() );

/* ── TEMP DEBUG LOGGING (SAFE BACKEND ONLY) ───────────────────
 * This is a temporary diagnostic helper to capture fatals in
 * wp-content/debug.log without changing frontend output.
 */
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
}
if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', true );
}
if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false );
}
@ini_set( 'display_errors', '0' );
@ini_set( 'log_errors', '1' );
@ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );

register_shutdown_function( function () {
    $error = error_get_last();
    if ( ! $error ) {
        return;
    }
    $fatal_types = [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ];
    if ( in_array( (int) $error['type'], $fatal_types, true ) ) {
        error_log(
            sprintf(
                '[PT101 Fatal] %s in %s on line %d',
                (string) $error['message'],
                (string) $error['file'],
                (int) $error['line']
            )
        );
    }
} );

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

/* ── Enrolled lesson UX/UI polish (single lesson + enrolled course view) ── */
add_action( 'wp_head', function () {
    if ( true ) return;
    ?>
<style id="pt101-enrolled-ux-polish">
/* Sticky top lesson bar with better hierarchy */
body.single-lesson .tutor-course-topic-single-header,
body.single-lesson .tutor-lesson-topbar,
body.single-courses .tutor-course-topic-single-header {
  position: sticky !important;
  top: 74px !important;
  z-index: 60 !important;
  backdrop-filter: blur(8px) !important;
  box-shadow: 0 8px 24px rgba(0,0,0,.22) !important;
}
body.admin-bar.single-lesson .tutor-course-topic-single-header,
body.admin-bar.single-lesson .tutor-lesson-topbar,
body.admin-bar.single-courses .tutor-course-topic-single-header {
  top: 106px !important;
}

/* Improve reading comfort in lesson body */
body.single-lesson .tutor-lesson-content,
body.single-lesson .tutor-single-entry-content,
body.single-lesson .tutor-course-topic-single-body {
  max-width: 860px !important;
  margin-inline: auto !important;
}
body.single-lesson .tutor-lesson-content p,
body.single-lesson .tutor-single-entry-content p,
body.single-lesson .tutor-course-topic-single-body p {
  line-height: 1.85 !important;
  font-size: 1.08rem !important;
  color: rgba(240, 240, 245, .88) !important;
}
body.single-lesson .tutor-lesson-content h1,
body.single-lesson .tutor-lesson-content h2,
body.single-lesson .tutor-lesson-content h3 {
  color: #f5f6fb !important;
  letter-spacing: -0.02em !important;
}

/* Sidebar lesson items: stronger active/incomplete states */
body.single-lesson .tutor-course-topics-list li,
body.single-courses .tutor-course-topics-list li {
  border-radius: 10px !important;
  margin: 4px 6px !important;
  border: 1px solid rgba(255,255,255,.06) !important;
  transition: border-color .18s ease, background .18s ease, transform .18s ease !important;
}
body.single-lesson .tutor-course-topics-list li:hover,
body.single-courses .tutor-course-topics-list li:hover {
  border-color: rgba(124,110,245,.45) !important;
  background: rgba(124,110,245,.10) !important;
  transform: translateY(-1px) !important;
}
body.single-lesson .tutor-course-topics-list li.tutor-active,
body.single-lesson .tutor-course-topics-list li.active,
body.single-courses .tutor-course-topics-list li.tutor-active,
body.single-courses .tutor-course-topics-list li.active {
  border-color: rgba(124,110,245,.75) !important;
  background: rgba(124,110,245,.16) !important;
  box-shadow: 0 0 0 1px rgba(124,110,245,.25) inset !important;
}

/* Mark-as-complete button: clearer primary action */
body.single-lesson .tutor-btn.tutor-btn-primary,
body.single-lesson .tutor-lesson-mark-complete,
body.single-lesson [class*="mark-complete"] {
  min-height: 44px !important;
  font-weight: 700 !important;
  letter-spacing: -0.01em !important;
  box-shadow: 0 8px 20px rgba(124,110,245,.28) !important;
}

/* Mobile: keep reading area spacious and avoid cramped topic list */
@media (max-width: 1024px) {
  body.single-lesson .tutor-lesson-content,
  body.single-lesson .tutor-single-entry-content,
  body.single-lesson .tutor-course-topic-single-body {
    max-width: none !important;
    padding-inline: 18px !important;
  }
  body.single-lesson .tutor-course-topics-list li,
  body.single-courses .tutor-course-topics-list li {
    margin-inline: 2px !important;
  }
}
</style>
    <?php
}, 110 );

/* ── Enrolled lesson micro-UX: focus active item + quick next shortcut ── */
add_action( 'wp_footer', function () {
    if ( true ) return;
    ?>
<script>
(function () {
  function getActiveLessonItem() {
    return document.querySelector(
      '.tutor-course-topics-list li.tutor-active,' +
      '.tutor-course-topics-list li.active,' +
      '.tutor-course-topics-list .is-active'
    );
  }

  function scrollActiveIntoView() {
    var active = getActiveLessonItem();
    if (!active || !active.scrollIntoView) return;
    active.scrollIntoView({ block: 'center', behavior: 'smooth' });
  }

  function nextLessonLink() {
    var active = getActiveLessonItem();
    if (!active) return null;
    var next = active.nextElementSibling;
    while (next) {
      var link = next.querySelector('a[href]');
      if (link) return link;
      next = next.nextElementSibling;
    }
    return null;
  }

  function bindShortcut() {
    document.addEventListener('keydown', function (e) {
      if (e.defaultPrevented) return;
      if (e.metaKey || e.ctrlKey || e.altKey) return;
      if (e.key !== 'n' && e.key !== 'N') return;
      var tag = (document.activeElement && document.activeElement.tagName) || '';
      if (tag === 'INPUT' || tag === 'TEXTAREA') return;
      var link = nextLessonLink();
      if (!link) return;
      e.preventDefault();
      window.location.href = link.href;
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      scrollActiveIntoView();
      bindShortcut();
    });
  } else {
    scrollActiveIntoView();
    bindShortcut();
  }
})();
</script>
    <?php
}, 1000 );

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

/* ── TUTOR LMS: SAFE COURSE REDIRECT ROUTING ───────────────────
 * Redirect only on actual single course requests.
 * This avoids expensive/recursive link filtering in large renders.
 */
add_action( 'template_redirect', function () {
    if ( is_admin() || wp_doing_ajax() ) return;
    if ( ! is_singular( 'courses' ) ) return;
    if ( ! function_exists( 'tutor_utils' ) ) return;

    $course_id = get_queried_object_id();
    if ( ! $course_id ) return;

    // Enrolled users stay on Tutor LMS player page.
    if ( tutor_utils()->is_enrolled( $course_id ) ) return;

    $slug = get_post_field( 'post_name', $course_id );
    if ( ! $slug ) return;

    $map = [
        'intro-to-trading'                         => '/intro-to-trading',
        'trading-foundations'                      => '/trading-foundations',
        'market-mechanics-analysis'                => '/market-mechanics-analysis',
        'strategy-development-advanced-technicals' => '/strategy-development-advanced-technicals',
        'mastering-professional-trading'           => '/mastering-professional-trading',
    ];

    if ( isset( $map[ $slug ] ) ) {
        wp_safe_redirect( home_url( $map[ $slug ] ), 302 );
        exit;
    }
}, 9 );

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
    if ( is_singular( 'lesson' ) ) return;
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

  /* ── Override Tutor LMS v2's own CSS variables ──────────────────
     Tutor v2 uses these internally with !important on card/modal
     backgrounds. Redefining them here is the only reliable fix.  */
  --tutor-white:           #13162b;
  --tutor-color-white:     #13162b;
  --tutor-body-color:      #f0efea;
  --tutor-body-bg:         #0d0f1a;
  --tutor-card-color:      #f0efea;
  --tutor-card-bg:         #13162b;
  --tutor-black:           #f0efea;
  --tutor-color-black:     #f0efea;
  --tutor-gray-50:         #161d2e;
  --tutor-gray-100:        #1a2240;
  --tutor-gray-200:        #1e2848;
  --tutor-gray-400:        rgba(240,239,234,.45);
  --tutor-gray-500:        rgba(240,239,234,.55);
  --tutor-gray-600:        rgba(240,239,234,.65);
  --tutor-border-color:    rgba(255,255,255,.08);
  --tutor-input-bg:        rgba(255,255,255,.06);
  --tutor-input-color:     #f0efea;
  --tutor-placeholder-color: rgba(240,239,234,.45);
}

/* ── Tutor tooltips (ⓘ icon hover on topic/lesson rows) ── */
/* Tutor v2 renders these as .tutor-tooltip > .tutor-tooltip-content  */
/* Some versions also use .tutor-tip, .tippy-box, or [data-tippy-content] */
.tutor-tooltip-content,
.tutor-tooltip > span:last-child,
.tutor-tooltip .tutor-tooltip-txt,
.tippy-box,
.tutor-tip,
[class*="tutor-tooltip"]:not([class*="icon"]) {
  background: #1e2848 !important;
  color: #f0efea !important;
  border: 1px solid rgba(255,255,255,0.1) !important;
  border-radius: 6px !important;
}
.tippy-arrow { color: #1e2848 !important; }

/* ── Nav: light text on Tutor single-course/lesson/quiz (dark header) ── */
body.single-courses .site-logo,
body.single-lesson .site-logo,
body.single-quiz .site-logo {
  color: #f0efea !important;
}
body.single-courses .primary-nav li a,
body.single-lesson .primary-nav li a,
body.single-quiz .primary-nav li a {
  color: rgba(240,239,234,0.75) !important;
}
body.single-courses .primary-nav li a:hover,
body.single-lesson .primary-nav li a:hover,
body.single-quiz .primary-nav li a:hover {
  color: #f0efea !important;
  background: rgba(255,255,255,0.06) !important;
}
body.single-courses .nav-dropdown-trigger,
body.single-lesson .nav-dropdown-trigger,
body.single-quiz .nav-dropdown-trigger {
  color: rgba(240,239,234,0.75) !important;
}
body.single-courses .nav-dropdown-trigger svg path,
body.single-lesson .nav-dropdown-trigger svg path,
body.single-quiz .nav-dropdown-trigger svg path {
  stroke: rgba(240,239,234,0.75) !important;
}
body.single-courses .btn-hdr-login,
body.single-lesson .btn-hdr-login,
body.single-quiz .btn-hdr-login {
  color: #f0efea !important;
  border-color: rgba(240,239,234,0.3) !important;
}

/* ── Push ALL page content below fixed 64px nav ── */
/* Hardcoded — CSS var --nav-h may not resolve inside PHP-injected <style> */
body.tutor-screen-frontend-dashboard,
body.tutor-frontend,
body.single-courses,
body.single-lesson,
body.single-quiz,
body.single-assignments {
  padding-top: 80px !important;
}
body.admin-bar.tutor-screen-frontend-dashboard,
body.admin-bar.tutor-frontend,
body.admin-bar.single-courses,
body.admin-bar.single-lesson {
  padding-top: 112px !important; /* 80 + 32px admin bar */
}
/* Inner page-wrap must NOT add extra top padding */
.tutor-page-wrap {
  padding-top: 0 !important;
}

/* ── HIDE Reviews globally (not needed) ── */
/* Dashboard sidebar nav item */
.tutor-dashboard-menu-reviews,
[data-page="tutor-reviews"],
.tutor-reviews-section { display: none !important; }
/* Course page tab — exact match via data-tutor-nav-target (inspected from live HTML) */
body.single-courses [data-tutor-nav-target="tutor-course-details-tab-reviews"],
body.single-courses li:has([data-tutor-nav-target="tutor-course-details-tab-reviews"]),
body.single-courses #tutor-course-details-tab-reviews,
body.single-courses [data-tutor-nav-target*="review"] { display: none !important; }

/* ── Global wrappers ── */
.tutor-wrap,
.tutor-dashboard,
.tutor-page-wrap,
body.tutor-screen-frontend-dashboard,
body.tutor-frontend {
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
/* Actual Tutor LMS v2 HTML: ul.tutor-dashboard-permalinks > li.tutor-dashboard-menu-item */
.tutor-dashboard-permalinks .tutor-dashboard-menu-item-link,
.tutor-dashboard-menu li a,
.tutor-dashboard-menu li button {
  color: var(--tutor-muted) !important;
  border-radius: 8px !important;
  transition: background .18s, color .18s !important;
}
.tutor-dashboard-permalinks .tutor-dashboard-menu-item-link:hover,
.tutor-dashboard-menu-item.active .tutor-dashboard-menu-item-link,
.tutor-dashboard-menu li a:hover,
.tutor-dashboard-menu li.active a {
  background: rgba(124,110,245,.15) !important;
  color: var(--tutor-text) !important;
}
.tutor-dashboard-menu-item.active .tutor-dashboard-menu-item-link,
.tutor-dashboard-menu li.active a {
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

/* ── Typography — headings & body text ── */
.tutor-wrap h1, .tutor-wrap h2, .tutor-wrap h3,
.tutor-wrap h4, .tutor-wrap h5, .tutor-wrap h6,
.tutor-dashboard-content-inner h2,
.tutor-dashboard-content-inner h3 {
  color: var(--tutor-text) !important;
}
/* Default body text: full brightness — only dim truly secondary copy */
.tutor-wrap {
  color: var(--tutor-text) !important;
}
/* Secondary / meta text explicitly */
.tutor-meta,
.tutor-course-card__meta,
.tutor-dashboard-menu .tutor-menu-item-label,
.tutor-stats-card__label,
.tutor-color-muted,
[class*="tutor-text-hint"],
[class*="tutor-fs-7"] {
  color: var(--tutor-muted) !important;
}
/* Links inside dashboard: purple accent, not orange */
.tutor-wrap a,
.tutor-dashboard-content a {
  color: var(--tutor-accent) !important;
}
.tutor-wrap a:hover,
.tutor-dashboard-content a:hover {
  color: var(--tutor-text) !important;
}
/* But sidebar links handled separately above */
.tutor-dashboard-permalinks .tutor-dashboard-menu-item-link,
.tutor-dashboard-menu li a {
  color: var(--tutor-muted) !important;
}
/* Course titles in cards/tables: always full brightness */
.tutor-course-name,
.tutor-course-card__title,
.tutor-dashboard-title,
.tutor-table td a,
.tutor-table td {
  color: var(--tutor-text) !important;
}
/* Stat card numbers */
.tutor-stats-card__count,
[class*="tutor-fs-1"],
[class*="tutor-fw-bold"] {
  color: var(--tutor-text) !important;
}
/* Section labels (e.g. "Enrolled Courses" under the number) */
.tutor-stats-card__label {
  color: var(--tutor-muted) !important;
}
/* "View All" and similar secondary links */
.tutor-dashboard-content a.tutor-color-secondary {
  color: var(--tutor-muted) !important;
}
.tutor-dashboard-content a.tutor-color-secondary:hover {
  color: var(--tutor-accent) !important;
}
/* "In Progress Courses", "My Courses" section headings — all Tutor v1/v2 variants */
.tutor-segment-title,
.tutor-dashboard-title,
.tutor-section-title,
.tutor-dashboard-content h2,
.tutor-dashboard-content h3,
.tutor-dashboard-content h4,
.tutor-dashboard-content h5,
.tutor-dashboard-content-inner h2,
.tutor-dashboard-content-inner h3,
.tutor-dashboard-content-inner h4,
.tutor-dashboard-content-inner h5 {
  color: var(--tutor-text) !important;
  font-weight: 700 !important;
}
/* Tutor LMS v2 utility color — overriding dark on dark */
.tutor-color-black,
.tutor-wrap .tutor-color-black {
  color: var(--tutor-text) !important;
}
.tutor-color-secondary,
.tutor-wrap .tutor-color-secondary {
  color: rgba(240,239,234,.65) !important;
}
.tutor-color-muted,
.tutor-wrap .tutor-color-muted,
.tutor-color-subdued {
  color: var(--tutor-muted) !important;
}
/* Profile name & rating area */
.tutor-dashboard-profile-name,
.tutor-dashboard-profile-bio {
  color: var(--tutor-text) !important;
}
.tutor-rating-value,
.tutor-rating-count {
  color: var(--tutor-muted) !important;
}
/* "Completed Lessons" and progress % labels */
.tutor-course-progress-label,
.tutor-course-progress-value {
  color: var(--tutor-muted) !important;
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
.tutor-wrap input[type="date"],
.tutor-wrap input[type="search"],
.tutor-wrap textarea,
.tutor-wrap select,
.tutor-wrap .tutor-select select,
.tutor-wrap .flatpickr-input,
.tutor-wrap .tutor-date-range-picker input {
  background: rgba(255,255,255,.06) !important;
  border: 1px solid var(--tutor-border) !important;
  color: var(--tutor-text) !important;
  border-radius: 8px !important;
}
.tutor-wrap input::placeholder,
.tutor-wrap textarea::placeholder {
  color: var(--tutor-muted) !important;
}
/* Native select arrow color on dark bg */
.tutor-wrap select {
  color-scheme: dark !important;
}

/* ── Empty-state boxes ("No Data Found") — dark, not white ── */
/* Also cover Tutor's hardcoded utility bg classes (not using CSS vars) */
html body .tutor-bg-white,
html body .tutor-bg-grey,
html body .tutor-bg-gray,
.tutor-empty-state,
.tutor-wrap .tutor-bg-white,
.tutor-course-segment .tutor-empty,
[class*="tutor-empty-state"],
.tutor-dashboard-content .tutor-list-empty,
.tutor-no-course-banner {
  background: var(--tutor-surface) !important;
  background-color: var(--tutor-surface) !important;
  border: 1px solid var(--tutor-border) !important;
  border-radius: var(--tutor-radius) !important;
  color: var(--tutor-muted) !important;
}
/* Text and icon inside empty states */
.tutor-empty-state *,
.tutor-wrap .tutor-bg-white *,
.tutor-dashboard-content .tutor-list-empty * {
  color: var(--tutor-muted) !important;
}

/* ── HIDE Reviews (not needed) ── */
.tutor-dashboard-menu-reviews,
.tutor-dashboard-permalinks .tutor-dashboard-menu-reviews,
.tutor-dashboard-menu li a[href*="reviews"],
.tutor-dashboard-menu li:has(a[href*="reviews"]),
[data-page="tutor-reviews"],
.tutor-reviews-section {
  display: none !important;
}

/* ── Profile banner / student header ── */
.tutor-dashboard-banner,
.tutor-student-top-area,
.tutor-dashboard-student-header,
.tutor-profile-header-wrap {
  background: var(--tutor-surface) !important;
  border-bottom: 1px solid var(--tutor-border) !important;
  padding-top: 24px !important;
  padding-bottom: 24px !important;
}
.tutor-dashboard-banner *,
.tutor-student-top-area *,
.tutor-dashboard-student-header * {
  color: var(--tutor-text) !important;
}

/* ── Sidebar menu: ensure all labels readable ── */
.tutor-dashboard-permalinks .tutor-dashboard-menu-item-link,
.tutor-dashboard-permalinks .tutor-dashboard-menu-item-link span,
.tutor-dashboard-menu li a,
.tutor-dashboard-menu li button,
.tutor-dashboard-menu .tutor-menu-item-label {
  color: rgba(240,239,234,.78) !important;
}
/* Instructor section label in sidebar */
.tutor-dashboard-menu .tutor-dashboard-menu-label,
.tutor-dashboard-permalinks .tutor-dashboard-menu-label {
  color: var(--tutor-muted) !important;
  font-size: .75rem !important;
  text-transform: uppercase !important;
  letter-spacing: .06em !important;
  padding: 16px 16px 6px !important;
}

/* ── Reduce excess whitespace between dashboard sections ── */
.tutor-dashboard-content-inner > * + * {
  margin-top: 24px !important;
}
.tutor-course-segment,
.tutor-my-course-part {
  padding: 0 !important;
  margin-bottom: 8px !important;
}
.tutor-dashboard-content {
  padding: 24px 28px !important;
}
/* Tighten gap above/below section headings */
.tutor-dashboard-content h2,
.tutor-dashboard-content h3,
.tutor-dashboard-content h4,
.tutor-dashboard-content h5,
.tutor-color-black {
  margin-top: 0 !important;
  margin-bottom: 12px !important;
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
/* Container bar (the gray/light strip behind Course Info / Reviews tabs) */
.tutor-tabs-nav,
.tutor-course-content-nav-tabs,
.tutor-course-nav-tab-items,
[class*="course-nav-tab"],
body.single-courses nav,
body.single-courses [class*="-tab-list"],
body.single-courses [class*="-tabs-wrap"],
body.single-courses [class*="-tabs-nav"] {
  background: var(--tutor-surface) !important;
  border-bottom: 1px solid var(--tutor-border) !important;
}
.tutor-tabs-nav li a,
.tutor-nav-tab,
.tutor-course-nav-tab-item {
  color: var(--tutor-muted) !important;
  border-color: transparent !important;
  background: transparent !important;
}
.tutor-tabs-nav li.tutor-is-active a,
.tutor-nav-tab.is-active,
.tutor-course-nav-tab-item.is-active {
  color: var(--tutor-text) !important;
  border-bottom-color: var(--tutor-accent) !important;
}

/* ── Course card thumbnail (dashboard In Progress section) ── */
.tutor-course-card-thumbnail,
.tutor-course-card .tutor-course-card__thumbnail,
.tutor-course-card__thumbnail-wrap,
.tutor-course-thumbnail,
.tutor-ratio,
.tutor-course-card .tutor-ratio {
  background: #161d2e !important;
}

/* ── Bottom gap — space between course content and footer ────────────────
   Theme uses .site-content / .site-main / #primary (from style.css inspection).
   body.pt101 is the theme's own body class — use it for max specificity.       */
html body.pt101.single-courses .site-content,
html body.pt101.single-courses .site-main,
html body.pt101.single-courses #content,
html body.pt101.single-courses #primary,
html body.pt101.single-courses main,
html body.single-courses .site-content,
html body.single-courses .site-main,
html body.single-courses #primary,
html body.single-courses main,
html body.single-courses .tutor-page-wrap,
html body.single-courses .tutor-single-course {
  padding-bottom: 80px !important;
}
/* Dashboard page bottom gap */
html body.pt101.tutor-screen-frontend-dashboard .site-content,
html body.pt101.tutor-screen-frontend-dashboard .site-main,
html body.pt101.tutor-screen-frontend-dashboard #primary,
html body.tutor-screen-frontend-dashboard .site-main,
html body.tutor-screen-frontend-dashboard #primary {
  padding-bottom: 64px !important;
}

/* ── About Course / description text — readable brightness ── */
body.single-courses .tutor-course-description p,
body.single-courses .tutor-course-description,
body.single-courses .tutor-course-description *,
body.single-courses [class*="course-desc"] p,
body.single-courses [class*="course-overview"] p {
  color: rgba(240,239,234,.88) !important;
  font-size: 1rem !important;
  line-height: 1.75 !important;
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

/* ═══════════════════════════════════════════════════════════════
   COURSE PLAYER / LESSON PAGE  (Turing College–inspired layout)
   Targets Tutor LMS v1 + v2 class names broadly to ensure
   the stylesheet wins against Tutor's own rules.
   ═══════════════════════════════════════════════════════════════ */

/* Full page background */
body.single-lesson,
body.single-quiz,
body.single-assignments,
#tutor-course-player,
.tutor-lesson-wrap {
  background: var(--tutor-bg) !important;
  color: var(--tutor-text) !important;
}

/* ── Sidebar container ── */
#tutor-course-player-sidebar,
.tutor-course-player-sidebar,
.tutor-course-topics-list-wrap,
.tutor-course-content-list-wrap,
#tutor-course-content-list,
.tutor-lead-info,
.tutor-popup-course-area {
  background: #111827 !important;
  border-right: 1px solid rgba(255,255,255,.08) !important;
  color: var(--tutor-text) !important;
}

/* ── Sidebar top bar (course title / close) ── */
.tutor-course-player-sidebar-header,
.tutor-course-player-sidebar .tutor-course-player-sidebar-title,
.tutor-course-topics-list-wrap .tutor-course-name {
  background: #0f1422 !important;
  color: var(--tutor-text) !important;
  border-bottom: 1px solid rgba(255,255,255,.08) !important;
  font-weight: 700 !important;
  font-size: .9375rem !important;
  padding: 16px 18px !important;
}

/* ── Topic header rows (e.g. "What Is Trading? — 0/1") ── */
.tutor-course-topic,
.tutor-course-topic-header,
.tutor-course-topic-title-wrap,
.tutor-course-topics-list .tutor-topic-head,
.tutor-accordion-item-header {
  background: #161d2e !important;
  border-bottom: 1px solid rgba(255,255,255,.07) !important;
  padding: 13px 16px !important;
}
.tutor-course-topic-header *,
.tutor-course-topic-title-wrap *,
.tutor-accordion-item-header * {
  color: var(--tutor-text) !important;
}
.tutor-course-topic-header:hover,
.tutor-accordion-item-header:hover {
  background: #1a2340 !important;
}
/* Progress count "0/1" */
.tutor-course-topic-progress,
.tutor-topic-count,
.tutor-course-topic-header .tutor-fs-7 {
  color: var(--tutor-muted) !important;
  font-size: .8125rem !important;
}

/* ── Lesson list items ── */
.tutor-course-content-list,
.tutor-course-topics-list ul,
.tutor-accordion-item-body {
  background: #111827 !important;
}
.tutor-course-content-list-item,
.tutor-course-content-list li,
.tutor-course-topics-list li {
  background: transparent !important;
  border-bottom: 1px solid rgba(255,255,255,.05) !important;
  padding: 0 !important;
}
.tutor-course-content-list-item a,
.tutor-course-topics-list li a,
.tutor-course-content-list-item .tutor-lesson-title {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  padding: 11px 18px 11px 22px !important;
  color: rgba(240,239,234,.75) !important;
  text-decoration: none !important;
  font-size: .875rem !important;
  line-height: 1.45 !important;
  transition: background .15s, color .15s !important;
}
.tutor-course-content-list-item a:hover,
.tutor-course-topics-list li a:hover {
  background: rgba(124,110,245,.1) !important;
  color: var(--tutor-text) !important;
}

/* Active / current lesson — purple left border */
.tutor-course-content-list-item.is-active,
.tutor-course-content-list-item.tutor-active,
.tutor-course-content-list-item.current-lesson {
  background: rgba(124,110,245,.12) !important;
  border-left: 3px solid var(--tutor-accent) !important;
}
.tutor-course-content-list-item.is-active a,
.tutor-course-content-list-item.tutor-active a {
  color: var(--tutor-text) !important;
  font-weight: 600 !important;
}

/* Completion circle / checkmark */
.tutor-course-content-list-item .tutor-lesson-completed-mark,
.tutor-course-content-list-item .tutor-round-checkbox,
.tutor-course-content-list-item input[type="checkbox"] {
  width: 17px !important;
  height: 17px !important;
  border-radius: 50% !important;
  border: 2px solid rgba(255,255,255,.2) !important;
  background: transparent !important;
  flex-shrink: 0 !important;
  accent-color: var(--tutor-accent) !important;
}
.tutor-course-content-list-item.is-completed .tutor-lesson-completed-mark,
.tutor-course-content-list-item .tutor-completed-checkmark {
  background: rgba(52,211,153,.15) !important;
  border-color: #34d399 !important;
  color: #34d399 !important;
}

/* ── Main content area ── */
#tutor-course-player-content,
.tutor-course-player-content,
.tutor-course-spotlight-wrap,
.tutor-lesson-content-wrap,
.tutor-spotlight-wrap {
  background: var(--tutor-bg) !important;
  color: var(--tutor-text) !important;
}

/* Content inner — constrain width like Turing College */
.tutor-course-player-content .tutor-course-content-outter,
.tutor-lesson-content-wrap .tutor-container,
.tutor-spotlight-wrap .tutor-container,
.tutor-course-spotlight-wrap > div,
.tutor-lesson-content {
  max-width: 780px !important;
  margin-left: auto !important;
  margin-right: auto !important;
  padding: 48px 40px !important;
}

/* Lesson title */
.tutor-lesson-content h1,
.tutor-course-player-content h1,
.tutor-spotlight-wrap h1,
.tutor-lesson-title-wrap h1,
.tutor-lesson-title-wrap h2 {
  font-size: 1.625rem !important;
  font-weight: 700 !important;
  color: var(--tutor-text) !important;
  letter-spacing: -.02em !important;
  margin: 0 0 24px !important;
  line-height: 1.25 !important;
}

/* Lesson body text */
.tutor-lesson-content p,
.tutor-course-player-content p,
.tutor-spotlight-wrap p,
#tutor-course-player-content p,
body.single-lesson .tutor-course-spotlight-wrap p {
  font-size: 1rem !important;
  line-height: 1.75 !important;
  color: #e8e6ff !important;
  margin-bottom: 1.25em !important;
}
/* All text inside the lesson content area */
#tutor-course-player-content,
#tutor-course-player-content *:not(a),
body.single-lesson .tutor-course-spotlight-wrap,
body.single-lesson [class*="lesson-content"] {
  color: rgba(240,239,234,.92) !important;
}

/* Lesson content headings */
.tutor-lesson-content h2,
.tutor-lesson-content h3,
.tutor-lesson-content h4 {
  color: var(--tutor-text) !important;
  font-weight: 700 !important;
  margin: 2em 0 .75em !important;
  letter-spacing: -.015em !important;
}
.tutor-lesson-content h2 { font-size: 1.3rem !important; }
.tutor-lesson-content h3 { font-size: 1.125rem !important; }

/* Lesson content lists */
.tutor-lesson-content ul,
.tutor-lesson-content ol {
  color: rgba(240,239,234,.88) !important;
  line-height: 1.75 !important;
  padding-left: 1.5em !important;
  margin-bottom: 1.25em !important;
}

/* Lesson content code */
.tutor-lesson-content pre,
.tutor-lesson-content code {
  background: rgba(255,255,255,.06) !important;
  border: 1px solid rgba(255,255,255,.08) !important;
  border-radius: 6px !important;
  color: #a5b4fc !important;
  font-size: .9em !important;
  padding: .2em .45em !important;
}
.tutor-lesson-content pre {
  padding: 16px 20px !important;
  overflow-x: auto !important;
}

/* ── Prev / Next lesson navigation ── */
.tutor-course-player-navigation,
.tutor-lesson-nav,
.tutor-course-lesson-nav {
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  padding: 24px 40px !important;
  max-width: 780px !important;
  margin: 0 auto !important;
  border-top: 1px solid rgba(255,255,255,.07) !important;
}
.tutor-course-player-navigation a,
.tutor-lesson-nav a,
.tutor-btn-prev-lesson,
.tutor-btn-next-lesson,
.tutor-btn-complete-lesson {
  background: rgba(124,110,245,.15) !important;
  border: 1px solid rgba(124,110,245,.3) !important;
  color: var(--tutor-accent) !important;
  border-radius: 8px !important;
  padding: 10px 20px !important;
  font-size: .875rem !important;
  font-weight: 600 !important;
  text-decoration: none !important;
  transition: background .18s, color .18s !important;
}
.tutor-course-player-navigation a:hover,
.tutor-btn-prev-lesson:hover,
.tutor-btn-next-lesson:hover {
  background: var(--tutor-accent) !important;
  color: #fff !important;
}
.tutor-btn-complete-lesson,
.tutor-btn-complete-lesson.tutor-btn-primary {
  background: var(--tutor-accent) !important;
  border-color: var(--tutor-accent) !important;
  color: #fff !important;
}
.tutor-btn-complete-lesson:hover {
  background: var(--tutor-accent-h) !important;
}

/* ── Video embed area ── */
.tutor-video-player,
.tutor-course-spotlight-video,
.tutor-lesson-video-wrap,
.tutor-video-wrap {
  background: #000 !important;
  border-radius: 10px !important;
  overflow: hidden !important;
  margin-bottom: 32px !important;
}

/* ── Quiz container ── */
.tutor-quiz-container,
.tutor-quiz-wrap {
  background: var(--tutor-surface) !important;
  border: 1px solid var(--tutor-border) !important;
  border-radius: var(--tutor-radius) !important;
  padding: 32px !important;
  color: var(--tutor-text) !important;
}
.tutor-quiz-container h2,
.tutor-quiz-container h3 {
  color: var(--tutor-text) !important;
}
.tutor-quiz-container .tutor-quiz-question {
  color: var(--tutor-text) !important;
  font-weight: 600 !important;
}
.tutor-quiz-container .tutor-quiz-option {
  background: rgba(255,255,255,.04) !important;
  border: 1px solid var(--tutor-border) !important;
  border-radius: 8px !important;
  color: var(--tutor-text) !important;
  padding: 12px 16px !important;
  margin-bottom: 8px !important;
  cursor: pointer !important;
  transition: background .15s !important;
}
.tutor-quiz-container .tutor-quiz-option:hover,
.tutor-quiz-container .tutor-quiz-option.selected {
  background: rgba(124,110,245,.15) !important;
  border-color: var(--tutor-accent) !important;
}

/* Scrollbar for sidebar */
#tutor-course-player-sidebar ::-webkit-scrollbar,
.tutor-course-topics-list-wrap ::-webkit-scrollbar { width: 4px; }
#tutor-course-player-sidebar ::-webkit-scrollbar-track,
.tutor-course-topics-list-wrap ::-webkit-scrollbar-track { background: #111827; }
#tutor-course-player-sidebar ::-webkit-scrollbar-thumb,
.tutor-course-topics-list-wrap ::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,.12);
  border-radius: 2px;
}

/* ── Mobile: stack sidebar on top ── */
@media (max-width: 768px) {
  .tutor-lesson-content,
  .tutor-course-spotlight-wrap > div {
    padding: 28px 20px !important;
  }
  .tutor-course-player-navigation {
    padding: 20px !important;
    flex-direction: column !important;
    gap: 12px !important;
  }
  .tutor-btn-prev-lesson,
  .tutor-btn-next-lesson,
  .tutor-btn-complete-lesson {
    width: 100% !important;
    text-align: center !important;
    justify-content: center !important;
  }
}

/* ═══════════════════════════════════════════════════════
   SINGLE COURSE PAGE  (course detail / info page)
   Uses body.single-courses prefix for high specificity
   ═══════════════════════════════════════════════════════ */

body.single-courses {
  background: var(--tutor-bg) !important;
  color: var(--tutor-text) !important;
}

/* Every card/box in the right sidebar → dark surface
   Using html+body chain for maximum specificity (beats Tutor's !important) */
html body .tutor-single-course-sidebar .tutor-card,
html body .tutor-single-course-sidebar > div,
html body .tutor-single-course-sidebar [class*="card"],
html body .tutor-single-course-sidebar [class*="enroll"],
html body .tutor-single-course-sidebar [class*="entry"],
html body .tutor-single-course-sidebar [class*="instructor"],
html body .tutor-single-course-sidebar [class*="widget"],
html body.single-courses .tutor-card,
html body.single-courses [class*="tutor-enroll"],
html body.single-courses [class*="course-entry"],
html body.single-courses .tutor-bg-white {
  background: var(--tutor-surface) !important;
  background-color: var(--tutor-surface) !important;
  border: 1px solid var(--tutor-border) !important;
  border-radius: var(--tutor-radius) !important;
  box-shadow: none !important;
}

/* All text inside the right sidebar → readable */
html body .tutor-single-course-sidebar,
html body .tutor-single-course-sidebar * {
  color: var(--tutor-text) !important;
}
/* Enrolled-on date pill (dark tooltip style) */
html body .tutor-single-course-sidebar [class*="enrolled-date"],
html body .tutor-single-course-sidebar [class*="enroll-date"] {
  background: rgba(124,110,245,.15) !important;
  border: 1px solid rgba(124,110,245,.3) !important;
  border-radius: 6px !important;
  padding: 6px 12px !important;
}

/* Secondary meta (Beginner, Total Enrolled, Last Updated) */
.tutor-single-course-sidebar [class*="meta"],
.tutor-single-course-sidebar [class*="info-item"],
body.single-courses [class*="course-level"],
body.single-courses [class*="course-meta"] * {
  color: rgba(240,239,234,.72) !important;
}

/* ── Tabs bar (Course Info / Reviews / Announcements) → dark ────────────
   Actual HTML: <nav class="tutor-nav" tutor-priority-nav="">
   The grey background is on the parent wrapper div, not the nav itself.
   Use :has() to target the wrapping div, and the attribute selector for precision. */
/* The nav itself */
html body.single-courses nav[tutor-priority-nav],
html body.single-courses nav.tutor-nav {
  background: #0d0f1a !important;
  background-color: #0d0f1a !important;
}
/* The sticky wrapper inside the course tab area */
html body.single-courses .tutor-course-details-tab .tutor-is-sticky,
html body.single-courses .tutor-course-details-tab,
html body.single-courses [class*="course-details-tab"],
html body.single-courses div:has(> nav[tutor-priority-nav]),
html body.single-courses div:has(> nav.tutor-nav) {
  background: #0d0f1a !important;
  background-color: #0d0f1a !important;
  border-bottom: 1px solid rgba(255,255,255,.08) !important;
}
/* Tab link text — muted default, bright on active */
html body.single-courses nav[tutor-priority-nav] .tutor-nav-link,
html body.single-courses nav.tutor-nav .tutor-nav-link,
html body.single-courses .tutor-nav-link {
  color: rgba(240,239,234,.65) !important;
  background: transparent !important;
}
html body.single-courses .tutor-nav-link.is-active,
html body.single-courses .tutor-nav-link.active {
  color: var(--tutor-text) !important;
  border-bottom-color: var(--tutor-accent) !important;
}

/* About Course / description text — actual class from inspected HTML: cdp-hero-sub */
body.single-courses .cdp-hero-sub,
body.single-courses p.cdp-hero-sub,
body.single-courses [class*="cdp-hero"],
body.single-courses .tutor-course-description,
body.single-courses .tutor-course-description p,
body.single-courses .tutor-course-description *,
body.single-courses [class*="tab-content"] p,
body.single-courses [class*="course-detail"] p,
body.single-courses .tutor-single-course-main-content p {
  color: rgba(240,239,234,.9) !important;
  line-height: 1.75 !important;
}
/* Broad catch-all: any paragraph on the course page */
html body.single-courses p {
  color: rgba(240,239,234,.88) !important;
}

/* "About Course" / "Course Content" headings */
body.single-courses .tutor-single-course-content h2,
body.single-courses .tutor-single-course-content h3,
body.single-courses .tutor-single-course-content h4 {
  color: var(--tutor-text) !important;
  font-weight: 700 !important;
  font-size: 1.125rem !important;
  margin-bottom: 10px !important;
}

/* Course content accordion (curriculum on the detail page) */
body.single-courses .tutor-accordion-item {
  border: 1px solid var(--tutor-border) !important;
  border-radius: 8px !important;
  overflow: hidden !important;
  margin-bottom: 8px !important;
}
body.single-courses .tutor-accordion-item-header,
body.single-courses [class*="accordion-item-header"] {
  background: var(--tutor-surface) !important;
  color: var(--tutor-text) !important;
  font-weight: 600 !important;
}
body.single-courses .tutor-accordion-item-body,
body.single-courses [class*="accordion-item-body"] {
  background: rgba(19,22,43,.7) !important;
}
body.single-courses .tutor-accordion-item-body * {
  color: rgba(240,239,234,.78) !important;
}

/* "Outline" button (Complete Course) → dark border style */
body.single-courses .tutor-btn-outline-primary,
.tutor-single-course-sidebar .tutor-btn-outline-primary {
  background: transparent !important;
  border: 1px solid var(--tutor-accent) !important;
  color: var(--tutor-accent) !important;
}
body.single-courses .tutor-btn-outline-primary:hover {
  background: var(--tutor-accent) !important;
  color: #fff !important;
}

/* ═══════════════════════════════════════════════════════════════
   COURSE PLAYER — use #tutor-course-player ID for high specificity
   and body.single-lesson (correct Tutor LMS body class)
   ═══════════════════════════════════════════════════════════════ */

/* Sidebar: force dark on EVERYTHING inside it */
#tutor-course-player #tutor-course-player-sidebar,
#tutor-course-player .tutor-course-player-sidebar,
body.single-lesson #tutor-course-player-sidebar,
body.single-lesson .tutor-course-player-sidebar {
  background: #111827 !important;
  background-color: #111827 !important;
}

/* ── Force dark on EVERY element inside the sidebar ─────────────────────────
   Use the most aggressive selector possible. Tutor v2 uses many class variants
   so we target by parent ID/class + wildcard, then restore specific overrides.  */
#tutor-course-player #tutor-course-player-sidebar,
#tutor-course-player .tutor-course-player-sidebar,
#tutor-course-player .tutor-popup-course-area,
#tutor-course-player .tutor-lead-info,
body.single-lesson #tutor-course-player-sidebar,
body.single-lesson .tutor-course-player-sidebar,
body.single-lesson .tutor-popup-course-area,
body.single-lesson .tutor-lead-info {
  background: #111827 !important;
  background-color: #111827 !important;
}

/* All descendants — wipe any white/grey bg Tutor injects */
#tutor-course-player #tutor-course-player-sidebar *,
#tutor-course-player .tutor-course-player-sidebar *,
#tutor-course-player .tutor-popup-course-area *,
#tutor-course-player .tutor-lead-info *,
body.single-lesson #tutor-course-player-sidebar *,
body.single-lesson .tutor-course-player-sidebar *,
body.single-lesson .tutor-popup-course-area *,
body.single-lesson .tutor-lead-info * {
  background-color: #111827 !important;
}

/* Restore: topic/accordion HEADERS → slightly lighter */
#tutor-course-player .tutor-accordion-item-header,
#tutor-course-player [class*="topic-header"],
#tutor-course-player [class*="topic-head"],
#tutor-course-player [class*="section-header"],
body.single-lesson [class*="topic-header"],
body.single-lesson [class*="topic-head"] {
  background-color: #161d2e !important;
}
#tutor-course-player .tutor-accordion-item-header:hover,
#tutor-course-player [class*="topic-header"]:hover {
  background-color: #1a2340 !important;
}

/* Active lesson item — purple tint */
#tutor-course-player .tutor-course-content-list-item.is-active,
#tutor-course-player [class*="content-list-item"].is-active,
#tutor-course-player [class*="content-list-item"].current,
#tutor-course-player [class*="topic-item"].is-active,
#tutor-course-player [class*="lesson-item"].is-active,
body.single-lesson [class*="topic-item"].is-active {
  background-color: rgba(124,110,245,.15) !important;
  border-left: 3px solid var(--tutor-accent) !important;
}

/* All sidebar text: readable on dark background */
#tutor-course-player #tutor-course-player-sidebar,
#tutor-course-player #tutor-course-player-sidebar *,
#tutor-course-player .tutor-course-player-sidebar,
#tutor-course-player .tutor-course-player-sidebar *,
#tutor-course-player .tutor-popup-course-area,
#tutor-course-player .tutor-popup-course-area *,
body.single-lesson #tutor-course-player-sidebar,
body.single-lesson #tutor-course-player-sidebar *,
body.single-lesson .tutor-course-player-sidebar *,
body.single-lesson .tutor-popup-course-area * {
  color: rgba(240,239,234,.82) !important;
}
#tutor-course-player #tutor-course-player-sidebar a:hover,
body.single-lesson .tutor-course-player-sidebar a:hover {
  color: #fff !important;
}
/* Active / currently playing lesson */
#tutor-course-player [class*="content-list-item"].is-active a,
#tutor-course-player [class*="content-list-item"].is-active span,
#tutor-course-player [class*="topic-item"].is-active a,
#tutor-course-player [class*="topic-item"].is-active span {
  color: var(--tutor-text) !important;
  font-weight: 600 !important;
}

/* Video: full width — do NOT touch overflow or aspect-ratio,
   Plyr.js uses padding-top:56.25% intrinsic sizing — breaking it makes video collapse.
   Only set width and background; let Plyr control height via its own padding trick. */
#tutor-course-player .tutor-video-player,
#tutor-course-player [class*="video-player"],
#tutor-course-player [class*="video-wrap"],
#tutor-course-player [class*="spotlight-video"],
#tutor-course-player .plyr,
#tutor-course-player .plyr__video-wrapper {
  width: 100% !important;
  max-width: 100% !important;
  background: #000 !important;
}
/* Do NOT set height: 100% here — Plyr's padding-top % technique handles it.
   Setting height: 100% on the wrapper would collapse it if parent has no height. */
#tutor-course-player video {
  display: block !important;
  max-width: 100% !important;
}
#tutor-course-player iframe {
  display: block !important;
  width: 100% !important;
  /* Let the parent's padding-top set the height, don't force 100% */
}

/* Lesson content area: constrain text width, bright text */
#tutor-course-player [class*="lesson-content"],
#tutor-course-player [class*="spotlight-wrap"] .tutor-container {
  max-width: 760px !important;
  margin: 0 auto !important;
  padding: 40px 36px !important;
  color: rgba(240,239,234,.9) !important;
}
#tutor-course-player [class*="lesson-content"] p,
#tutor-course-player [class*="spotlight-wrap"] p {
  font-size: 1.0625rem !important;
  line-height: 1.8 !important;
  color: rgba(240,239,234,.88) !important;
  margin-bottom: 1.3em !important;
}
#tutor-course-player [class*="lesson-content"] h1,
#tutor-course-player [class*="lesson-content"] h2,
#tutor-course-player [class*="lesson-content"] h3 {
  color: var(--tutor-text) !important;
  font-weight: 700 !important;
  margin-bottom: .5em !important;
}

/* Prev / Next navigation bar */
#tutor-course-player [class*="course-player-footer"],
#tutor-course-player [class*="player-content-footer"],
#tutor-course-player [class*="lesson-nav"],
.tutor-course-player-content-footer {
  background: var(--tutor-surface) !important;
  border-top: 1px solid var(--tutor-border) !important;
  padding: 16px 32px !important;
  display: flex !important;
  justify-content: center !important;
  gap: 16px !important;
}
/* Prev/Next/Complete buttons — cover all known Tutor v1+v2 class variants */
#tutor-course-player [class*="prev-content"],
#tutor-course-player [class*="next-content"],
#tutor-course-player [class*="prev-btn"],
#tutor-course-player [class*="next-btn"],
#tutor-course-player [class*="complete-lesson"],
#tutor-course-player [class*="footer"] a,
#tutor-course-player [class*="footer"] button,
.tutor-prev-content,
.tutor-next-content,
.tutor-btn-prev-content,
.tutor-btn-next-content,
.tutor-btn-complete-lesson,
.tutor-btn-prev-lesson,
.tutor-btn-next-lesson,
.tutor-course-player-content-footer a,
.tutor-course-player-content-footer button {
  background: rgba(124,110,245,.15) !important;
  border: 1px solid rgba(124,110,245,.35) !important;
  color: var(--tutor-accent) !important;
  border-radius: 8px !important;
  padding: 10px 22px !important;
  font-weight: 600 !important;
  font-size: .875rem !important;
  text-decoration: none !important;
  transition: background .18s !important;
}
#tutor-course-player [class*="prev-content"]:hover,
#tutor-course-player [class*="next-content"]:hover,
.tutor-btn-prev-content:hover,
.tutor-btn-next-content:hover {
  background: var(--tutor-accent) !important;
  color: #fff !important;
}
/* Complete lesson = solid accent */
#tutor-course-player [class*="complete-lesson"],
.tutor-btn-complete-lesson {
  background: var(--tutor-accent) !important;
  border-color: var(--tutor-accent) !important;
  color: #fff !important;
}

/* Course player bottom padding */
#tutor-course-player {
  padding-bottom: 0 !important;
}
body.single-lesson {
  padding-bottom: 0 !important;
}
body.single-lesson #tutor-course-player {
  min-height: calc(100vh - 80px) !important;
}
</style>
    <?php
}, 100 );


/* ── Force-override Tutor LMS elements that resist CSS (JS runs after all scripts) ── */
/* No is_singular gate — course page may be a WP page type, detect via DOM instead */
add_action( 'wp_footer', function () {
    ?>
<script>
(function(){
  function tutorFix(){
    /* Guard: only run on Tutor course pages */
    if(!document.querySelector('.tutor-course-details-tab') &&
       !document.querySelector('.tutor-accordion-item') &&
       !document.querySelector('.tutor-single-course-wrap')){ return; }

    /* ── Tab links: generous padding ── */
    document.querySelectorAll('.tutor-nav-link').forEach(function(el){
      el.style.setProperty('padding','18px 28px','important');
    });

    /* ── Tab content panels: top breathing room ── */
    document.querySelectorAll('.tutor-tab,#tutor-course-details-tab-info,.tutor-tab-item').forEach(function(el){
      el.style.setProperty('padding-top','48px','important');
    });

    /* ── Tab bar wrapper and nav: match page background ── */
    ['.tutor-course-details-tab .tutor-is-sticky','.tutor-course-details-tab','nav.tutor-nav','[tutor-priority-nav]'].forEach(function(s){
      document.querySelectorAll(s).forEach(function(el){
        el.style.setProperty('background','#0d0f1a','important');
        el.style.setProperty('background-color','#0d0f1a','important');
      });
    });

    /* ── Hide Reviews tab ── */
    document.querySelectorAll('[data-tutor-nav-target="tutor-course-details-tab-reviews"]').forEach(function(el){
      var li=el.closest('li');
      if(li) li.style.setProperty('display','none','important');
      el.style.setProperty('display','none','important');
    });
    document.querySelectorAll('#tutor-course-details-tab-reviews').forEach(function(el){
      el.style.setProperty('display','none','important');
    });

    /* ── Description paragraphs: full brightness ── */
    document.querySelectorAll(
      '#tutor-course-details-tab-info p,.tutor-tab-item p,'+
      '.tutor-course-description p,.tutor-single-course-main-content p,.tutor-course-details-page p'
    ).forEach(function(el){
      el.style.setProperty('color','rgba(240,239,234,0.9)','important');
    });

    /* ── Accordion spacing ── */
    var items = document.querySelectorAll('.tutor-accordion-item');
    if(items.length){
      var container = items[0].parentElement;
      container.style.setProperty('margin-top','24px','important');
      container.style.setProperty('display','flex','important');
      container.style.setProperty('flex-direction','column','important');
      container.style.setProperty('gap','10px','important');
      items.forEach(function(item){
        item.style.setProperty('margin-bottom','0','important');
        item.style.setProperty('border-radius','14px','important');
        item.style.setProperty('border','1px solid rgba(255,255,255,.08)','important');
        item.style.setProperty('overflow','hidden','important');
      });
      document.querySelectorAll('.tutor-accordion-item-header').forEach(function(h){
        h.style.setProperty('background','#161929','important');
        h.style.setProperty('padding','20px 24px','important');
      });
      document.querySelectorAll('.tutor-accordion-item-body').forEach(function(b){
        b.style.setProperty('background','#0f1120','important');
      });
      document.querySelectorAll('li.tutor-course-content-list-item').forEach(function(el){
        el.style.setProperty('padding','14px 22px','important');
        el.style.setProperty('display','flex','important');
        el.style.setProperty('justify-content','space-between','important');
        el.style.setProperty('align-items','center','important');
      });
    }

    /* ── Section headings inside tab ── */
    document.querySelectorAll(
      '#tutor-course-details-tab-info h2,#tutor-course-details-tab-info h3,'+
      '.tutor-tab-item h2,.tutor-tab-item h3'
    ).forEach(function(h){
      h.style.setProperty('margin-bottom','12px','important');
      h.style.setProperty('font-weight','700','important');
      h.style.setProperty('letter-spacing','-0.02em','important');
    });
  }

  /* Guard flag: prevents MO from re-triggering during our own mutations */
  var pt101Applying=false;
  var _orig=tutorFix;
  tutorFix=function(){
    if(pt101Applying) return;
    pt101Applying=true;
    _orig();
    setTimeout(function(){ pt101Applying=false; }, 0);
  };

  if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded',tutorFix);
  } else {
    tutorFix();
  }
  window.addEventListener('load', function(){
    tutorFix();
    [200,600,1200,2500].forEach(function(ms){ setTimeout(tutorFix, ms); });
  });

  /* MutationObserver: re-apply when new accordion nodes appear */
  var mo=new MutationObserver(function(mutations){
    if(pt101Applying) return;
    var needsFix=false;
    for(var i=0;i<mutations.length;i++){
      var nodes=mutations[i].addedNodes;
      for(var j=0;j<nodes.length;j++){
        var n=nodes[j];
        if(n.nodeType===1&&(
          n.classList.contains('tutor-accordion-item')||
          n.classList.contains('tutor-accordion')||
          (n.querySelector&&n.querySelector('.tutor-accordion-item'))
        )){ needsFix=true; break; }
      }
      if(needsFix) break;
    }
    if(needsFix) tutorFix();
  });
  mo.observe(document.body,{childList:true,subtree:true});
})();
</script>
    <?php
}, 999 );

/* ── Course page: Turing College-style polish ── */
/* wp_footer (priority 101) = output AFTER all enqueued stylesheets in wp_head,
   so our !important rules are the last declarations and always win over Tutor CSS. */
add_action( 'wp_footer', function () {
    if ( is_singular( 'lesson' ) ) return; // Don't override lesson pages
    ?>
<style id="pt101-course-polish">

/* Tab bar: spacious with clear breathing room */
body.single-courses .tutor-nav-link {
  padding: 18px 28px !important;
  font-size: 0.9375rem !important;
  font-weight: 500 !important;
  letter-spacing: 0.01em !important;
}
body.single-courses .tutor-nav-link.is-active,
body.single-courses .tutor-nav-link.active {
  font-weight: 600 !important;
  border-bottom-width: 2px !important;
}

/* Tab content: generous breathing room (actual class from inspected HTML) */
.tutor-tab,
#tutor-course-details-tab-info,
.tutor-tab-item {
  padding-top: 48px !important;
}

/* Section headings (About Course, Course Content): match site tokens */
body.single-courses .tutor-course-tab-content h2,
body.single-courses .tutor-course-tab-content h3,
body.single-courses .tutor-single-course-content h2,
body.single-courses .tutor-single-course-content h3 {
  font-size: 1.125rem !important;
  font-weight: 700 !important;
  letter-spacing: -0.02em !important;
  color: #f0f0f5 !important;
  margin-bottom: 12px !important;
}

/* ── Accordion: no body-class prefix — works regardless of WP post type ──
   --bg-card: #161929  --r-md: 14px  --border-dark: rgba(255,255,255,.08) */

/* Each item: card radius + border */
.tutor-accordion-item {
  border-radius: 14px !important;
  border: 1px solid rgba(255,255,255,.08) !important;
  overflow: hidden !important;
  margin-bottom: 0 !important;
}
/* Gap between boxes via sibling selector */
.tutor-accordion-item + .tutor-accordion-item {
  margin-top: 10px !important;
}
/* Space between "Course Content" heading and first box */
.tutor-accordion-item:first-child {
  margin-top: 24px !important;
}

/* Header: --bg-card background, generous padding */
.tutor-accordion-item-header {
  background: #161929 !important;
  padding: 20px 24px !important;
}
.tutor-accordion-item-header * {
  font-size: 0.9375rem !important;
  font-weight: 600 !important;
  color: #f0f0f5 !important;
}

/* Body: slightly deeper dark */
.tutor-accordion-item-body {
  background: #0f1120 !important;
}

/* Lesson list items: generous padding on the li directly */
li.tutor-course-content-list-item {
  padding: 14px 22px !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
}
/* Links inside items: no extra padding (li handles it) */
.tutor-course-content-list-item a,
.tutor-course-topics-list li a,
.tutor-course-content-list-item .tutor-lesson-title {
  padding: 0 !important;
  gap: 12px !important;
  font-size: 0.875rem !important;
  line-height: 1.5 !important;
  color: rgba(240,240,245,.75) !important;
}
.tutor-course-content-list-item a:hover,
.tutor-course-topics-list li a:hover {
  background: rgba(124,110,245,.1) !important;
  color: #f0f0f5 !important;
}
.tutor-course-content-list-item {
  border-bottom: 1px solid rgba(255,255,255,.04) !important;
}
.tutor-course-content-list-item:last-child {
  border-bottom: none !important;
}

/* Keep scoped rules as well for specificity on confirmed single-courses pages */
body.single-courses .tutor-accordion,
body.single-courses [class*="tutor-accordion-list"] {
  margin-top: 20px !important;
  display: flex !important;
  flex-direction: column !important;
  gap: 10px !important;
}

/* Hero / thumbnail: dark fallback */
body.single-courses .tutor-single-course-hero,
body.single-courses [class*="course-hero"],
body.single-courses .tutor-ratio,
body.single-courses .tutor-course-thumbnail {
  background: #161929 !important;
}

/* Sidebar card: --r-md radius, proper padding */
body.single-courses .tutor-single-course-sidebar .tutor-card,
body.single-courses .tutor-single-course-sidebar > div {
  border-radius: 14px !important;
  padding: 22px !important;
}


</style>
    <?php
}, 101 );

/* ── Dashboard: Turing College-inspired clean UI ── */
add_action( 'wp_head', function () {
    ?>
<style id="pt101-dashboard-polish">

/* Cards: site tokens — #161929 bg, 14px radius, subtle border */
body.tutor-frontend .tutor-card,
body.tutor-frontend .tutor-course-card,
body.tutor-screen-frontend-dashboard .tutor-card {
  background: #161929 !important;
  border: 1px solid rgba(255,255,255,.06) !important;
  border-radius: 14px !important;
  box-shadow: none !important;
  transition: border-color .18s !important;
}
body.tutor-frontend .tutor-course-card:hover {
  border-color: rgba(124,110,245,.3) !important;
}

/* Stats cards */
body.tutor-screen-frontend-dashboard .tutor-stats-card,
body.tutor-screen-frontend-dashboard [class*="stats-card"] {
  background: #161929 !important;
  border: 1px solid rgba(255,255,255,.06) !important;
  border-radius: 14px !important;
  padding: 22px !important;
}
body.tutor-screen-frontend-dashboard .tutor-stats-card__count,
body.tutor-screen-frontend-dashboard [class*="stats-card"] [class*="count"] {
  font-size: 2rem !important;
  font-weight: 700 !important;
  letter-spacing: -0.03em !important;
  color: #f0f0f5 !important;
}
body.tutor-screen-frontend-dashboard .tutor-stats-card__label {
  font-size: 0.8125rem !important;
  color: rgba(240,240,245,.55) !important;
  margin-top: 4px !important;
}

/* Progress bar: thin accent line */
body.tutor-frontend .tutor-progress-bar,
body.tutor-screen-frontend-dashboard .tutor-progress-bar {
  height: 4px !important;
  border-radius: 99px !important;
  background: rgba(255,255,255,.1) !important;
}
body.tutor-frontend .tutor-progress-bar__fill,
body.tutor-frontend .tutor-progress-bar > span,
body.tutor-screen-frontend-dashboard .tutor-progress-bar__fill {
  background: #7c6ef5 !important;
  border-radius: 99px !important;
}
body.tutor-frontend .tutor-course-progress-value {
  color: #7c6ef5 !important;
  font-weight: 600 !important;
  font-size: 0.8125rem !important;
}

/* Course card title */
body.tutor-frontend .tutor-course-card__title,
body.tutor-frontend .tutor-course-name {
  font-size: 0.9375rem !important;
  font-weight: 600 !important;
  color: #f0f0f5 !important;
  letter-spacing: -0.01em !important;
}

/* Dashboard section headings */
body.tutor-screen-frontend-dashboard .tutor-dashboard-content h2,
body.tutor-screen-frontend-dashboard .tutor-dashboard-content h3,
body.tutor-screen-frontend-dashboard .tutor-segment-title,
body.tutor-screen-frontend-dashboard .tutor-section-title {
  font-size: 1.25rem !important;
  font-weight: 700 !important;
  letter-spacing: -0.025em !important;
  color: #f0f0f5 !important;
  margin-bottom: 20px !important;
}

/* Sidebar nav: clean, pill-style, accent on active */
body.tutor-screen-frontend-dashboard .tutor-dashboard-permalinks .tutor-dashboard-menu-item-link {
  border-radius: 8px !important;
  padding: 10px 16px !important;
  margin-bottom: 2px !important;
  font-size: 0.875rem !important;
  font-weight: 500 !important;
  border-left: 3px solid transparent !important;
  color: rgba(240,240,245,.65) !important;
}
body.tutor-screen-frontend-dashboard .tutor-dashboard-permalinks .tutor-dashboard-menu-item.active .tutor-dashboard-menu-item-link {
  background: rgba(124,110,245,.12) !important;
  border-left-color: #7c6ef5 !important;
  color: #f0f0f5 !important;
  font-weight: 600 !important;
}
body.tutor-screen-frontend-dashboard .tutor-dashboard-permalinks .tutor-dashboard-menu-item-link:hover {
  background: rgba(255,255,255,.05) !important;
  color: #f0f0f5 !important;
}

/* Primary buttons: pill style matching site buttons */
body.tutor-frontend .tutor-btn-primary {
  border-radius: 100px !important;
  font-size: 0.875rem !important;
  font-weight: 600 !important;
  padding: 9px 22px !important;
  background: #7c6ef5 !important;
  border-color: #7c6ef5 !important;
}

</style>
    <?php
}, 101 );

/* ── Lesson player: JS force-override sidebar (same pattern as course page) ── */
add_action( 'wp_footer', function () {
    if ( true ) return;
    ?>
<script>
(function(){
  function lessonFix(){
    /* Sidebar: force dark */
    ['.tutor-lesson-sidebar','.tutor-course-single-sidebar-wrapper'].forEach(function(s){
      document.querySelectorAll(s).forEach(function(el){
        el.style.setProperty('background','#111827','important');
        el.style.setProperty('background-color','#111827','important');
      });
    });
    /* All text inside sidebar: readable */
    document.querySelectorAll('.tutor-lesson-sidebar *,.tutor-course-single-sidebar-wrapper *').forEach(function(el){
      var bg=getComputedStyle(el).backgroundColor;
      if(bg==='rgb(239, 241, 246)'||bg==='rgb(255, 255, 255)'){
        el.style.setProperty('background-color','#111827','important');
      }
    });
  }
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',lessonFix);}
  else{lessonFix();}
  window.addEventListener('load',lessonFix);
})();
</script>
    <?php
}, 999 );


/* ── Lesson completion bridge (single reliable layer) ── */
add_action( 'wp_footer', function () {
    if ( true ) return;
    ?>
<script>
(function(){
  function all(sel){ return Array.prototype.slice.call(document.querySelectorAll(sel)); }
  function visible(el){ return !!(el && el.offsetParent !== null); }
  function txt(el){ return ((el && el.textContent) || '').replace(/\s+/g,' ').trim().toLowerCase(); }

  function completionTargets(){
    var sels = [
      'form[action*="complete"] button[type="submit"]',
      'form[action*="complete"] input[type="submit"]',
      'button[data-tutor-action*="complete"]',
      'a[data-tutor-action*="complete"]',
      'button[name*="complete"]',
      '.tutor-lesson-mark-complete',
      'a[href*="complete_lesson"]',
      'a[href*="mark_as_complete"]'
    ];
    var out = [];
    sels.forEach(function(s){ all(s).forEach(function(el){ if(out.indexOf(el)===-1) out.push(el); }); });
    return out.filter(visible);
  }

  function isMarkButton(el){
    if(!el) return false;
    var t = txt(el);
    return t.indexOf('mark as complete') !== -1 || t.indexOf('complete lesson') !== -1;
  }

  document.addEventListener('click', function(e){
    var trigger = e.target.closest('button, a, [role="button"]');
    if(!isMarkButton(trigger)) return;

    var targets = completionTargets();
    if(!targets.length) return;

    // If this click is already on a real completion target, keep native flow.
    if(targets.indexOf(trigger) !== -1) return;

    // Forward topbar/clone button clicks to Tutor's real completion control.
    e.preventDefault();
    var target = targets[0];
    var form = target.closest && target.closest('form');
    if(form && typeof form.submit === 'function') {
      form.submit();
      return;
    }
    target.click();
  }, true);
})();
</script>
    <?php
}, 1200 );

/* ── Course info text placement refinement ── */
add_action( 'wp_head', function () {
    if ( ! is_singular( 'courses' ) ) return;
    ?>
<style id="pt101-course-info-placement-refine-v1">
/* Pull content closer to tabs and keep it in a readable column */
body.single-courses #tutor-course-details-tab-info,
body.single-courses .tutor-course-details-tab-info,
body.single-courses .tutor-tab-item,
body.single-courses [class*="course-tab-content"],
body.single-courses .tutor-single-course-main-content {
  padding-top: 20px !important;
}

body.single-courses .tutor-course-description,
body.single-courses .tutor-course-description p,
body.single-courses #tutor-course-details-tab-info p,
body.single-courses .tutor-tab-item p,
body.single-courses .tutor-single-course-main-content p {
  max-width: 820px !important;
  margin-left: 0 !important;
  margin-right: auto !important;
  text-align: left !important;
}

body.single-courses .tutor-course-description p,
body.single-courses #tutor-course-details-tab-info p,
body.single-courses .tutor-tab-item p,
body.single-courses .tutor-single-course-main-content p {
  line-height: 1.88 !important;
  font-size: 1.08rem !important;
  letter-spacing: 0 !important;
  margin-bottom: 16px !important;
}

/* Strong heading anchor and cleaner vertical rhythm */
body.single-courses .tutor-single-course-content h2,
body.single-courses .tutor-single-course-content h3,
body.single-courses #tutor-course-details-tab-info h2,
body.single-courses #tutor-course-details-tab-info h3 {
  margin-top: 0 !important;
  margin-bottom: 18px !important;
  font-size: clamp(1.75rem, 2.4vw, 2.2rem) !important;
  line-height: 1.18 !important;
  letter-spacing: -0.02em !important;
}

/* Keep section transitions tidy */
body.single-courses .tutor-single-course-content h3 + p,
body.single-courses #tutor-course-details-tab-info h3 + p {
  margin-top: 0 !important;
}

@media (max-width: 1024px) {
  body.single-courses .tutor-course-description,
  body.single-courses .tutor-course-description p,
  body.single-courses #tutor-course-details-tab-info p,
  body.single-courses .tutor-tab-item p,
  body.single-courses .tutor-single-course-main-content p {
    max-width: none !important;
  }

  body.single-courses .tutor-course-description p,
  body.single-courses #tutor-course-details-tab-info p,
  body.single-courses .tutor-tab-item p,
  body.single-courses .tutor-single-course-main-content p {
    font-size: 1rem !important;
    line-height: 1.8 !important;
  }
}
</style>
    <?php
}, 1500 );


/* ── Tutor lesson page: default structure + dark skin only ── */
add_action( 'wp_head', function () {
    if ( true ) return;
    ?>
<style id="pt101-lesson-default-dark-skin">
/* Keep Tutor default layout/spacing. Only recolor for site dark theme. */
body.single-lesson,
body.single-lesson #tutor-course-player,
body.single-lesson .tutor-wrap {
  background: #090f22 !important;
  color: #f0efea !important;
}

body.single-lesson .tutor-course-player-sidebar,
body.single-lesson #tutor-course-player-sidebar,
body.single-lesson .tutor-course-topics-list-wrap,
body.single-lesson .tutor-lesson-sidebar {
  background: #11192f !important;
  border-color: rgba(255,255,255,.10) !important;
}

body.single-lesson .tutor-course-topic-single-header,
body.single-lesson .tutor-lesson-topbar {
  background: #4866d9 !important;
  border-color: rgba(255,255,255,.14) !important;
}

body.single-lesson .tutor-course-topic-single-body,
body.single-lesson .tutor-single-entry-content,
body.single-lesson .tutor-lesson-content,
body.single-lesson .tutor-course-content-wrap {
  background: transparent !important;
}

body.single-lesson p,
body.single-lesson h1,
body.single-lesson h2,
body.single-lesson h3,
body.single-lesson h4,
body.single-lesson li,
body.single-lesson span,
body.single-lesson a {
  color: #f0efea !important;
}

body.single-lesson .tutor-text-muted,
body.single-lesson [class*="muted"],
body.single-lesson [class*="meta"] {
  color: rgba(240,239,234,.70) !important;
}

body.single-lesson button,
body.single-lesson .tutor-btn {
  border-color: rgba(255,255,255,.22) !important;
}

body.single-lesson .tutor-lesson-mark-complete,
body.single-lesson button[data-tutor-action*="complete"],
body.single-lesson a[data-tutor-action*="complete"] {
  background: #7c6ef5 !important;
  color: #fff !important;
}
</style>
    <?php
}, 5000 );


/* ── Lesson-only dark color pass (default Tutor layout) ── */
add_action( 'wp_head', function () {
    if ( true ) return;
    ?>
<style id="pt101-lesson-default-dark-skin-v2">
/* No layout changes: color/background only */
body.single-lesson .tutor-course-topics-list li,
body.single-lesson .tutor-course-topic,
body.single-lesson .tutor-topic-item,
body.single-lesson .tutor-course-content-list-item,
body.single-lesson .tutor-course-content-list-item a,
body.single-lesson .tutor-course-topics-list a {
  background: #121a2f !important;
  color: #f0efea !important;
  border-color: rgba(255,255,255,.10) !important;
}

body.single-lesson .tutor-course-topics-list li.tutor-active,
body.single-lesson .tutor-course-topics-list li.active,
body.single-lesson .tutor-course-content-list-item.is-active,
body.single-lesson .tutor-course-content-list-item.current {
  background: #1a2340 !important;
  color: #ffffff !important;
}

body.single-lesson .tutor-course-topics-list li * {
  color: inherit !important;
}

body.single-lesson .tutor-iconic-btn,
body.single-lesson .tutor-icon,
body.single-lesson [class*="tutor-icon"] {
  color: rgba(240,239,234,.78) !important;
}
</style>
    <?php
}, 5001 );


/* ── Lesson page: minimal safe dark skin (default Tutor controls intact) ── */
add_action( 'wp_head', function () {
    if ( true ) return; // Disabled: reverting lesson page to default Tutor LMS design
    if ( ! is_singular( 'lesson' ) ) return;
    ?>
<style id="pt101-lesson-default-dark-skin-safe-v3">
/* Structure/layout untouched: color only */
body.single-lesson,
body.single-lesson #tutor-course-player {
  background: #090f22 !important;
}

body.single-lesson #tutor-course-player-sidebar,
body.single-lesson .tutor-course-player-sidebar,
body.single-lesson .tutor-course-topics-list-wrap,
body.single-lesson .tutor-course-content-list-wrap {
  background: #101a32 !important;
  border-color: rgba(255,255,255,.10) !important;
}

body.single-lesson .tutor-course-content-list-item,
body.single-lesson .tutor-course-content-list li,
body.single-lesson .tutor-course-topics-list li,
body.single-lesson .tutor-topic-item {
  background: #111d37 !important;
  border-color: rgba(255,255,255,.10) !important;
}

body.single-lesson .tutor-course-content-list-item.is-active,
body.single-lesson .tutor-course-content-list-item.current,
body.single-lesson .tutor-course-topics-list li.tutor-active,
body.single-lesson .tutor-course-topics-list li.active {
  background: #1a2849 !important;
}

body.single-lesson .tutor-course-content-list-item *,
body.single-lesson .tutor-course-topics-list li * {
  color: #e9ecf5 !important;
}

body.single-lesson .tutor-course-topic-single-header,
body.single-lesson .tutor-lesson-topbar {
  background: #4b68de !important;
  border-color: rgba(255,255,255,.16) !important;
}

body.single-lesson .tutor-course-topic-single-body,
body.single-lesson .tutor-single-entry-content,
body.single-lesson .tutor-lesson-content {
  background: #0b1227 !important;
  color: #eef1f8 !important;
}

body.single-lesson .tutor-course-topic-single-body p,
body.single-lesson .tutor-single-entry-content p,
body.single-lesson .tutor-lesson-content p,
body.single-lesson .tutor-course-topic-single-body h1,
body.single-lesson .tutor-course-topic-single-body h2,
body.single-lesson .tutor-course-topic-single-body h3 {
  color: #eef1f8 !important;
}

/* Ensure default Tutor nav/completion controls stay visible */
body.single-lesson .tutor-lesson-nav a,
body.single-lesson .tutor-btn-complete-lesson,
body.single-lesson .tutor-lesson-mark-complete,
body.single-lesson button[data-tutor-action*="complete"],
body.single-lesson a[data-tutor-action*="complete"] {
  color: #ffffff !important;
  border-color: rgba(255,255,255,.26) !important;
}

body.single-lesson .tutor-progress-bar {
  background: rgba(255,255,255,.18) !important;
}
body.single-lesson .tutor-progress-bar__fill,
body.single-lesson .tutor-progress-bar > span {
  background: #7c6ef5 !important;
}
</style>
    <?php
}, 5100 );

/* ── Lesson page: reset theme dark background so Tutor LMS controls the canvas ── */
add_action( 'wp_head', function () {
    if ( ! is_singular( 'lesson' ) ) return;
    ?>
<style id="pt101-lesson-bg-reset">
/* The theme sets html/body.pt101 { background: #0d0f1a; color: #fff } globally.
   Reset on lesson pages so Tutor LMS's native layout shows correctly. */
html,
body.pt101.single-lesson,
body.single-lesson {
  background: #fff !important;
  color: #212327 !important;
}

/* Override theme's global white text rules for lesson content */
body.single-lesson h1,
body.single-lesson h2,
body.single-lesson h3,
body.single-lesson h4,
body.single-lesson h5,
body.single-lesson h6 {
  color: #1a1c23 !important;
}
body.single-lesson p,
body.single-lesson li,
body.single-lesson span,
body.single-lesson div,
body.single-lesson td,
body.single-lesson th,
body.single-lesson label,
body.single-lesson blockquote {
  color: #212327 !important;
}
body.single-lesson a {
  color: #5046e5 !important;
}
body.single-lesson a:hover {
  color: #3730a3 !important;
}
/* Muted/secondary text */
body.single-lesson .tutor-text-muted,
body.single-lesson [class*="muted"],
body.single-lesson [class*="meta"] {
  color: #6b7280 !important;
}

/* ── Footer: restore native styling (must come AFTER the broad overrides to win) ── */
body.single-lesson .site-footer {
  background: #f0efea !important;
  color: #111320 !important;
}
body.single-lesson .site-footer h4 {
  color: #000 !important;
}
body.single-lesson .site-footer a,
body.single-lesson .site-footer li,
body.single-lesson .site-footer span,
body.single-lesson .site-footer div,
body.single-lesson .site-footer p {
  color: #111320 !important;
}
body.single-lesson .site-footer .footer-legal-links a,
body.single-lesson .site-footer .footer-cookie-btn {
  color: rgba(17,19,32,.55) !important;
}

/* Restore header nav — white text on dark background.
   Target only the nav elements, NOT the dropdown panels (which have their own dark text). */
body.single-lesson .site-logo,
body.single-lesson .nav-dropdown-trigger,
body.single-lesson .primary-nav > li > a,
body.single-lesson .primary-nav > li > button,
body.single-lesson .btn-hdr-login,
body.single-lesson .btn-hdr-enroll {
  color: #f0f0f5 !important;
}

/* ── Mark-as-complete button: ensure visible ── */
body.single-lesson .tutor-lesson-mark-complete,
body.single-lesson .tutor-btn-complete-lesson,
body.single-lesson button[data-tutor-action*="complete"],
body.single-lesson a[data-tutor-action*="complete"],
body.single-lesson form[method="post"] button[type="submit"],
body.single-lesson [class*="complete-lesson"],
body.single-lesson [class*="mark-complete"] {
  display: inline-flex !important;
  visibility: visible !important;
  opacity: 1 !important;
  background: #5046e5 !important;
  color: #fff !important;
  border: none !important;
  border-radius: 8px !important;
  padding: 12px 28px !important;
  font-size: 0.9rem !important;
  font-weight: 600 !important;
  cursor: pointer !important;
  letter-spacing: -0.01em !important;
  transition: background 0.15s !important;
}
body.single-lesson .tutor-lesson-mark-complete:hover,
body.single-lesson .tutor-btn-complete-lesson:hover,
body.single-lesson button[data-tutor-action*="complete"]:hover,
body.single-lesson a[data-tutor-action*="complete"]:hover,
body.single-lesson [class*="complete-lesson"]:hover,
body.single-lesson [class*="mark-complete"]:hover {
  background: #3730a3 !important;
  color: #fff !important;
}

/* Other buttons */
body.single-lesson .tutor-btn,
body.single-lesson button:not(.site-footer button) {
  border-color: rgba(0,0,0,.15) !important;
}

/* Code blocks */
body.single-lesson pre,
body.single-lesson code {
  background: #f3f4f6 !important;
  border: 1px solid #e5e7eb !important;
  color: #1f2937 !important;
}
/* Content lists */
body.single-lesson .tutor-lesson-content ul,
body.single-lesson .tutor-lesson-content ol {
  color: #212327 !important;
}
</style>
    <?php
}, 99999 );

/* ── Lesson page: relocate mark-as-complete into visible content area ── */
add_action( 'wp_footer', function () {
    if ( ! is_singular( 'lesson' ) ) return;
    ?>
<style id="pt101-lesson-complete-bar">
/* Completion bar injected by JS above the prev/next nav */
.pt101-complete-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  padding: 20px 40px;
  margin: 32px 0 0;
  background: #f8f7f4;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.06);
}
.pt101-complete-bar .pt101-progress-wrap {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  min-width: 0;
}
.pt101-complete-bar .pt101-progress-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151 !important;
  white-space: nowrap;
}
.pt101-complete-bar .pt101-progress-track {
  flex: 1;
  max-width: 200px;
  height: 6px;
  background: #e5e7eb;
  border-radius: 99px;
  overflow: hidden;
}
.pt101-complete-bar .pt101-progress-fill {
  height: 100%;
  background: #5046e5;
  border-radius: 99px;
  transition: width 0.3s;
}
.pt101-complete-bar .pt101-mark-btn {
  display: inline-flex !important;
  align-items: center;
  gap: 8px;
  background: #5046e5 !important;
  color: #fff !important;
  border: none !important;
  border-radius: 8px !important;
  padding: 12px 28px !important;
  font-size: 0.9rem !important;
  font-weight: 600 !important;
  cursor: pointer !important;
  white-space: nowrap;
  transition: background 0.15s !important;
  text-decoration: none !important;
}
.pt101-complete-bar .pt101-mark-btn:hover {
  background: #3730a3 !important;
}
.pt101-complete-bar .pt101-mark-btn svg {
  width: 18px;
  height: 18px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}
@media (max-width: 640px) {
  .pt101-complete-bar {
    flex-direction: column;
    align-items: stretch;
    padding: 16px 20px;
  }
}
</style>
<script>
(function(){
  if(!document.body.classList.contains('single-lesson')) return;

  /* Find Tutor's native mark-complete button/form */
  var selectors = [
    'form.tutor-lesson-mark-complete',
    '.tutor-lesson-mark-complete',
    '.tutor-btn-complete-lesson',
    'button[data-tutor-action*="complete"]',
    'a[data-tutor-action*="complete"]',
    '[class*="complete-lesson-btn"]',
    '[class*="mark-complete"]'
  ];

  function findNativeBtn(){
    for(var i=0;i<selectors.length;i++){
      var el = document.querySelector(selectors[i]);
      if(el) return el;
    }
    return null;
  }

  /* Find progress text */
  function findProgress(){
    var els = document.querySelectorAll('[class*="progress"], [class*="completing"]');
    for(var i=0;i<els.length;i++){
      var t = els[i].textContent||'';
      var m = t.match(/(\d+)%/);
      if(m) return parseInt(m[1],10);
    }
    return null;
  }

  /* Find the content area to insert into */
  function findContentArea(){
    var candidates = [
      '#tutor-course-player-content',
      '.tutor-course-player-content',
      '.tutor-lesson-content-wrap',
      '.tutor-course-spotlight-wrap',
      '.tutor-lesson-content',
      '.tutor-single-entry-content'
    ];
    for(var i=0;i<candidates.length;i++){
      var el = document.querySelector(candidates[i]);
      if(el) return el;
    }
    return null;
  }

  function init(){
    var content = findContentArea();
    if(!content) return;

    /* Don't double-insert */
    if(document.querySelector('.pt101-complete-bar')) return;

    var nativeBtn = findNativeBtn();
    var pct = findProgress();

    /* Build the completion bar */
    var bar = document.createElement('div');
    bar.className = 'pt101-complete-bar';

    var progressHTML = '';
    if(pct !== null){
      progressHTML =
        '<div class="pt101-progress-wrap">' +
          '<span class="pt101-progress-label">' + pct + '% Complete</span>' +
          '<div class="pt101-progress-track"><div class="pt101-progress-fill" style="width:'+pct+'%"></div></div>' +
        '</div>';
    }

    var btnHTML =
      '<button type="button" class="pt101-mark-btn">' +
        '<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>' +
        'Mark as Complete' +
      '</button>';

    bar.innerHTML = progressHTML + btnHTML;
    content.appendChild(bar);

    /* Wire up the clone to trigger native completion */
    var cloneBtn = bar.querySelector('.pt101-mark-btn');
    cloneBtn.addEventListener('click', function(e){
      e.preventDefault();
      var real = findNativeBtn();
      if(real){
        /* If it's a form, submit it */
        var form = real.closest ? real.closest('form') : null;
        if(!form && real.tagName === 'FORM') form = real;
        if(form && typeof form.submit === 'function'){
          form.requestSubmit ? form.requestSubmit() : form.submit();
          return;
        }
        real.click();
        return;
      }
      /* Fallback: try the standard Tutor AJAX approach */
      alert('Lesson completion not available. Please refresh the page and try again.');
    });
  }

  /* Run after Tutor loads */
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){ setTimeout(init, 500); });
  } else {
    setTimeout(init, 500);
  }
})();
</script>
    <?php
}, 99999 );
