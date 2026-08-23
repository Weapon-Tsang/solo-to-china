<?php
/**
 * Plugin Name: SoloToChina Tools
 * Description: Project-owned tools for SoloToChina, starting with the Attraction Ticket Reservation & Reminder.
 * Version: 0.4.0
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

define( 'STC_TOOLS_VERSION', '0.4.0' );
define( 'STC_TOOLS_PATH', plugin_dir_path( __FILE__ ) );
define( 'STC_TOOLS_URL', plugin_dir_url( __FILE__ ) );

require_once STC_TOOLS_PATH . 'includes/attractions.php';
require_once STC_TOOLS_PATH . 'includes/shortcodes.php';

function stc_tools_should_enqueue_assets() {
	if ( is_front_page() || is_page( 'tools' ) ) {
		return true;
	}

	if ( ! is_singular() ) {
		return false;
	}

	$post = get_post();

	return $post && has_shortcode( $post->post_content, 'solo_to_china_ticket_tool' );
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
}
add_action( 'wp_enqueue_scripts', 'stc_tools_enqueue_assets' );

function stc_tools_register_shortcodes() {
	add_shortcode( 'solo_to_china_ticket_tool', 'stc_tools_render_ticket_tool' );
}
add_action( 'init', 'stc_tools_register_shortcodes' );
