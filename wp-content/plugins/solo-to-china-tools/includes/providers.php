<?php
/** Provider abstractions for place vision and destination resolution. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

interface STC_Place_Vision_Provider {
	public function is_available();
	public function identify( $images, $context = array() );
}

interface STC_Place_Resolver_Provider {
	public function is_available();
	public function resolve( $query );
}

function stc_tools_config( $constant, $environment ) {
	if ( defined( $constant ) ) { return trim( (string) constant( $constant ) ); }
	$value = getenv( $environment );
	return false === $value ? '' : trim( (string) $value );
}

class STC_HTTP_Place_Vision_Provider implements STC_Place_Vision_Provider {
	private $endpoint;
	private $token;
	public function __construct() {
		$this->endpoint = stc_tools_config( 'STC_PLACE_VISION_ENDPOINT', 'STC_PLACE_VISION_ENDPOINT' );
		$this->token = stc_tools_config( 'STC_PLACE_VISION_TOKEN', 'STC_PLACE_VISION_TOKEN' );
	}
	public function is_available() { return (bool) ( $this->endpoint && $this->token && 'https' === wp_parse_url( $this->endpoint, PHP_URL_SCHEME ) ); }
	public function identify( $images, $context = array() ) {
		$images = isset( $images['bytes'] ) ? array( $images ) : array_values( (array) $images );
		$payload_images = array_map(
			static function ( $image ) {
				return array(
					'mime_type' => isset( $image['mime'] ) ? $image['mime'] : '',
					'base64'    => isset( $image['bytes'] ) ? base64_encode( $image['bytes'] ) : '',
				);
			},
			$images
		);
		$response = wp_remote_post( $this->endpoint, array(
			'timeout' => min( 35, 15 + ( 5 * count( $payload_images ) ) ), 'redirection' => 0, 'limit_response_size' => 524288,
			'headers' => array( 'Authorization' => 'Bearer ' . $this->token, 'Content-Type' => 'application/json' ),
			'body' => wp_json_encode( array( 'images' => $payload_images, 'image_count' => count( $payload_images ), 'city_hint' => isset( $context['city_hint'] ) ? $context['city_hint'] : '', 'response_schema' => 'stc-place-v2' ) ),
		) );
		unset( $payload_images );
		if ( is_wp_error( $response ) ) { return new WP_Error( 'provider_timeout', 'Provider request failed.' ); }
		$code = wp_remote_retrieve_response_code( $response );
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return 200 === $code && is_array( $data ) ? $data : new WP_Error( 'provider_unavailable', 'Provider response was unavailable.', array( 'status' => in_array( $code, array( 413,415,429,503,504 ), true ) ? $code : 503 ) );
	}
}

class STC_HTTP_Place_Resolver_Provider implements STC_Place_Resolver_Provider {
	private $endpoint;
	private $token;
	public function __construct() {
		$this->endpoint = stc_tools_config( 'STC_PLACE_RESOLVER_ENDPOINT', 'STC_PLACE_RESOLVER_ENDPOINT' );
		$this->token = stc_tools_config( 'STC_PLACE_RESOLVER_TOKEN', 'STC_PLACE_RESOLVER_TOKEN' );
	}
	public function is_available() { return (bool) ( $this->endpoint && $this->token && 'https' === wp_parse_url( $this->endpoint, PHP_URL_SCHEME ) ); }
	public function resolve( $query ) {
		$response = wp_remote_post( $this->endpoint, array( 'timeout' => 12, 'redirection' => 0, 'limit_response_size' => 524288, 'headers' => array( 'Authorization' => 'Bearer ' . $this->token, 'Content-Type' => 'application/json' ), 'body' => wp_json_encode( array( 'query' => $query, 'response_schema' => 'stc-place-v1' ) ) ) );
		if ( is_wp_error( $response ) ) { return new WP_Error( 'provider_timeout', 'Resolver request failed.' ); }
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		$code = wp_remote_retrieve_response_code( $response );
		return 200 === $code && is_array( $data ) ? $data : new WP_Error( 'provider_unavailable', 'Resolver response was unavailable.', array( 'status' => in_array( $code, array( 429, 503, 504 ), true ) ? $code : 503 ) );
	}
}

function stc_tools_get_vision_provider() { return apply_filters( 'stc_tools_place_vision_provider', new STC_HTTP_Place_Vision_Provider() ); }
function stc_tools_get_resolver_provider() { return apply_filters( 'stc_tools_place_resolver_provider', new STC_HTTP_Place_Resolver_Provider() ); }

function stc_tools_normalize_provider_candidate( $candidate, $confidence = 'low', $match_level = 'unknown' ) {
	if ( ! is_array( $candidate ) ) { return null; }
	foreach ( $candidate as $field => $value ) {
		if ( 'visible_clues' === $field ) {
			if ( ! is_array( $value ) || count( $value ) > 8 || count( array_filter( $value, 'is_string' ) ) !== count( $value ) ) { return null; }
		} elseif ( in_array($field,array('candidate_entity_key','canonical_name_en','canonical_name_zh','city_en','city_zh','province','recognition_reason','viewpoint_name_en','viewpoint_name_zh','viewpoint_description','match_level'),true) && (!is_string($value) || strlen($value)>1500) ) { return null; }
	}
	$key = isset( $candidate['candidate_entity_key'] ) ? sanitize_title( $candidate['candidate_entity_key'] ) : '';
	$verified = $key ? stc_tools_get_place( $key ) : null;
	$place = $verified ? $verified : array(
		'entity_key' => '', 'name_en' => isset( $candidate['canonical_name_en'] ) ? $candidate['canonical_name_en'] : '', 'name_zh' => isset( $candidate['canonical_name_zh'] ) ? $candidate['canonical_name_zh'] : '',
		'city_en' => isset( $candidate['city_en'] ) ? $candidate['city_en'] : '', 'city_zh' => isset( $candidate['city_zh'] ) ? $candidate['city_zh'] : '', 'province_en' => isset( $candidate['province'] ) ? $candidate['province'] : '',
		'country' => 'China', 'sources' => array( 'name_zh' => 'AI_INFERRED', 'city' => 'AI_INFERRED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
	);
	$place['confidence'] = $confidence;
	$place['match_level'] = $match_level;
	$place['recognition_reason'] = isset( $candidate['recognition_reason'] ) ? $candidate['recognition_reason'] : '';
	$place['visible_clues'] = isset( $candidate['visible_clues'] ) ? $candidate['visible_clues'] : array();
	$viewpoint_verified = $verified && isset( $verified['sources']['viewpoint'] ) && 'VERIFIED' === $verified['sources']['viewpoint'];
	foreach ( array( 'viewpoint_name_en', 'viewpoint_name_zh', 'viewpoint_description' ) as $field ) {
		$place[$field] = $viewpoint_verified ? $verified[$field] : ( $candidate[$field] ?? '' );
	}
	$place['viewpoint_status'] = $viewpoint_verified ? 'verified' : ( ! empty( $place['viewpoint_name_en'] ) || ! empty( $place['viewpoint_name_zh'] ) ? 'likely' : 'unknown' );
	$place['sources']['viewpoint'] = 'verified' === $place['viewpoint_status'] ? 'VERIFIED' : ( 'likely' === $place['viewpoint_status'] ? 'AI_INFERRED' : 'UNKNOWN' );
	return stc_tools_public_place( $place );
}

/** Explicit legacy resolver-v1 adapter. Unknown schemas never masquerade as vision output. */
function stc_tools_prepare_resolver_result( $raw ) {
    if ( ! is_array( $raw ) ) { return array( 'primary_candidate' => null ); }
    if ( isset( $raw['primary_candidate'], $raw['confidence'] ) ) { return stc_tools_prepare_vision_result( $raw ); }
    if ( 'stc-place-v1' === ( $raw['schema'] ?? '' ) && 'resolved' === ( $raw['status'] ?? '' ) && ! empty( $raw['entity_key'] ) ) {
        return array( 'primary_candidate' => stc_tools_get_place( sanitize_title( $raw['entity_key'] ) ) );
    }
    return array( 'primary_candidate' => null );
}

/** Validate before normalization; malformed provider JSON is a service error. */
function stc_tools_valid_vision_result( $raw ) {
 if (!is_array($raw) || !in_array($raw['status'] ?? null,array('matched','identified','unknown','ambiguous','possible'),true) || !in_array($raw['confidence'] ?? null,array('high','medium','low'),true) || !in_array($raw['match_level'] ?? null,array('exact_viewpoint','attraction','neighborhood','city','unknown'),true)) { return false; }
 if (isset($raw['schema']) && 'stc-place-v2' !== $raw['schema']) { return false; }
 if (!array_key_exists('primary_candidate',$raw) || !isset($raw['alternative_candidates']) || !is_array($raw['alternative_candidates']) || count($raw['alternative_candidates'])>4) { return false; }
 if (null !== $raw['primary_candidate'] && !stc_tools_normalize_provider_candidate($raw['primary_candidate'])) { return false; }
 foreach($raw['alternative_candidates'] as $candidate) { if (!stc_tools_normalize_provider_candidate($candidate)) { return false; } }
 return 'high' !== $raw['confidence'] || (is_array($raw['primary_candidate']) && !empty($raw['primary_candidate']));
}
