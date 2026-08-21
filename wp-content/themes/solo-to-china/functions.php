<?php
/**
 * SoloToChina theme setup.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_THEME_VERSION', '0.2.0' );

function stc_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

	register_nav_menus(
		[
			'primary' => __( 'Primary Navigation', 'solo-to-china' ),
		]
	);
}
add_action( 'after_setup_theme', 'stc_theme_setup' );

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
