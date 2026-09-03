<?php
/**
 * Privacy-minimal same-origin relay for public commercial impression/click events.
 *
 * Server credentials are read only from environment variables and are never
 * included in payloads, responses, database options, or application logs.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the public event payload allowlist.
 *
 * @return string[]
 */
function stc_commercial_event_allowed_fields() {
	return array( 'event_type', 'article_id', 'draft_id', 'affiliate_asset_id', 'provider', 'category', 'slot_key', 'component_variant', 'placement', 'entity', 'route', 'destination', 'timestamp', 'device', 'locale', 'strategy_version' );
}

/**
 * Enforce a same-origin browser boundary.
 *
 * @param WP_REST_Request $request REST request.
 * @return bool
 */
function stc_commercial_event_is_same_origin( $request ) {
	$origin = trim( (string) $request->get_header( 'origin' ) );
	if ( '' === $origin ) {
		return false;
	}

	$origin_parts = wp_parse_url( $origin );
	$home_parts   = wp_parse_url( home_url( '/' ) );
	if ( ! is_array( $origin_parts ) || ! is_array( $home_parts ) || empty( $origin_parts['scheme'] ) || empty( $origin_parts['host'] ) || empty( $home_parts['scheme'] ) || empty( $home_parts['host'] ) ) {
		return false;
	}

	$origin_port = isset( $origin_parts['port'] ) ? (int) $origin_parts['port'] : ( 'https' === $origin_parts['scheme'] ? 443 : 80 );
	$home_port   = isset( $home_parts['port'] ) ? (int) $home_parts['port'] : ( 'https' === $home_parts['scheme'] ? 443 : 80 );

	return strtolower( $origin_parts['scheme'] ) === strtolower( $home_parts['scheme'] )
		&& strtolower( $origin_parts['host'] ) === strtolower( $home_parts['host'] )
		&& $origin_port === $home_port;
}

/**
 * Build a one-way request identity for rate limiting and deduplication.
 *
 * @return string
 */
function stc_commercial_event_request_identity() {
	$address = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
	return hash_hmac( 'sha256', $address, wp_salt( 'nonce' ) );
}

/**
 * Validate and normalize a public commercial event payload.
 *
 * @param mixed $payload Decoded request payload.
 * @return array<string, string>|WP_Error
 */
function stc_validate_commercial_event_payload( $payload ) {
	if ( ! is_array( $payload ) ) {
		return new WP_Error( 'stc_commercial_event_invalid', __( 'Invalid event payload.', 'solo-to-china' ), array( 'status' => 400 ) );
	}

	$allowed = stc_commercial_event_allowed_fields();
	if ( array_diff( array_keys( $payload ), $allowed ) ) {
		return new WP_Error( 'stc_commercial_event_unknown_field', __( 'Unknown event field.', 'solo-to-china' ), array( 'status' => 400 ) );
	}

	$required = array( 'event_type', 'affiliate_asset_id', 'provider', 'category', 'slot_key', 'component_variant', 'placement', 'timestamp', 'device', 'locale', 'strategy_version' );
	$clean    = array();
	foreach ( $allowed as $field ) {
		if ( ! isset( $payload[ $field ] ) || '' === (string) $payload[ $field ] ) {
			continue;
		}
		if ( ! is_scalar( $payload[ $field ] ) || wp_strip_all_tags( (string) $payload[ $field ] ) !== (string) $payload[ $field ] ) {
			return new WP_Error( 'stc_commercial_event_invalid_field', __( 'Invalid event field.', 'solo-to-china' ), array( 'status' => 400 ) );
		}
		$clean[ $field ] = sanitize_text_field( (string) $payload[ $field ] );
		if ( strlen( $clean[ $field ] ) > 200 ) {
			return new WP_Error( 'stc_commercial_event_field_too_large', __( 'Event field is too large.', 'solo-to-china' ), array( 'status' => 413 ) );
		}
	}

	foreach ( $required as $field ) {
		if ( empty( $clean[ $field ] ) ) {
			return new WP_Error( 'stc_commercial_event_missing_field', __( 'Required event field is missing.', 'solo-to-china' ), array( 'status' => 400 ) );
		}
	}

	if ( ! in_array( $clean['event_type'], array( 'impression', 'click' ), true )
		|| ! in_array( $clean['category'], array( 'HOTEL', 'FLIGHT', 'TRAIN', 'ATTRACTION', 'TOUR_ACTIVITY', 'FLIGHT_HOTEL', 'CAR_RENTAL', 'AIRPORT_TRANSFER', 'PLANNER' ), true )
		|| ! in_array( $clean['placement'], array( 'contextual', 'end_resource' ), true )
		|| ! in_array( $clean['device'], array( 'mobile', 'tablet', 'desktop' ), true )
		|| false === strtotime( $clean['timestamp'] ) ) {
		return new WP_Error( 'stc_commercial_event_invalid_enum', __( 'Event value is not allowed.', 'solo-to-china' ), array( 'status' => 400 ) );
	}

	return $clean;
}

