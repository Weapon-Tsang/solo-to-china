<?php
/**
 * Versioned Content Contract, REST capability endpoint, and guide metadata.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_CONTENT_CONTRACT_VERSION', '2.1.0' );

/**
 * Return the absolute path to the canonical machine-readable contract.
 *
 * @return string
 */
function stc_content_contract_path() {
	return get_template_directory() . '/content-contract/content-contract.v2.json';
}

/**
 * Load and memoize the canonical Content Contract.
 *
 * @return array<string, mixed>
 */
function stc_get_content_contract() {
	static $contract = null;

	if ( null !== $contract ) {
		return $contract;
	}

	$path = stc_content_contract_path();

	if ( ! is_readable( $path ) ) {
		$contract = [];
		return $contract;
	}

	$decoded  = json_decode( file_get_contents( $path ), true );
	$contract = is_array( $decoded ) ? $decoded : [];

	if ( $contract ) {
		$page_blocks            = stc_get_cms_component_definitions( 'page_block' );
		$page_block_ids         = stc_component_registry_page_block_ids();
		$dynamic_component_ids  = array_values(
			array_map(
				static function ( $component ) {
					return $component['id'];
				},
				array_filter(
					$page_blocks,
					static function ( $component ) {
						return isset( $component['render_mode'] ) && 'shortcode' === $component['render_mode'];
					}
				)
			)
		);

		$contract['components'] = $page_blocks;
		foreach ( $contract['guide_types'] as &$guide_type ) {
			$guide_type['allowed_components']           = $page_block_ids;
			$guide_type['optional_dynamic_components'] = $dynamic_component_ids;
		}
		unset( $guide_type );
	}

	return $contract;
}

/**
 * Return the supported public guide type slugs.
 *
 * @return string[]
 */
function stc_allowed_guide_types() {
	$contract = stc_get_content_contract();

	return isset( $contract['guide_types'] ) && is_array( $contract['guide_types'] )
		? array_keys( $contract['guide_types'] )
		: [ 'survival-kit', 'city-guide', 'attraction-guide', 'travel-guide' ];
}

/**
 * Return one guide type definition.
 *
 * @param string $guide_type Guide type slug.
 * @return array<string, mixed>|null
 */
function stc_get_guide_type_contract( $guide_type ) {
	$contract   = stc_get_content_contract();
	$guide_type = sanitize_key( $guide_type );

	return isset( $contract['guide_types'][ $guide_type ] ) ? $contract['guide_types'][ $guide_type ] : null;
}

/**
 * Resolve the stable WordPress category slug for a guide type.
 *
 * @param string $guide_type Guide type slug.
 * @return string
 */
function stc_guide_type_category_slug( $guide_type ) {
	$definition = stc_get_guide_type_contract( $guide_type );

	return $definition && ! empty( $definition['category_slug'] ) ? sanitize_title( $definition['category_slug'] ) : '';
}

/**
 * Serve the public, read-only capability contract.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_get_content_contract( $request ) {
	$contract = stc_get_content_contract();
	$path     = stc_content_contract_path();
	$registry_path = stc_component_registry_path();

	if ( ! $contract ) {
		return new WP_Error(
			'stc_content_contract_unavailable',
			__( 'The SoloToChina Content Contract is unavailable.', 'solo-to-china' ),
			[ 'status' => 500 ]
		);
	}

	$modified = max( filemtime( $path ), filemtime( $registry_path ) );
	$etag     = '"' . hash( 'sha256', file_get_contents( $path ) . file_get_contents( $registry_path ) ) . '"';

	if ( $etag === trim( (string) $request->get_header( 'if-none-match' ) ) ) {
		$response = new WP_REST_Response( null, 304 );
	} else {
		$response = new WP_REST_Response( $contract, 200 );
	}

	$response->header( 'ETag', $etag );
	$response->header( 'Last-Modified', gmdate( 'D, d M Y H:i:s', $modified ) . ' GMT' );
	$response->header( 'Cache-Control', 'public, max-age=300, stale-while-revalidate=86400' );

	return $response;
}

/**
 * Register the public Content Contract endpoint.
 */
function stc_register_content_contract_route() {
	register_rest_route(
		'stc/v1',
		'/content-contract',
		[
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'stc_rest_get_content_contract',
			'permission_callback' => '__return_true',
		]
	);
}
add_action( 'rest_api_init', 'stc_register_content_contract_route' );

