<?php
/**
 * SoloToChina Child Theme setup.
 *
 * @package SoloToChinaChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_CHILD_VERSION', '0.1.0' );

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
}
add_action( 'wp_enqueue_scripts', 'stc_child_enqueue_assets', 20 );
