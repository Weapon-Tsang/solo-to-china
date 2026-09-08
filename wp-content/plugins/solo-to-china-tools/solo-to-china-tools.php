<?php
/**
 * Plugin Name: SoloToChina Tools
 * Description: Privacy-first place finding, taxi cards, and ticket planning for SoloToChina.
 * Version: 0.25.0
 * Author: SoloToChina
 * Text Domain: solo-to-china-tools
 * Requires at least: 6.5
 * Requires PHP: 7.4
 *
 * @package SoloToChinaTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_TOOLS_VERSION', '0.25.0' );
define( 'STC_TOOLS_PATH', plugin_dir_path( __FILE__ ) );
define( 'STC_TOOLS_URL', plugin_dir_url( __FILE__ ) );
define( 'STC_TOOLS_MAX_IMAGE_BYTES', 20 * 1024 * 1024 );
define( 'STC_TOOLS_MAX_IMAGE_COUNT', 4 );
define( 'STC_TOOLS_MAX_UPLOAD_BYTES', 60 * 1024 * 1024 );

require_once STC_TOOLS_PATH . 'includes/attractions.php';
require_once STC_TOOLS_PATH . 'includes/places.php';
require_once STC_TOOLS_PATH . 'includes/providers.php';
require_once STC_TOOLS_PATH . 'includes/shortcodes.php';
require_once STC_TOOLS_PATH . 'includes/rest-api.php';

function stc_tools_should_enqueue_assets() {
	if ( is_front_page() || is_page( array( 'tools', 'find-this-place', 'taxi-card' ) ) ) {
		return true;
	}

	if ( ! is_singular() ) {
		return false;
	}

	$post = get_post();

	return $post && (
		has_shortcode( $post->post_content, 'solo_to_china_ticket_tool' ) ||
		has_shortcode( $post->post_content, 'solo_to_china_place_finder' ) ||
		has_shortcode( $post->post_content, 'solo_to_china_taxi_card' ) ||
		has_shortcode( $post->post_content, 'solo_to_china_tools_directory' ) ||
		has_shortcode( $post->post_content, 'stc_destination_card' ) ||
		has_shortcode( $post->post_content, 'stc_ticket_reminder' )
	);
}

function stc_tools_enqueue_assets() {
	if ( ! stc_tools_should_enqueue_assets() ) {
		return;
	}

	wp_enqueue_style(
		'stc-tools',
		STC_TOOLS_URL . 'assets/css/tools.css',
		[],
		STC_TOOLS_VERSION
	);

	wp_enqueue_script(
		'stc-tools',
		STC_TOOLS_URL . 'assets/js/tools.js',
		[],
		STC_TOOLS_VERSION,
		true
	);
	wp_localize_script( 'stc-tools', 'stcToolsConfig', array(
		'placeEndpoint' => esc_url_raw( rest_url( 'stc/v1/place-finder' ) ),
		'taxiEndpoint'  => esc_url_raw( rest_url( 'stc/v1/taxi-card' ) ),
		'taxiUrl'       => esc_url_raw( home_url( '/tools/taxi-card/' ) ),
		'maxImageBytes' => STC_TOOLS_MAX_IMAGE_BYTES,
		'maxImageCount' => STC_TOOLS_MAX_IMAGE_COUNT,
		'maxUploadBytes' => STC_TOOLS_MAX_UPLOAD_BYTES,
	) );
}
add_action( 'wp_enqueue_scripts', 'stc_tools_enqueue_assets' );

function stc_tools_register_shortcodes() {
	add_shortcode( 'solo_to_china_ticket_tool', 'stc_tools_render_ticket_tool' );
	add_shortcode( 'solo_to_china_place_finder', 'stc_tools_render_place_finder' );
	add_shortcode( 'solo_to_china_taxi_card', 'stc_tools_render_taxi_card' );
	add_shortcode( 'solo_to_china_tools_directory', 'stc_tools_render_directory' );
}
add_action( 'init', 'stc_tools_register_shortcodes' );

function stc_tools_ensure_pages() {
	$tools = get_page_by_path( 'tools' );
	if ( ! $tools ) {
		$tools_id = wp_insert_post( array( 'post_title' => 'Tools', 'post_name' => 'tools', 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => '<!-- SoloToChina Tools directory. -->' ) );
		$tools = $tools_id && ! is_wp_error( $tools_id ) ? get_post( $tools_id ) : null;
	}
	if ( ! $tools ) { return false; }
	$pages = array(
		'find-this-place' => array( 'title' => 'Find This Place', 'content' => '[solo_to_china_place_finder]' ),
		'taxi-card' => array( 'title' => 'Show This to a Driver', 'content' => '[solo_to_china_taxi_card]' ),
	);
	foreach ( $pages as $slug => $page ) {
		if ( get_page_by_path( 'tools/' . $slug ) ) { continue; }
		$result = wp_insert_post( array( 'post_title' => $page['title'], 'post_name' => $slug, 'post_parent' => (int) $tools->ID, 'post_type' => 'page', 'post_status' => 'publish', 'post_content' => $page['content'] ) );
		if ( ! $result || is_wp_error( $result ) ) { return false; }
	}
	return true;
}

function stc_tools_activate() {
	stc_tools_ensure_pages();
	update_option( 'stc_tools_page_version', STC_TOOLS_VERSION );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'stc_tools_activate' );

function stc_tools_maybe_ensure_pages() {
	if ( STC_TOOLS_VERSION !== get_option( 'stc_tools_page_version' ) && stc_tools_ensure_pages() ) {
		update_option( 'stc_tools_page_version', STC_TOOLS_VERSION );
	}
}
add_action( 'admin_init', 'stc_tools_maybe_ensure_pages' );
