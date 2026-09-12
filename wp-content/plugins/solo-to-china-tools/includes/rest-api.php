<?php
/** Anonymous, privacy-first REST endpoints for SoloToChina tools. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function stc_tools_client_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
	return hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
}

function stc_tools_rate_limit( $bucket, $limit, $window, $identity = '' ) {
	$key = 'stc_tool_' . sanitize_key( $bucket ) . '_' . substr( $identity ?: stc_tools_client_key(), 0, 32 );
	$lease = stc_tools_acquire_lease( $key, 3 );
	if ( ! $lease ) { return new WP_Error( 'stc_rate_limited', 'Another request is being processed. Try again shortly.', array( 'status' => 429 ) ); }
	try {
	$state = get_transient( $key );
	$state = is_array( $state ) ? $state : array( 'count' => 0, 'expires' => time() + $window );
	if ( empty( $state['expires'] ) || $state['expires'] <= time() ) { $state = array( 'count' => 0, 'expires' => time() + $window ); }
	if ( (int) $state['count'] >= $limit ) { return new WP_Error( 'stc_rate_limited', __( 'Too many requests. Wait a few minutes and try again.', 'solo-to-china-tools' ), array( 'status' => 429 ) ); }
	$state['count'] = (int) $state['count'] + 1;
	set_transient( $key, $state, max( 1, $state['expires'] - time() ) );
	return true;
	} finally { stc_tools_release_lease( $lease ); }
}

function stc_tools_private_response( $data, $status = 200 ) {
	$response = new WP_REST_Response( $data, $status );
	$response->header( 'Cache-Control', 'private, no-store, max-age=0' );
	$response->header( 'X-Content-Type-Options', 'nosniff' );
	return $response;
}

function stc_tools_collect_image_uploads( $files ) {
	$field = isset( $files['images'] ) ? $files['images'] : ( isset( $files['image'] ) ? $files['image'] : null );
	if ( ! is_array( $field ) || ! isset( $field['name'], $field['tmp_name'], $field['size'], $field['error'] ) ) {
		return new WP_Error( 'invalid_upload', __( 'Choose between 1 and 4 photos of the same place.', 'solo-to-china-tools' ), array( 'status' => 400 ) );
	}

	if ( ! is_array( $field['name'] ) ) {
		return array( $field );
	}

	$uploads = array();
	$count   = count( $field['name'] );
	for ( $index = 0; $index < $count; $index++ ) {
		$uploads[] = array(
			'name'     => isset( $field['name'][ $index ] ) ? $field['name'][ $index ] : '',
			'tmp_name' => isset( $field['tmp_name'][ $index ] ) ? $field['tmp_name'][ $index ] : '',
			'size'     => isset( $field['size'][ $index ] ) ? $field['size'][ $index ] : 0,
			'error'    => isset( $field['error'][ $index ] ) ? $field['error'][ $index ] : UPLOAD_ERR_NO_FILE,
			'type'     => isset( $field['type'][ $index ] ) ? $field['type'][ $index ] : '',
		);
	}
	return $uploads;
}

function stc_tools_cleanup_uploads( $uploads ) {
	foreach ( (array) $uploads as $upload ) {
		$tmp = isset( $upload['tmp_name'] ) ? (string) $upload['tmp_name'] : '';
		if ( $tmp && is_uploaded_file( $tmp ) ) { @unlink( $tmp ); }
	}
}

function stc_tools_validate_image_upload( $file, $inspect_only = false ) {
	if ( ! is_array( $file ) || ! isset( $file['tmp_name'], $file['name'], $file['size'], $file['error'] ) ) {
		return new WP_Error( 'invalid_upload', __( 'The photo could not be uploaded. Try choosing it again.', 'solo-to-china-tools' ), array( 'status' => 400 ) );
	}
	$tmp = (string) $file['tmp_name'];
	if ( in_array( (int) $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) ) {
		if ( is_uploaded_file( $tmp ) ) { @unlink( $tmp ); }
		return new WP_Error( 'server_upload_limit', __( 'This photo is too large for the service. Try a smaller screenshot.', 'solo-to-china-tools' ), array( 'status' => 413 ) );
	}
	if ( UPLOAD_ERR_OK !== (int) $file['error'] ) {
		if ( is_uploaded_file( $tmp ) ) { @unlink( $tmp ); }
		return new WP_Error( 'invalid_upload', __( 'The photo could not be uploaded. Try choosing it again.', 'solo-to-china-tools' ), array( 'status' => 400 ) );
	}
	if ( (int) $file['size'] < 1 || (int) $file['size'] > STC_TOOLS_MAX_IMAGE_BYTES ) {
		if ( is_uploaded_file( $tmp ) ) { @unlink( $tmp ); }
		return new WP_Error( 'file_too_large', __( 'Each photo must be 20 MB or smaller.', 'solo-to-china-tools' ), array( 'status' => 413 ) );
	}
	if ( ! is_uploaded_file( $tmp ) || ! is_readable( $tmp ) ) {
		return new WP_Error( 'unsafe_image', __( 'This image could not be read safely. Try another file.', 'solo-to-china-tools' ), array( 'status' => 400 ) );
	}
	$image = @getimagesize( $tmp );
	$mime  = function_exists( 'wp_get_image_mime' ) ? wp_get_image_mime( $tmp ) : ( isset( $image['mime'] ) ? $image['mime'] : '' );
	if ( ! is_array( $image ) || ! in_array( $mime, array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
		@unlink( $tmp );
		return new WP_Error( 'unsupported_image', __( 'This image format is not supported yet. Try a screenshot or JPG, PNG, or WebP image.', 'solo-to-china-tools' ), array( 'status' => 415 ) );
	}
	$width = (int) $image[0]; $height = (int) $image[1];
	if ( $width < 1 || $height < 1 || $width > 12000 || $height > 12000 || $width * $height > 40000000 ) {
		@unlink( $tmp );
		return new WP_Error( 'unsafe_dimensions', __( 'This image is too large to process safely. Try a smaller screenshot.', 'solo-to-china-tools' ), array( 'status' => 413 ) );
	}
	if ( $inspect_only ) { return array( 'mime' => $mime, 'width' => $width, 'height' => $height ); }
	// Re-encode validated content to remove EXIF/GPS and reject undecodable images.
    $editor = wp_get_image_editor( $tmp );
    if ( is_wp_error( $editor ) ) { @unlink( $tmp ); return new WP_Error( 'image_processing_unavailable', 'This image cannot be prepared safely. Try a smaller screenshot.', array( 'status' => 415 ) ); }
    $editor->maybe_exif_rotate();
    $editor->resize( 4096, 4096, false );
    $editor->set_quality( 90 );
    $clean = $editor->save( $tmp . '-clean.jpg', 'image/jpeg' );
    if ( is_wp_error( $clean ) || empty( $clean['path'] ) ) { @unlink( $tmp ); return new WP_Error( 'corrupt_image', 'This image could not be prepared. Try another photo.', array( 'status' => 415 ) ); }
    $bytes = file_get_contents( $clean['path'] );
    @unlink( $clean['path'] );
    $mime = 'image/jpeg'; $width = $clean['width']; $height = $clean['height'];
	@unlink( $tmp );
	if ( false === $bytes || '' === $bytes ) {
		return new WP_Error( 'corrupt_image', __( 'This image appears corrupted. Try another file.', 'solo-to-china-tools' ), array( 'status' => 400 ) );
	}
	return array( 'bytes' => $bytes, 'mime' => $mime, 'width' => $width, 'height' => $height );
}

function stc_tools_prepare_vision_result( $raw ) {
	$status = isset( $raw['status'] ) ? sanitize_key( $raw['status'] ) : 'unknown';
	$confidence = isset( $raw['confidence'] ) && in_array( $raw['confidence'], array( 'high', 'medium', 'low' ), true ) ? $raw['confidence'] : 'low';
	$match_level = isset( $raw['match_level'] ) ? sanitize_key( $raw['match_level'] ) : 'unknown';
	$primary = stc_tools_normalize_provider_candidate( isset( $raw['primary_candidate'] ) ? $raw['primary_candidate'] : array(), $confidence, $match_level );
	$alternatives = array();
	foreach ( array_slice( isset( $raw['alternative_candidates'] ) ? (array) $raw['alternative_candidates'] : array(), 0, 4 ) as $candidate ) {
		$normalized = stc_tools_normalize_provider_candidate( $candidate, 'medium', isset( $candidate['match_level'] ) ? sanitize_key( $candidate['match_level'] ) : 'unknown' );
		if ( $normalized && ( $normalized['name_en'] || $normalized['name_zh'] ) ) { $alternatives[] = $normalized; }
	}
	if ( 'medium' === $confidence && $primary ) { array_unshift( $alternatives, $primary ); }
	if ( ! $primary || ( ! $primary['name_en'] && ! $primary['name_zh'] ) ) { $status = 'unknown'; $confidence = 'low'; $primary = null; }
	if ( 'low' === $confidence ) { $primary = null; }
	$unique = array(); foreach ( $alternatives as $candidate ) { $key = $candidate['entity_key'] ?: strtolower( $candidate['name_en'] . ':' . $candidate['city_en'] . ':' . $candidate['name_zh'] ); $unique[$key] = $candidate; }
	$alternatives = array_slice( array_values( $unique ), 0, 4 );
	return array( 'status' => $status, 'confidence' => $confidence, 'match_level' => $match_level, 'primary_candidate' => 'high' === $confidence ? $primary : null, 'alternative_candidates' => $alternatives );
}

function stc_tools_rest_place_finder( WP_REST_Request $request ) {
	$limited = stc_tools_rate_limit( 'vision', 8, 10 * MINUTE_IN_SECONDS );
	if ( is_wp_error( $limited ) ) { return $limited; }
	$content_length = isset( $_SERVER['CONTENT_LENGTH'] ) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
	if ( $content_length > STC_TOOLS_MAX_UPLOAD_BYTES + ( 1024 * 1024 ) ) {
		return new WP_Error( 'upload_too_large', __( 'The selected photos must total 60 MB or less.', 'solo-to-china-tools' ), array( 'status' => 413 ) );
	}
	$files   = $request->get_file_params();
	if ( empty( $files ) && $content_length > 0 ) {
		return new WP_Error( 'server_upload_limit', __( 'These photos are too large for the service. Try fewer photos or smaller screenshots.', 'solo-to-china-tools' ), array( 'status' => 413 ) );
	}
	$uploads = stc_tools_collect_image_uploads( $files );
	if ( is_wp_error( $uploads ) ) { return $uploads; }
	if ( count( $uploads ) < 1 || count( $uploads ) > STC_TOOLS_MAX_IMAGE_COUNT ) {
		stc_tools_cleanup_uploads( $uploads );
		return new WP_Error( 'invalid_image_count', __( 'Choose between 1 and 4 photos of the same place.', 'solo-to-china-tools' ), array( 'status' => 400 ) );
	}
	$total_bytes = array_sum( array_map( static function ( $upload ) { return isset( $upload['size'] ) ? (int) $upload['size'] : 0; }, $uploads ) );
	if ( $total_bytes > STC_TOOLS_MAX_UPLOAD_BYTES ) {
		stc_tools_cleanup_uploads( $uploads );
		return new WP_Error( 'upload_too_large', __( 'The selected photos must total 60 MB or less.', 'solo-to-china-tools' ), array( 'status' => 413 ) );
	}
	foreach ( $uploads as $upload ) {
		$inspected = stc_tools_validate_image_upload( $upload, true );
		if ( is_wp_error( $inspected ) ) { stc_tools_cleanup_uploads( $uploads ); return $inspected; }
	}
	$provider = stc_tools_get_vision_provider();
	if ( ! $provider instanceof STC_Place_Vision_Provider || ! $provider->is_available() ) {
		stc_tools_cleanup_uploads( $uploads );
		return new WP_Error( 'provider_unavailable', __( 'Place identification is temporarily unavailable.', 'solo-to-china-tools' ), array( 'status' => 503 ) );
	}
	$images = array();
	foreach ( $uploads as $upload ) {
		$image = stc_tools_validate_image_upload( $upload );
		if ( is_wp_error( $image ) ) {
			stc_tools_cleanup_uploads( $uploads );
			return $image;
		}
		$images[] = $image;
	}
	$city_hint = sanitize_text_field( (string) $request->get_param( 'city_hint' ) );
	$city_hint = function_exists( 'mb_substr' ) ? mb_substr( $city_hint, 0, 80 ) : substr( $city_hint, 0, 80 );
	$raw = stc_tools_guarded_vision( $provider, $images, array( 'city_hint' => $city_hint ) );
	unset( $images );
	if ( is_wp_error( $raw ) ) {
		$status = 'provider_timeout' === $raw->get_error_code() ? 504 : 503;
		$error_data = $raw->get_error_data();
		if ( is_array( $error_data ) && in_array( $error_data['status'] ?? 0, array( 413,415,429,503,504 ), true ) ) { $status = $error_data['status']; }
		return new WP_Error( $raw->get_error_code(), __( 'Place identification is temporarily unavailable. Try again shortly.', 'solo-to-china-tools' ), array( 'status' => $status ) );
	}
	if ( ! stc_tools_valid_vision_result( $raw ) ) { return new WP_Error( 'invalid_provider_response', 'The recognition service returned an unreadable result. Try again shortly.', array( 'status' => 503 ) ); }
	return stc_tools_private_response( stc_tools_prepare_vision_result( $raw ) );
}

function stc_tools_rest_taxi_card( WP_REST_Request $request ) {
	$limited = stc_tools_rate_limit( 'resolver', 30, 10 * MINUTE_IN_SECONDS );
	if ( is_wp_error( $limited ) ) { return $limited; }
	$entity_key = sanitize_title( (string) $request->get_param( 'entity_key' ) );
	if ( $entity_key ) {
		$place = stc_tools_get_place( $entity_key );
		return $place ? stc_tools_private_response( array( 'status' => 'resolved', 'place' => $place, 'copy_text' => stc_tools_copy_destination( $place ) ) ) : new WP_Error( 'unknown_place', __( 'We could not verify that destination. Try its full place name and city.', 'solo-to-china-tools' ), array( 'status' => 404 ) );
	}
	$query = trim( sanitize_text_field( (string) $request->get_param( 'query' ) ) );
	if ( '' === $query || strlen( $query ) > 160 ) { return new WP_Error( 'invalid_query', __( 'Enter a destination name.', 'solo-to-china-tools' ), array( 'status' => 400 ) ); }
	$matches = stc_tools_resolve_curated_place( $query );
	if ( count( $matches ) > 1 ) { return stc_tools_private_response( array( 'status' => 'ambiguous', 'candidates' => $matches ) ); }
	if ( 1 === count( $matches ) ) { return stc_tools_private_response( array( 'status' => 'resolved', 'place' => $matches[0], 'copy_text' => stc_tools_copy_destination( $matches[0] ) ) ); }
	$provider = stc_tools_get_resolver_provider();
	if ( $provider instanceof STC_Place_Resolver_Provider && $provider->is_available() ) {
		try { $raw = $provider->resolve( $query ); } catch ( Throwable $error ) { $raw = new WP_Error( 'provider_unavailable', 'Resolver failed.' ); }
		if ( is_wp_error( $raw ) ) {
			$status = 'provider_timeout' === $raw->get_error_code() ? 504 : 503;
			$data = $raw->get_error_data();
			if ( is_array( $data ) && in_array( $data['status'] ?? 0, array( 429, 503, 504 ), true ) ) { $status = $data['status']; }
			return new WP_Error( 'resolver_unavailable', __( 'Destination lookup is temporarily unavailable. Try again shortly.', 'solo-to-china-tools' ), array( 'status' => $status ) );
		}
		if ( ! is_wp_error( $raw ) ) {
			$result = stc_tools_prepare_resolver_result( $raw );
			if ( $result['primary_candidate'] && $result['primary_candidate']['entity_key'] ) { return stc_tools_private_response( array( 'status' => 'resolved', 'place' => $result['primary_candidate'], 'copy_text' => stc_tools_copy_destination( $result['primary_candidate'] ) ) ); }
		}
	}
	return new WP_Error( 'unknown_place', __( 'We could not verify that destination. Try its full place name and city.', 'solo-to-china-tools' ), array( 'status' => 404 ) );
}

function stc_tools_register_rest_routes() {
	register_rest_route( 'stc/v1', '/place-finder', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'stc_tools_rest_place_finder', 'permission_callback' => '__return_true' ) );
	register_rest_route( 'stc/v1', '/taxi-card', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'stc_tools_rest_taxi_card', 'permission_callback' => '__return_true', 'args' => array( 'query' => array( 'type' => 'string' ), 'entity_key' => array( 'type' => 'string' ) ) ) );
}
add_action( 'rest_api_init', 'stc_tools_register_rest_routes' );

function stc_tools_protect_rest_response( $response, $server, $request ) {
	$route = $request instanceof WP_REST_Request ? $request->get_route() : '';
	if ( 0 === strpos( $route, '/stc/v1/destination-catalog' ) || 0 === strpos( $route, '/stc/v1/place-finder' ) || 0 === strpos( $route, '/stc/v1/taxi-card' ) ) {
		$response->header( 'Cache-Control', 'private, no-store, max-age=0' );
		$response->header( 'X-Content-Type-Options', 'nosniff' );
	}
	return $response;
}
add_filter( 'rest_post_dispatch', 'stc_tools_protect_rest_response', 10, 3 );

function stc_tools_register_entity_meta() {
	register_post_meta( 'post', '_stc_entity_key', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_title', 'auth_callback' => function () { return current_user_can( 'edit_posts' ); } ) );
}
add_action( 'init', 'stc_tools_register_entity_meta' );
