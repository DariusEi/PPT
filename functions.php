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
        'Trading foundations'                        => '#programs',
        'Market mechanics & analysis'                => '#programs',
        'Strategy development & advanced technicals' => '#programs',
        'Mastering professional trading'             => '#programs',
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
    if ( get_option( 'pt101_course_pages_v5' ) ) return;
    pt101_ensure_course_pages();
    update_option( 'pt101_course_pages_v5', '1' );
}, 20 );