/**
 * Receive a validated event and relay it to the configured CMS Engine endpoint.
 *
 * Failure is intentionally non-blocking for visitor navigation.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_receive_commercial_event( $request ) {
	if ( ! stc_commercial_event_is_same_origin( $request ) ) {
		return new WP_Error( 'stc_commercial_event_origin', __( 'Event origin is not allowed.', 'solo-to-china' ), array( 'status' => 403 ) );
	}

	$raw_body = (string) $request->get_body();
	if ( strlen( $raw_body ) > 4096 ) {
		return new WP_Error( 'stc_commercial_event_payload_too_large', __( 'Event payload is too large.', 'solo-to-china' ), array( 'status' => 413 ) );
	}

	$payload = stc_validate_commercial_event_payload( $request->get_json_params() );
	if ( is_wp_error( $payload ) ) {
		return $payload;
	}

	$identity = stc_commercial_event_request_identity();
	$rate_key = 'stc_commercial_rate_' . substr( $identity, 0, 32 );
	$rate     = (int) get_transient( $rate_key );
	if ( $rate >= 120 ) {
		return new WP_Error( 'stc_commercial_event_rate_limit', __( 'Event rate limit exceeded.', 'solo-to-china' ), array( 'status' => 429 ) );
	}
	set_transient( $rate_key, $rate + 1, HOUR_IN_SECONDS );

	$dedup_source = $identity . '|' . $payload['event_type'] . '|' . $payload['affiliate_asset_id'] . '|' . $payload['slot_key'] . '|' . ( isset( $payload['article_id'] ) ? $payload['article_id'] : '' );
	$dedup_key    = 'stc_commercial_dedup_' . substr( hash( 'sha256', $dedup_source ), 0, 32 );
	if ( get_transient( $dedup_key ) ) {
		return new WP_REST_Response( array( 'accepted' => true, 'forwarded' => false, 'duplicate' => true ), 202 );
	}
	set_transient( $dedup_key, 1, 10 * MINUTE_IN_SECONDS );

	$endpoint = trim( (string) getenv( 'STC_COMMERCIAL_EVENTS_ENDPOINT' ) );
	$token    = trim( (string) getenv( 'STC_COMMERCIAL_EVENTS_TOKEN' ) );
	if ( '' === $endpoint || '' === $token || '' === stc_validate_https_component_url( $endpoint ) ) {
		return new WP_REST_Response( array( 'accepted' => true, 'forwarded' => false, 'reason' => 'relay_not_configured' ), 202 );
	}

	$result = wp_remote_post(
		$endpoint,
		array(
			'timeout'     => 2,
			'redirection' => 0,
			'sslverify'   => true,
			'headers'     => array(
				'Authorization' => 'Bearer ' . $token,
				'Content-Type'  => 'application/json',
			),
			'body'        => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $result ) || wp_remote_retrieve_response_code( $result ) < 200 || wp_remote_retrieve_response_code( $result ) >= 300 ) {
		return new WP_REST_Response( array( 'accepted' => true, 'forwarded' => false, 'reason' => 'relay_unavailable' ), 202 );
	}

	return new WP_REST_Response( array( 'accepted' => true, 'forwarded' => true ), 202 );
}

/**
 * Register the same-origin public event relay.
 */
function stc_register_commercial_event_route() {
	register_rest_route(
		'stc/v1',
		'/commercial-events',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'stc_rest_receive_commercial_event',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'stc_register_commercial_event_route' );
