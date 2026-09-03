<?php
/**
 * SoloToChina theme setup.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_THEME_VERSION', '0.26.0' );

require_once get_template_directory() . '/inc/component-registry.php';
require_once get_template_directory() . '/inc/content-contract.php';
require_once get_template_directory() . '/inc/content-components.php';
require_once get_template_directory() . '/inc/content-renderers.php';
require_once get_template_directory() . '/inc/commercial-events.php';

function stc_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'stc-guide-card-2x', 960, 0, false );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

	register_nav_menus(
		[
			'primary' => __( 'Primary Navigation', 'solo-to-china' ),
		]
	);
}
add_action( 'after_setup_theme', 'stc_theme_setup' );

/**
 * Add deterministic server-rendered IDs to content H2 elements.
 *
 * Explicit Gutenberg/CMS anchors are preserved. Missing or duplicate heading
 * IDs receive readable stable slugs, so navigation remains useful without JS.
 *
 * @param string $content Rendered post content.
 * @return string
 */
function stc_add_stable_content_heading_ids( $content ) {
	if ( ! is_singular() || ! in_the_loop() || ! is_main_query() || false === stripos( $content, '<h2' ) ) {
		return $content;
	}

	$used_ids = array();
	$index    = 0;

	return preg_replace_callback(
		'/<h2\b([^>]*)>(.*?)<\/h2>/is',
		function ( $matches ) use ( &$used_ids, &$index ) {
			$index++;
			$attributes  = $matches[1];
			$inner_html  = $matches[2];
			$explicit_id = '';

			if ( preg_match( '/\sid=(["\'])(.*?)\1/i', $attributes, $id_match ) ) {
				$explicit_id = sanitize_title( $id_match[2] );
			}

			$base_id = $explicit_id ? $explicit_id : sanitize_title( wp_strip_all_tags( $inner_html ) );
			$base_id = $base_id ? $base_id : 'section-' . $index;
			$heading_id = $base_id;
			$suffix     = 2;

			while ( isset( $used_ids[ $heading_id ] ) ) {
				$heading_id = $base_id . '-' . $suffix;
				$suffix++;
			}

			$used_ids[ $heading_id ] = true;

			if ( $explicit_id ) {
				$attributes = preg_replace( '/\sid=(["\']).*?\1/i', ' id="' . esc_attr( $heading_id ) . '"', $attributes, 1 );
			} else {
				$attributes = ' id="' . esc_attr( $heading_id ) . '"' . $attributes;
			}

			return '<h2' . $attributes . '>' . $inner_html . '</h2>';
		},
		$content
	);
}
add_filter( 'the_content', 'stc_add_stable_content_heading_ids', 12 );

function stc_enqueue_assets() {
	wp_enqueue_style(
		'stc-main',
		get_template_directory_uri() . '/assets/css/main.css',
		[],
		STC_THEME_VERSION
	);

	wp_enqueue_script(
		'stc-main',
		get_template_directory_uri() . '/assets/js/main.js',
		[],
		STC_THEME_VERSION,
		true
	);

	wp_enqueue_script(
		'stc-commercial-events',
		get_template_directory_uri() . '/assets/js/commercial-events.js',
		array(),
		STC_THEME_VERSION,
		true
	);
	wp_localize_script(
		'stc-commercial-events',
		'stcCommercialEvents',
		array( 'endpoint' => esc_url_raw( rest_url( 'stc/v1/commercial-events' ) ) )
	);
}
add_action( 'wp_enqueue_scripts', 'stc_enqueue_assets' );

function stc_primary_navigation_items() {
	return [
		[ 'label' => 'Home', 'url' => home_url( '/' ) ],
		[ 'label' => 'Survival Kit', 'url' => home_url( '/survival-kit/' ) ],
		[ 'label' => 'City Guides', 'url' => home_url( '/city-guides/' ) ],
		[ 'label' => 'Attraction Guides', 'url' => home_url( '/attraction-guides/' ) ],
		[ 'label' => 'Planner', 'url' => home_url( '/planner/' ) ],
		[ 'label' => 'Tools', 'url' => home_url( '/tools/' ) ],
		[ 'label' => 'FAQ', 'url' => home_url( '/faq/' ) ],
	];
}

function stc_render_primary_navigation() {
	echo '<nav id="stc-primary-nav" class="stc-nav" aria-label="' . esc_attr__( 'Primary navigation', 'solo-to-china' ) . '">';
	foreach ( stc_primary_navigation_items() as $item ) {
		echo '<a class="stc-nav__link" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
	}
	echo '</nav>';
}

