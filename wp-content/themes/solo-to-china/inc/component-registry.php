<?php
/**
 * Canonical Frontend Component Registry and public capability endpoint.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_COMPONENT_REGISTRY_VERSION', '1.1.0' );

/**
 * Return the canonical Component Registry path.
 *
 * @return string
 */
function stc_component_registry_path() {
	return get_template_directory() . '/content-contract/component-registry.v1.json';
}

/**
 * Return a generated CMS artifact path bundled with the Theme.
 *
 * @param string $artifact Artifact filename.
 * @return string
 */
function stc_generated_component_artifact_path( $artifact ) {
	return get_template_directory() . '/content-contract/' . $artifact;
}

/**
 * Load and memoize the canonical Frontend Component Registry.
 *
 * @return array<string, mixed>
 */
function stc_get_component_registry() {
	static $registry = null;

	if ( null !== $registry ) {
		return $registry;
	}

	$path = stc_component_registry_path();
	if ( ! is_readable( $path ) ) {
		$registry = array();
		return $registry;
	}

	$decoded  = json_decode( file_get_contents( $path ), true );
	$registry = is_array( $decoded ) ? $decoded : array();

	return $registry;
}

/**
 * Return a stable component definition by its published API ID.
 *
 * @param string $component_id Stable component ID.
 * @return array<string, mixed>|null
 */
function stc_get_component_definition( $component_id ) {
	$component_id = sanitize_key( $component_id );
	$registry     = stc_get_component_registry();
	$components   = isset( $registry['components'] ) && is_array( $registry['components'] ) ? $registry['components'] : array();

	foreach ( $components as $component ) {
		if ( isset( $component['id'] ) && $component_id === $component['id'] ) {
			return $component;
		}
	}

	return null;
}

/**
 * Return components explicitly available to the CMS.
 *
 * @param string|null $interface Optional page_block or presentation_meta filter.
 * @return array<int, array<string, mixed>>
 */
function stc_get_cms_component_definitions( $interface = null ) {
	$registry   = stc_get_component_registry();
	$components = isset( $registry['components'] ) && is_array( $registry['components'] ) ? $registry['components'] : array();

	return array_values(
		array_filter(
			$components,
			static function ( $component ) use ( $interface ) {
				if ( empty( $component['cms_usable'] ) ) {
					return false;
				}

				return null === $interface || ( isset( $component['cms_interface'] ) && $interface === $component['cms_interface'] );
			}
		)
	);
}

/**
 * Return stable page.blocks[] IDs for Contract compatibility output.
 *
 * @return string[]
 */
function stc_component_registry_page_block_ids() {
	return array_values(
		array_map(
			static function ( $component ) {
				return $component['id'];
			},
			stc_get_cms_component_definitions( 'page_block' )
		)
	);
}

/**
 * Serve the complete read-only Frontend Component Registry.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_get_component_registry( $request ) {
	$registry = stc_get_component_registry();
	$path     = stc_component_registry_path();

	if ( ! $registry ) {
		return new WP_Error(
			'stc_component_registry_unavailable',
			__( 'The SoloToChina Component Registry is unavailable.', 'solo-to-china' ),
			array( 'status' => 500 )
		);
	}

	$modified = filemtime( $path );
	$etag     = '"' . hash_file( 'sha256', $path ) . '"';
	$response = $etag === trim( (string) $request->get_header( 'if-none-match' ) )
		? new WP_REST_Response( null, 304 )
		: new WP_REST_Response( $registry, 200 );

	$response->header( 'ETag', $etag );
	$response->header( 'Last-Modified', gmdate( 'D, d M Y H:i:s', $modified ) . ' GMT' );
	$response->header( 'Cache-Control', 'public, max-age=300, stale-while-revalidate=86400' );

	return $response;
}

/**
 * Serve a deterministic generated JSON artifact with stable cache validators.
 *
 * @param WP_REST_Request $request REST request.
 * @param string          $path Artifact path.
 * @param string          $error_code Error code.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_get_generated_artifact( $request, $path, $error_code ) {
	if ( ! is_readable( $path ) ) {
		return new WP_Error( $error_code, __( 'The requested SoloToChina Contract artifact is unavailable.', 'solo-to-china' ), array( 'status' => 500 ) );
	}

	$raw     = file_get_contents( $path );
	$decoded = json_decode( $raw, true );
	if ( ! is_array( $decoded ) ) {
		return new WP_Error( $error_code, __( 'The requested SoloToChina Contract artifact is invalid.', 'solo-to-china' ), array( 'status' => 500 ) );
	}

	$modified = filemtime( $path );
	$etag     = '"' . hash( 'sha256', $raw ) . '"';
	$response = $etag === trim( (string) $request->get_header( 'if-none-match' ) )
		? new WP_REST_Response( null, 304 )
		: new WP_REST_Response( $decoded, 200 );
	$response->header( 'ETag', $etag );
	$response->header( 'Last-Modified', gmdate( 'D, d M Y H:i:s', $modified ) . ' GMT' );
	$response->header( 'Cache-Control', 'public, max-age=300, stale-while-revalidate=86400' );

	return $response;
}

/**
 * Serve the generated CMS Component Contract shape.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_get_generated_component_registry( $request ) {
	return stc_rest_get_generated_artifact( $request, stc_generated_component_artifact_path( 'component-registry.generated.json' ), 'stc_generated_component_registry_unavailable' );
}

/**
 * Serve the generated CMS Page Schema.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_get_page_schema( $request ) {
	return stc_rest_get_generated_artifact( $request, stc_generated_component_artifact_path( 'page-schema.generated.json' ), 'stc_page_schema_unavailable' );
}

/**
 * Register the public Component Registry endpoint.
 */
function stc_register_component_registry_route() {
	register_rest_route(
		'stc/v1',
		'/component-registry',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'stc_rest_get_component_registry',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'stc/v1',
		'/component-registry/generated',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'stc_rest_get_generated_component_registry',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'stc/v1',
		'/page-schema',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'stc_rest_get_page_schema',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'stc_register_component_registry_route' );