/**
 * Sanitize guide type metadata against the Contract allowlist.
 *
 * @param mixed $value Metadata value.
 * @return string
 */
function stc_sanitize_guide_type_meta( $value ) {
	$value = sanitize_key( (string) $value );

	return in_array( $value, stc_allowed_guide_types(), true ) ? $value : '';
}

/**
 * Sanitize the per-post Contract version to the supported version.
 *
 * @param mixed $value Metadata value.
 * @return string
 */
function stc_sanitize_content_contract_version_meta( $value ) {
	$value = sanitize_text_field( (string) $value );

	return STC_CONTENT_CONTRACT_VERSION === $value ? $value : '';
}

/**
 * Normalize CMS-controlled presentation flags to a strict boolean.
 *
 * @param mixed $value Metadata value.
 * @return bool
 */
function stc_sanitize_presentation_flag_meta( $value ) {
	return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Allow only Theme-owned visual variants explicitly requested by the CMS.
 *
 * @param mixed $value Metadata value.
 * @return string
 */
function stc_sanitize_hero_variant_meta( $value ) {
	$value   = sanitize_key( (string) $value );
	$allowed = array( 'default', 'attraction', 'city', 'survival' );

	return in_array( $value, $allowed, true ) ? $value : 'default';
}

/**
 * Restrict REST metadata writes to users who can edit the post.
 *
 * @param bool   $allowed   Existing capability result.
 * @param string $meta_key  Metadata key.
 * @param int    $object_id Post ID.
 * @return bool
 */
function stc_content_meta_auth_callback( $allowed, $meta_key, $object_id ) {
	unset( $allowed, $meta_key );

	return $object_id ? current_user_can( 'edit_post', $object_id ) : current_user_can( 'edit_posts' );
}

/**
 * Register stable CMS-facing guide metadata for posts.
 */
function stc_register_content_contract_meta() {
	register_post_meta(
		'post',
		'_stc_guide_type',
		[
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'stc_sanitize_guide_type_meta',
			'auth_callback'     => 'stc_content_meta_auth_callback',
			'show_in_rest'      => [
				'schema' => [
					'type' => 'string',
					'enum' => stc_allowed_guide_types(),
				],
			],
		]
	);

	register_post_meta(
		'post',
		'_stc_content_contract_version',
		[
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'stc_sanitize_content_contract_version_meta',
			'auth_callback'     => 'stc_content_meta_auth_callback',
			'show_in_rest'      => [
				'schema' => [
					'type' => 'string',
					'enum' => [ STC_CONTENT_CONTRACT_VERSION ],
				],
			],
		]
	);

	foreach ( array( '_stc_show_share', '_stc_show_toc' ) as $flag_key ) {
		register_post_meta(
			'post',
			$flag_key,
			array(
				'type'              => 'boolean',
				'single'            => true,
				'sanitize_callback' => 'stc_sanitize_presentation_flag_meta',
				'auth_callback'     => 'stc_content_meta_auth_callback',
				'show_in_rest'      => true,
			)
		);
	}

	register_post_meta(
		'post',
		'_stc_hero_variant',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'stc_sanitize_hero_variant_meta',
			'auth_callback'     => 'stc_content_meta_auth_callback',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
					'enum' => array( 'default', 'attraction', 'city', 'survival' ),
				),
			),
		)
	);
}
add_action( 'init', 'stc_register_content_contract_meta' );

/**
 * Check a CMS-selected page utility without inferring it from content type.
 *
 * @param string   $utility Utility name: share or toc.
 * @param int|null $post_id Post ID.
 * @return bool
 */
function stc_page_presentation_enabled( $utility, $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$keys    = array(
		'share' => '_stc_show_share',
		'toc'   => '_stc_show_toc',
	);

	if ( ! $post_id || ! isset( $keys[ $utility ] ) ) {
		return false;
	}

	return stc_sanitize_presentation_flag_meta( get_post_meta( $post_id, $keys[ $utility ], true ) );
}

/**
 * Return the CMS-selected Hero presentation variant.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function stc_get_hero_variant( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

	return $post_id ? stc_sanitize_hero_variant_meta( get_post_meta( $post_id, '_stc_hero_variant', true ) ) : 'default';
}

/**
 * Read an explicitly stored guide type without guessing from the title.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function stc_get_explicit_guide_type( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	return stc_sanitize_guide_type_meta( get_post_meta( $post_id, '_stc_guide_type', true ) );
}