function stc_core_pages() {
	return [
		'survival-kit'      => 'Survival Kit',
		'city-guides'       => 'City Guides',
		'attraction-guides' => 'Attraction Guides',
		'planner'           => 'Planner',
		'tools'             => 'Tools',
		'faq'               => 'FAQ',
	];
}

function stc_core_guide_categories() {
	return [
		'survival-kit'      => [
			'name'        => 'Survival Kit',
			'description' => 'Practical setup and troubleshooting guides for first-time China travel.',
		],
		'city-guides'       => [
			'name'        => 'City Guides',
			'description' => 'City strategy guides for where to stay, how to move, and what to do.',
		],
		'attraction-guides' => [
			'name'        => 'Attraction Guides',
			'description' => 'Scenic spot and attraction guides for timing, transport, tickets, and route planning.',
		],
		'travel-guides'     => [
			'name'        => 'Travel Guides',
			'description' => 'General travel guides using the default SoloToChina article shell.',
		],
	];
}

function stc_ensure_core_pages() {
	foreach ( stc_core_pages() as $slug => $title ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}

		wp_insert_post(
			[
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '<!-- SoloToChina core landing page. The theme renders this page from its slug. -->',
			]
		);
	}
}
add_action( 'after_switch_theme', 'stc_ensure_core_pages' );

function stc_ensure_core_categories() {
	foreach ( stc_core_guide_categories() as $slug => $category ) {
		if ( term_exists( $slug, 'category' ) ) {
			continue;
		}

		wp_insert_term(
			$category['name'],
			'category',
			[
				'description' => $category['description'],
				'slug'        => $slug,
			]
		);
	}
}
add_action( 'after_switch_theme', 'stc_ensure_core_categories' );

function stc_is_attraction_guide_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	$explicit_type = stc_get_explicit_guide_type( $post_id );

	return 'attraction-guide' === $explicit_type || ( ! $explicit_type && ( has_category( 'attraction-guides', $post_id ) || has_tag( 'attraction-guide', $post_id ) ) );
}

function stc_is_city_guide_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	$explicit_type = stc_get_explicit_guide_type( $post_id );

	return 'city-guide' === $explicit_type || ( ! $explicit_type && ( has_category( 'city-guides', $post_id ) || has_tag( 'city-guide', $post_id ) ) );
}

function stc_is_survival_kit_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	$explicit_type = stc_get_explicit_guide_type( $post_id );

	return 'survival-kit' === $explicit_type || ( ! $explicit_type && ( has_category( 'survival-kit', $post_id ) || has_tag( 'survival-kit', $post_id ) ) );
}

function stc_get_guide_type_slug( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$explicit_type = stc_get_explicit_guide_type( $post_id );

	if ( $explicit_type ) {
		return $explicit_type;
	}

	if ( stc_is_survival_kit_post( $post_id ) ) {
		return 'survival-kit';
	}

	if ( stc_is_attraction_guide_post( $post_id ) ) {
		return 'attraction-guide';
	}

	if ( stc_is_city_guide_post( $post_id ) ) {
		return 'city-guide';
	}

	return 'travel-guide';
}

function stc_get_guide_type_label( $post_id = null ) {
	$slug = stc_get_guide_type_slug( $post_id );

	$labels = [
		'survival-kit'     => __( 'Survival Kit', 'solo-to-china' ),
		'attraction-guide' => __( 'Attraction Guide', 'solo-to-china' ),
		'city-guide'       => __( 'City Guide', 'solo-to-china' ),
		'travel-guide'     => __( 'Travel Guide', 'solo-to-china' ),
	];

	return $labels[ $slug ] ?? $labels['travel-guide'];
}

function stc_render_guide_card_media( $image_file, $alt = '' ) {
	$image_url = get_template_directory_uri() . '/assets/images/' . ltrim( $image_file, '/' );

	echo '<span class="stc-image-card__media">';
	echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt ) . '" width="960" height="1200" loading="lazy" decoding="async">';
	echo '</span>';
}

function stc_render_faq_chevron() {
	echo '<svg class="stc-faq__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false">';
	echo '<path d="m7 9 5 5 5-5" />';
	echo '</svg>';
}

