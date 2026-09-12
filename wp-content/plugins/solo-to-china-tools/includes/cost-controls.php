<?php
/** Atomic short leases and fixed-window budgets; no visitor payload is persisted. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function stc_tools_acquire_lease( $key, $seconds ) {
	global $wpdb;
	$name = 'stc_lease_' . sanitize_key( $key );
	$now = time();
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s AND CAST(option_value AS UNSIGNED) < %d", $name, $now ) );
	wp_cache_delete( $name, 'options' );
	$value = ( $now + $seconds ) . ':' . wp_generate_uuid4();
	return add_option( $name, $value, '', false ) ? array( 'name' => $name, 'value' => $value ) : false;
}

function stc_tools_release_lease( $lease ) {
	if ( ! is_array( $lease ) ) { return; }
	global $wpdb;
	// A late worker must never release a newer owner's lease after expiry.
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", $lease['name'], $lease['value'] ) );
	wp_cache_delete( $lease['name'], 'options' );
}

function stc_tools_guarded_vision( $provider, $images, $context ) {
	$lease = false;
	$maximum = max( 1, min( 8, (int) ( stc_tools_config( 'STC_VISION_CONCURRENCY', 'STC_VISION_CONCURRENCY' ) ?: 2 ) ) );
	for ( $i = 0; $i < $maximum; $i++ ) { $lease = stc_tools_acquire_lease( 'vision_slot_' . $i, 50 ); if ( $lease ) { break; } }
	if ( ! $lease ) { return new WP_Error( 'provider_busy', 'Recognition is busy.', array( 'status' => 429 ) ); }
	try {
		// The shared daily budget uses the same atomic rate limiter, without storing raw IPs.
		$limit = max( 1, (int) ( stc_tools_config( 'STC_VISION_DAILY_LIMIT', 'STC_VISION_DAILY_LIMIT' ) ?: 100 ) );
		$budget = stc_tools_rate_limit( 'vision_daily_' . gmdate( 'Ymd' ), $limit, DAY_IN_SECONDS, 'global' );
		if ( is_wp_error( $budget ) ) { return $budget; }
		return $provider->identify( $images, $context );
	} catch ( Throwable $error ) { return new WP_Error( 'provider_unavailable', 'Recognition failed.' ); }
	finally { stc_tools_release_lease( $lease ); }
}
