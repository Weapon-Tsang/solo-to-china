<?php
/**
 * SoloToChina Child Theme setup.
 *
 * @package SoloToChinaChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_CHILD_VERSION', '0.4.0' );

/**
 * Load Child Theme presentation after the Parent Theme stylesheet.
 *
 * The Parent Theme remains responsible for its own `stc-main` asset. The Child
 * Theme declares that handle as a dependency so WordPress preserves the correct
 * parent -> child -> design-system cascade without loading the parent twice.
 */
function stc_child_enqueue_assets() {
	wp_enqueue_style(
		'stc-child-style',
		get_stylesheet_uri(),
		[ 'stc-main' ],
		STC_CHILD_VERSION
	);

	wp_enqueue_style(
		'stc-child-design-system',
		get_stylesheet_directory_uri() . '/assets/css/design-system.css',
		[ 'stc-child-style' ],
		STC_CHILD_VERSION
	);

	wp_enqueue_style(
		'stc-child-site',
		get_stylesheet_directory_uri() . '/assets/css/site.css',
		[ 'stc-child-design-system' ],
		STC_CHILD_VERSION
	);

	if ( is_front_page() ) {
		wp_enqueue_style(
			'stc-child-home',
			get_stylesheet_directory_uri() . '/assets/css/home.css',
			[ 'stc-child-site' ],
			STC_CHILD_VERSION
		);
	}

	if ( is_single() ) {
		wp_enqueue_style(
			'stc-child-article',
			get_stylesheet_directory_uri() . '/assets/css/article.css',
			[ 'stc-child-site' ],
			STC_CHILD_VERSION
		);

		wp_enqueue_style(
			'stc-child-content-components',
			get_stylesheet_directory_uri() . '/assets/css/content-components.css',
			[ 'stc-child-article' ],
			STC_CHILD_VERSION
		);
	}

	wp_enqueue_script(
		'stc-child-interactions',
		get_stylesheet_directory_uri() . '/assets/js/site.js',
		[ 'stc-main' ],
		STC_CHILD_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'stc_child_enqueue_assets', 20 );

/**
 * Add a compact, semantic breadcrumb to guide articles without duplicating the
 * Parent Theme single template. Schema output remains available to SEO plugins.
 *
 * @param string $content Post content.
 * @return string
 */
function stc_child_prepend_guide_breadcrumbs( $content ) {
	if ( ! is_single() || ! in_the_loop() || ! is_main_query() || ! function_exists( 'stc_get_guide_type_slug' ) ) {
		return $content;
	}

	$guide_type = stc_get_guide_type_slug();
	$hubs       = [
		'attraction-guide' => [
			'label' => __( 'Attraction Guides', 'solo-to-china-child' ),
			'path'  => '/attraction-guides/',
		],
		'city-guide'       => [
			'label' => __( 'City Guides', 'solo-to-china-child' ),
			'path'  => '/city-guides/',
		],
		'survival-kit'     => [
			'label' => __( 'Survival Kit', 'solo-to-china-child' ),
			'path'  => '/survival-kit/',
		],
	];

	if ( ! isset( $hubs[ $guide_type ] ) || false !== strpos( $content, 'stc-guide-breadcrumb' ) ) {
		return $content;
	}

	$hub        = $hubs[ $guide_type ];
	$breadcrumb = '<nav class="stc-guide-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'solo-to-china-child' ) . '">';
	$breadcrumb .= '<ol><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'solo-to-china-child' ) . '</a></li>';
	$breadcrumb .= '<li><a href="' . esc_url( home_url( $hub['path'] ) ) . '">' . esc_html( $hub['label'] ) . '</a></li>';
	$breadcrumb .= '<li><span aria-current="page">' . esc_html( get_the_title() ) . '</span></li></ol></nav>';

	return $breadcrumb . $content;
}
add_filter( 'the_content', 'stc_child_prepend_guide_breadcrumbs', 8 );

/**
 * Render the fixed primary navigation with an accessible current-page state.
 */
function stc_child_render_primary_navigation() {
	$current_guide_type = is_single() && function_exists( 'stc_get_guide_type_slug' ) ? stc_get_guide_type_slug() : '';
	$guide_nav_map      = [
		'survival-kit'      => 'survival-kit',
		'city-guide'        => 'city-guides',
		'attraction-guide' => 'attraction-guides',
	];

	echo '<nav id="stc-primary-nav" class="stc-nav" aria-label="' . esc_attr__( 'Primary navigation', 'solo-to-china-child' ) . '">';

	foreach ( stc_primary_navigation_items() as $item ) {
		$item_path  = trim( (string) wp_parse_url( $item['url'], PHP_URL_PATH ), '/' );
		$is_current = ( '' === $item_path && is_front_page() )
			|| ( '' !== $item_path && ( is_page( $item_path ) || is_category( $item_path ) ) )
			|| ( isset( $guide_nav_map[ $current_guide_type ] ) && $item_path === $guide_nav_map[ $current_guide_type ] );
		$current     = $is_current ? ' aria-current="page"' : '';

		echo '<a class="stc-nav__link" href="' . esc_url( $item['url'] ) . '"' . $current . '>' . esc_html( $item['label'] ) . '</a>';
	}

	echo '</nav>';
}