function stc_render_guide_card( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	$type_slug  = stc_get_guide_type_slug( $post_id );
	$type_label = stc_get_guide_type_label( $post_id );
	$classes    = get_post_class( [ 'stc-post-card', 'stc-post-card--' . $type_slug ], $post_id );
	$excerpt    = get_the_excerpt( $post_id );
	$date_attr  = get_the_date( DATE_W3C, $post_id );
	$date_label = get_the_date( '', $post_id );

	echo '<article class="' . esc_attr( implode( ' ', $classes ) ) . '">';
	echo '<a class="stc-post-card__link" href="' . esc_url( get_permalink( $post_id ) ) . '">';
	if ( has_post_thumbnail( $post_id ) ) {
		echo '<span class="stc-post-card__media">';
		echo wp_get_attachment_image(
			get_post_thumbnail_id( $post_id ),
			'stc-guide-card-2x',
			false,
			[
				'class'    => 'stc-post-card__image',
				'loading'  => 'lazy',
				'decoding' => 'async',
				'sizes'    => '(max-width: 720px) calc(100vw - 40px), (max-width: 1100px) calc(50vw - 48px), 360px',
			]
		);
		echo '</span>';
	}
	echo '<div class="stc-post-card__meta">';
	echo '<span class="stc-post-card__type">' . esc_html( $type_label ) . '</span>';
	echo '<time datetime="' . esc_attr( $date_attr ) . '">' . esc_html( $date_label ) . '</time>';
	echo '</div>';
	echo '<h2>' . esc_html( get_the_title( $post_id ) ) . '</h2>';
	if ( $excerpt ) {
		echo '<p>' . esc_html( wp_trim_words( $excerpt, 28 ) ) . '</p>';
	}
	echo '<span class="stc-post-card__cta">' . esc_html__( 'Read guide', 'solo-to-china' ) . '</span>';
	echo '</a>';
	echo '</article>';
}

function stc_render_guide_toc( $modifier_class = '' ) {
	$classes = trim( 'stc-guide-toc ' . sanitize_html_class( $modifier_class ) );

	echo '<nav class="' . esc_attr( $classes ) . '" aria-label="' . esc_attr__( 'On this page', 'solo-to-china' ) . '" data-stc-guide-toc>';
	echo '<h2>' . esc_html__( 'On this page', 'solo-to-china' ) . '</h2>';
	echo '<ol data-stc-guide-toc-list></ol>';
	echo '</nav>';
}

/**
 * Render the reusable Share This Page utility.
 *
 * The CMS decides whether it is present; the Theme only renders and enhances it.
 *
 * @param array<string, mixed> $args Optional post, title, and description values.
 */
function stc_render_share_this_page( $args = array() ) {
	$post_id     = isset( $args['post_id'] ) ? (int) $args['post_id'] : (int) get_the_ID();
	$title       = isset( $args['title'] ) ? sanitize_text_field( $args['title'] ) : get_the_title( $post_id );
	$description = isset( $args['description'] ) ? sanitize_text_field( $args['description'] ) : get_the_excerpt( $post_id );
	$canonical   = $post_id ? wp_get_canonical_url( $post_id ) : '';
	$canonical   = $canonical ? $canonical : ( $post_id ? get_permalink( $post_id ) : home_url( '/' ) );
	$panel_id    = 'stc-share-panel-' . ( $post_id ? $post_id : wp_unique_id() );
	$heading_id  = $panel_id . '-title';

	if ( ! $title || ! wp_http_validate_url( $canonical ) ) {
		return;
	}

	echo '<div class="stc-share" data-stc-share data-share-title="' . esc_attr( $title ) . '" data-share-description="' . esc_attr( wp_trim_words( $description, 28 ) ) . '" data-share-canonical="' . esc_url( $canonical ) . '">';
	echo '<button class="stc-share__trigger" type="button" aria-expanded="false" aria-controls="' . esc_attr( $panel_id ) . '" aria-label="' . esc_attr__( 'Share this page', 'solo-to-china' ) . '" data-stc-share-trigger>';
	echo '<span class="stc-share__trigger-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></span>';
	echo '<span class="stc-share__trigger-copy"><strong>' . esc_html__( 'Share this page', 'solo-to-china' ) . '</strong><small>' . esc_html__( 'Send it to a travel companion', 'solo-to-china' ) . '</small></span>';
	echo '<span class="stc-share__trigger-arrow" aria-hidden="true">&#8599;</span></button>';
	echo '<div id="' . esc_attr( $panel_id ) . '" class="stc-share__panel" role="dialog" aria-labelledby="' . esc_attr( $heading_id ) . '" data-stc-share-panel hidden>';
	echo '<div class="stc-share__panel-heading"><div><span>' . esc_html__( 'Share the useful part', 'solo-to-china' ) . '</span><strong id="' . esc_attr( $heading_id ) . '">' . esc_html__( 'Pass this guide along', 'solo-to-china' ) . '</strong></div>';
	echo '<button class="stc-share__close" type="button" aria-label="' . esc_attr__( 'Close sharing options', 'solo-to-china' ) . '" data-stc-share-close><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 7 10 10M17 7 7 17"/></svg></button></div>';
	echo '<p class="stc-share__panel-copy">' . esc_html__( 'Good plans travel further when they are easy to share.', 'solo-to-china' ) . '</p>';
	echo '<div class="stc-share__channels">';
	echo '<a class="stc-share__channel" href="#" data-stc-share-whatsapp><span aria-hidden="true">WA</span>' . esc_html__( 'WhatsApp', 'solo-to-china' ) . '</a>';
	echo '<a class="stc-share__channel" href="#" data-stc-share-email><span aria-hidden="true">@</span>' . esc_html__( 'Email', 'solo-to-china' ) . '</a>';
	echo '</div>';
	echo '<div class="stc-share__copy-row"><input type="text" value="' . esc_attr( $canonical ) . '" readonly aria-label="' . esc_attr__( 'Canonical page link', 'solo-to-china' ) . '" data-stc-share-url><button class="stc-share__copy" type="button" data-stc-share-copy>' . esc_html__( 'Copy link', 'solo-to-china' ) . '</button></div>';
	echo '<p class="stc-share__status" role="status" aria-live="polite" data-stc-share-status></p>';
	echo '</div></div>';
}

