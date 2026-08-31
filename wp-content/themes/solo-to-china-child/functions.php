<?php
/**
 * SoloToChina Child Theme setup.
 *
 * @package SoloToChinaChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_CHILD_VERSION', '0.2.0' );

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
 * Render the fixed primary navigation with an accessible current-page state.
 */
function stc_child_render_primary_navigation() {
	$current_guide_type = is_single() && function_exists( 'stc_get_guide_type_slug' ) ? stc_get_guide_type_slug() : '';
	$guide_nav_map      = [
		'survival-kit'      => 'survival-kit',
		'city-guide'        => 'city-guides',
		'attraction-guide'  => 'attraction-guides',
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