function stc_core_page_latest_guides_config( $slug ) {
	$config = [
		'survival-kit'      => [
			'category' => 'survival-kit',
			'label'    => __( 'Latest Survival Kit guides', 'solo-to-china' ),
			'empty'    => __( 'Published Survival Kit guides will appear here.', 'solo-to-china' ),
		],
		'city-guides'       => [
			'category' => 'city-guides',
			'label'    => __( 'Latest City Guides', 'solo-to-china' ),
			'empty'    => __( 'Published City Guide articles will appear here.', 'solo-to-china' ),
		],
		'attraction-guides' => [
			'category' => 'attraction-guides',
			'label'    => __( 'Latest Attraction Guides', 'solo-to-china' ),
			'empty'    => __( 'Published Attraction Guide articles will appear here.', 'solo-to-china' ),
		],
	];

	return $config[ $slug ] ?? null;
}

function stc_render_core_page_latest_guides( $slug ) {
	$config = stc_core_page_latest_guides_config( $slug );

	if ( ! $config ) {
		return;
	}

	$query = new WP_Query(
		[
			'category_name'       => $config['category'],
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'post_type'           => 'post',
			'posts_per_page'      => 6,
		]
	);

	$category     = get_category_by_slug( $config['category'] );
	$archive_link = $category ? get_category_link( $category ) : home_url( '/category/' . $config['category'] . '/' );

	echo '<section class="stc-page-section stc-latest-guides" aria-labelledby="stc-latest-guides-title">';
	echo '<div class="stc-latest-guides__header">';
	echo '<div>';
	echo '<p>' . esc_html__( 'Fresh practical guides', 'solo-to-china' ) . '</p>';
	echo '<h2 id="stc-latest-guides-title">' . esc_html( $config['label'] ) . '</h2>';
	echo '</div>';
	echo '<a href="' . esc_url( $archive_link ) . '">' . esc_html__( 'Browse all', 'solo-to-china' ) . '</a>';
	echo '</div>';

	if ( $query->have_posts() ) {
		echo '<div class="stc-post-list">';
		while ( $query->have_posts() ) {
			$query->the_post();
			stc_render_guide_card( get_the_ID() );
		}
		echo '</div>';
	} else {
		echo '<p class="stc-latest-guides__empty">' . esc_html( $config['empty'] ) . '</p>';
	}

	wp_reset_postdata();

	echo '</section>';
}

function stc_render_survival_icon( $icon ) {
	$paths = [
		'payment' => '<path d="M12 3v18"/><path d="M17 7.5c-.9-1-2.2-1.5-4-1.5-2.4 0-4 1.1-4 2.8 0 4.1 8 1.7 8 6 0 1.8-1.7 3.2-4.2 3.2-1.8 0-3.3-.6-4.3-1.7"/>',
		'apps'    => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>',
		'esim'    => '<path d="M5 8a7 7 0 0 1 14 0"/><path d="M8 11a4 4 0 0 1 8 0"/><path d="M12 15h.01"/><path d="M9 18h6"/>',
		'visa'    => '<rect x="6" y="3" width="12" height="18" rx="2"/><path d="M9 7h6"/><path d="M9 11h6"/><path d="M9 15h3"/>',
		'vpn'     => '<path d="M4 9a12 12 0 0 1 16 0"/><path d="M7 12a7.5 7.5 0 0 1 10 0"/><path d="M10 15a3 3 0 0 1 4 0"/><path d="M12 19h.01"/>',
	];

	$path = $paths[ $icon ] ?? $paths['apps'];

	echo '<span class="stc-survival-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false">' . $path . '</svg></span>';
}
