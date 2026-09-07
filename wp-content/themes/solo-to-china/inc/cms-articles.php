<?php
/**
 * Contract-aware CMS Publish Package ingestion for WordPress drafts.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_CMS_PUBLISH_PACKAGE_VERSION', '1.0.0' );
define( 'STC_CMS_PUBLISH_MAX_BYTES', 1048576 );

/**
 * Return the bundled generated Publish Package Schema path.
 *
 * @return string
 */
function stc_cms_publish_package_schema_path() {
	return stc_generated_component_artifact_path( 'cms-publish-package.generated.json' );
}

/**
 * Load a bundled generated JSON artifact.
 *
 * @param string $path Artifact path.
 * @return array<string, mixed>
 */
function stc_cms_load_json_artifact( $path ) {
	if ( ! is_readable( $path ) ) {
		return array();
	}

	$decoded = json_decode( file_get_contents( $path ), true );
	return is_array( $decoded ) ? $decoded : array();
}

/**
 * Return the exact checksum of the generated CMS Component Contract.
 *
 * @return string
 */
function stc_cms_component_contract_checksum() {
	$path = stc_generated_component_artifact_path( 'component-registry.generated.json' );
	return is_readable( $path ) ? strtolower( hash_file( 'sha256', $path ) ) : '';
}

/**
 * Build a consistent REST validation error.
 *
 * @param string $code Error code.
 * @param string $message Public message.
 * @param int    $status HTTP status.
 * @param string $path Optional payload path.
 * @return WP_Error
 */
function stc_cms_publish_error( $code, $message, $status = 400, $path = '' ) {
	$data = array( 'status' => $status );
	if ( $path ) {
		$data['path'] = $path;
	}

	return new WP_Error( $code, $message, $data );
}

/**
 * Determine whether a PHP array is a JSON-style list.
 *
 * @param mixed $value Candidate value.
 * @return bool
 */
function stc_cms_is_list( $value ) {
	if ( ! is_array( $value ) ) {
		return false;
	}

	$index = 0;
	foreach ( $value as $key => $unused ) {
		unset( $unused );
		if ( $key !== $index ) {
			return false;
		}
		$index++;
	}

	return true;
}

/**
 * Return a UTF-8 aware string length.
 *
 * @param string $value Text value.
 * @return int
 */
function stc_cms_string_length( $value ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
}

/**
 * Allow only editorial inline markup that Gutenberg can preserve safely.
 *
 * @return array<string, array<string, bool>>
 */
function stc_cms_allowed_inline_html() {
	return array(
		'a'      => array( 'href' => true, 'title' => true, 'target' => true, 'rel' => true ),
		'br'     => array(),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'code'   => array(),
		's'      => array(),
		'sup'    => array(),
		'sub'    => array(),
	);
}

/**
 * Confirm that HTML-valued component text contains no executable/raw markup.
 *
 * @param string $value Candidate inline HTML.
 * @return bool
 */
function stc_cms_inline_html_is_safe( $value ) {
	return wp_kses( $value, stc_cms_allowed_inline_html(), array( 'http', 'https', 'mailto' ) ) === $value;
}

/**
 * Match a decoded JSON value against a supported JSON Schema type.
 *
 * @param mixed  $value Candidate value.
 * @param string $type JSON Schema type.
 * @return bool
 */
function stc_cms_value_matches_type( $value, $type ) {
	switch ( $type ) {
		case 'object':
			return is_array( $value ) && ( empty( $value ) || ! stc_cms_is_list( $value ) );
		case 'array':
			return is_array( $value ) && stc_cms_is_list( $value );
		case 'string':
			return is_string( $value );
		case 'integer':
			return is_int( $value );
		case 'number':
			return is_int( $value ) || is_float( $value );
		case 'boolean':
			return is_bool( $value );
		case 'null':
			return null === $value;
	}

	return false;
}

/**
 * Validate the JSON Schema subset used by the generated frontend contracts.
 *
 * @param mixed                $value Value to validate.
 * @param array<string, mixed> $schema Generated schema node.
 * @param string               $path Payload path.
 * @param string               $error_code Error code.
 * @return true|WP_Error
 */
function stc_cms_validate_schema_value( $value, $schema, $path, $error_code ) {
	$base_schema = $schema;
	$branches    = isset( $base_schema['oneOf'] ) && is_array( $base_schema['oneOf'] ) ? $base_schema['oneOf'] : array();
	unset( $base_schema['oneOf'] );

	if ( array_key_exists( 'const', $base_schema ) && $value !== $base_schema['const'] ) {
		return stc_cms_publish_error( $error_code, __( 'A value does not match the required constant.', 'solo-to-china' ), 422, $path );
	}
	if ( isset( $base_schema['enum'] ) && ! in_array( $value, $base_schema['enum'], true ) ) {
		return stc_cms_publish_error( $error_code, __( 'A value is outside the supported enum.', 'solo-to-china' ), 422, $path );
	}

	if ( isset( $base_schema['type'] ) ) {
		$types   = is_array( $base_schema['type'] ) ? $base_schema['type'] : array( $base_schema['type'] );
		$matched = false;
		foreach ( $types as $type ) {
			if ( stc_cms_value_matches_type( $value, $type ) ) {
				$matched = true;
				break;
			}
		}
		if ( ! $matched ) {
			return stc_cms_publish_error( $error_code, __( 'A value has the wrong JSON type.', 'solo-to-china' ), 422, $path );
		}
	}

	if ( is_string( $value ) ) {
		$length = stc_cms_string_length( $value );
		if ( isset( $base_schema['minLength'] ) && $length < (int) $base_schema['minLength'] ) {
			return stc_cms_publish_error( $error_code, __( 'A string is shorter than allowed.', 'solo-to-china' ), 422, $path );
		}
		if ( isset( $base_schema['maxLength'] ) && $length > (int) $base_schema['maxLength'] ) {
			return stc_cms_publish_error( $error_code, __( 'A string is longer than allowed.', 'solo-to-china' ), 422, $path );
		}
		if ( isset( $base_schema['pattern'] ) && 1 !== preg_match( '~' . str_replace( '~', '\\~', $base_schema['pattern'] ) . '~u', $value ) ) {
			return stc_cms_publish_error( $error_code, __( 'A string does not match the required pattern.', 'solo-to-china' ), 422, $path );
		}
		if ( isset( $base_schema['format'] ) && 'uri' === $base_schema['format'] ) {
			$user = wp_parse_url( $value, PHP_URL_USER );
			$pass = wp_parse_url( $value, PHP_URL_PASS );
			if ( ! wp_http_validate_url( $value ) || null !== $user || null !== $pass ) {
				return stc_cms_publish_error( $error_code, __( 'A URL is invalid.', 'solo-to-china' ), 422, $path );
			}
		}
		if ( isset( $base_schema['format'] ) && 'date-time' === $base_schema['format'] ) {
			$pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})$/';
			if ( 1 !== preg_match( $pattern, $value ) || false === strtotime( $value ) ) {
				return stc_cms_publish_error( $error_code, __( 'A date-time value is invalid.', 'solo-to-china' ), 422, $path );
			}
		}
		if ( isset( $base_schema['contentMediaType'] ) && 'text/html' === $base_schema['contentMediaType'] && ! stc_cms_inline_html_is_safe( $value ) ) {
			return stc_cms_publish_error( $error_code, __( 'Executable or unsupported HTML is not allowed.', 'solo-to-china' ), 422, $path );
		}
	}

	if ( ( is_int( $value ) || is_float( $value ) ) && isset( $base_schema['minimum'] ) && $value < $base_schema['minimum'] ) {
		return stc_cms_publish_error( $error_code, __( 'A number is below the supported minimum.', 'solo-to-china' ), 422, $path );
	}
	if ( ( is_int( $value ) || is_float( $value ) ) && isset( $base_schema['maximum'] ) && $value > $base_schema['maximum'] ) {
		return stc_cms_publish_error( $error_code, __( 'A number exceeds the supported maximum.', 'solo-to-china' ), 422, $path );
	}

	$is_object_schema = isset( $base_schema['properties'] ) || isset( $base_schema['required'] ) || array_key_exists( 'additionalProperties', $base_schema );
	if ( is_array( $value ) && $is_object_schema ) {
		$required = isset( $base_schema['required'] ) ? $base_schema['required'] : array();
		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $value ) ) {
				return stc_cms_publish_error( $error_code, __( 'A required field is missing.', 'solo-to-china' ), 422, $path . '.' . $key );
			}
		}

		$properties = isset( $base_schema['properties'] ) && is_array( $base_schema['properties'] ) ? $base_schema['properties'] : array();
		foreach ( $value as $key => $child ) {
			if ( isset( $properties[ $key ] ) ) {
				$result = stc_cms_validate_schema_value( $child, $properties[ $key ], $path . '.' . $key, $error_code );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
				continue;
			}
			if ( isset( $base_schema['additionalProperties'] ) && false === $base_schema['additionalProperties'] ) {
				return stc_cms_publish_error( $error_code, __( 'An unknown field is not allowed.', 'solo-to-china' ), 422, $path . '.' . $key );
			}
			if ( isset( $base_schema['additionalProperties'] ) && is_array( $base_schema['additionalProperties'] ) ) {
				$result = stc_cms_validate_schema_value( $child, $base_schema['additionalProperties'], $path . '.' . $key, $error_code );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}
	}

	if ( is_array( $value ) && stc_cms_is_list( $value ) ) {
		if ( isset( $base_schema['minItems'] ) && count( $value ) < (int) $base_schema['minItems'] ) {
			return stc_cms_publish_error( $error_code, __( 'An array contains too few items.', 'solo-to-china' ), 422, $path );
		}
		if ( isset( $base_schema['maxItems'] ) && count( $value ) > (int) $base_schema['maxItems'] ) {
			return stc_cms_publish_error( $error_code, __( 'An array contains too many items.', 'solo-to-china' ), 422, $path );
		}
		if ( isset( $base_schema['items'] ) && is_array( $base_schema['items'] ) ) {
			foreach ( $value as $index => $child ) {
				$result = stc_cms_validate_schema_value( $child, $base_schema['items'], $path . '[' . $index . ']', $error_code );
				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}
	}

	if ( $branches ) {
		$matches = 0;
		foreach ( $branches as $branch ) {
			if ( true === stc_cms_validate_schema_value( $value, $branch, $path, $error_code ) ) {
				$matches++;
			}
		}
		if ( 1 !== $matches ) {
			return stc_cms_publish_error( $error_code, __( 'A value must match exactly one supported shape.', 'solo-to-china' ), 422, $path );
		}
	}

	return true;
}

/**
 * Validate affiliate URLs and structured commercial data with Theme-owned rules.
 *
 * @param string               $component_id Component ID.
 * @param array<string, mixed> $data Component data.
 * @return true|WP_Error
 */
function stc_cms_validate_commercial_component( $component_id, $data ) {
	// Registry 1.1 keeps the historical affiliate_cta as an HTTPS-only fallback.
	// The four structured affiliate components use the stricter provider allowlist.
	if ( 'affiliate_cta' === $component_id ) {
		return true;
	}
	foreach ( array( 'target_url', 'image_url' ) as $url_key ) {
		if ( ! empty( $data[ $url_key ] ) && '' === stc_validate_affiliate_url( $data[ $url_key ] ) ) {
			return stc_cms_publish_error( 'UNSAFE_AFFILIATE_URL', __( 'An affiliate URL is outside the HTTPS host allowlist.', 'solo-to-china' ), 422, 'page.blocks[].data.' . $url_key );
		}
	}
	if ( ! empty( $data['embed_config']['src'] ) && '' === stc_validate_affiliate_url( $data['embed_config']['src'] ) ) {
		return stc_cms_publish_error( 'UNSAFE_AFFILIATE_URL', __( 'An affiliate embed URL is outside the HTTPS host allowlist.', 'solo-to-china' ), 422, 'page.blocks[].data.embed_config.src' );
	}

	if ( 'affiliate_search_card' === $component_id && ! empty( $data['embed_config'] ) && ! stc_parse_affiliate_embed_config( $data['embed_config'], 'search_box' ) ) {
		return stc_cms_publish_error( 'INVALID_COMMERCIAL_COMPONENT', __( 'The search embed configuration is invalid.', 'solo-to-china' ), 422 );
	}
	if ( 'affiliate_banner' === $component_id ) {
		$is_static  = isset( $data['asset_type'] ) && 'STATIC_BANNER' === $data['asset_type'];
		$has_embed  = ! empty( $data['embed_config'] );
		$has_image  = ! empty( $data['image_url'] ) || ! empty( $data['alt_text'] );
		if ( ( $is_static && ( $has_embed || empty( $data['image_url'] ) || empty( $data['alt_text'] ) ) ) || ( ! $is_static && ( $has_image || ! stc_parse_affiliate_embed_config( $data['embed_config'], 'dynamic_banner' ) ) ) ) {
			return stc_cms_publish_error( 'INVALID_COMMERCIAL_COMPONENT', __( 'The banner representation does not match its asset type.', 'solo-to-china' ), 422 );
		}
	}
	if ( 'affiliate_promotion_card' === $component_id ) {
		$from  = empty( $data['valid_from'] ) ? false : strtotime( $data['valid_from'] );
		$until = empty( $data['valid_until'] ) ? false : strtotime( $data['valid_until'] );
		if ( ( ! empty( $data['valid_from'] ) && false === $from ) || ( ! empty( $data['valid_until'] ) && false === $until ) || ( false !== $from && false !== $until && $from > $until ) ) {
			return stc_cms_publish_error( 'INVALID_COMMERCIAL_COMPONENT', __( 'The promotion validity window is invalid.', 'solo-to-china' ), 422 );
		}
	}

	return true;
}

/**
 * Validate a single ordered Page Payload component against the canonical Registry.
 *
 * @param mixed $block Decoded block.
 * @param int   $index Block index.
 * @return true|WP_Error
 */
function stc_cms_validate_page_block( $block, $index ) {
	$path = 'page.blocks[' . $index . ']';
	if ( ! is_array( $block ) || stc_cms_is_list( $block ) || array_diff( array_keys( $block ), array( 'type', 'variant', 'data' ) ) || array_diff( array( 'type', 'variant', 'data' ), array_keys( $block ) ) ) {
		return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'A page block must contain only type, variant, and data.', 'solo-to-china' ), 422, $path );
	}

	$component_id = sanitize_key( (string) $block['type'] );
	$definition   = stc_get_component_definition( $component_id );
	if ( ! $definition || empty( $definition['cms_usable'] ) || 'page_block' !== $definition['cms_interface'] ) {
		return stc_cms_publish_error( 'UNKNOWN_COMPONENT', __( 'The Page Payload contains an unavailable component.', 'solo-to-china' ), 422, $path . '.type' );
	}
	if ( isset( $definition['status'] ) && 'deprecated' === $definition['status'] ) {
		return stc_cms_publish_error( 'DEPRECATED_COMPONENT', __( 'The Page Payload contains a deprecated component.', 'solo-to-china' ), 422, $path . '.type' );
	}
	if ( ! is_string( $block['variant'] ) || ! in_array( $block['variant'], $definition['variants'], true ) ) {
		return stc_cms_publish_error( 'UNSUPPORTED_VARIANT', __( 'The component variant is not supported.', 'solo-to-china' ), 422, $path . '.variant' );
	}

	$component_error_code = isset( $definition['category'] ) && 'commercial' === $definition['category'] ? 'INVALID_COMMERCIAL_COMPONENT' : 'INVALID_COMPONENT_DATA';
	$result = stc_cms_validate_schema_value( $block['data'], $definition['schema'], $path . '.data', $component_error_code );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	if ( 'heading' === $component_id ) {
		$expected_level = 'section' === $block['variant'] ? 2 : 3;
		if ( $expected_level !== $block['data']['level'] ) {
			return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'Heading level and variant disagree.', 'solo-to-china' ), 422, $path );
		}
	}
	if ( 'list' === $component_id && isset( $block['data']['ordered'] ) && (bool) $block['data']['ordered'] !== ( 'ordered' === $block['variant'] ) ) {
		return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'List ordering and variant disagree.', 'solo-to-china' ), 422, $path );
	}
	if ( 'image' === $component_id && isset( $block['data']['role'] ) && $block['data']['role'] !== $block['variant'] ) {
		return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'Image role and variant disagree.', 'solo-to-china' ), 422, $path );
	}
	if ( 'comparison_table' === $component_id ) {
		$column_count = count( $block['data']['columns'] );
		foreach ( $block['data']['rows'] as $row ) {
			if ( count( $row ) !== $column_count ) {
				return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'Every comparison row must match the column count.', 'solo-to-china' ), 422, $path . '.data.rows' );
			}
		}
	}

	if ( isset( $definition['category'] ) && 'commercial' === $definition['category'] ) {
		return stc_cms_validate_commercial_component( $component_id, $block['data'] );
	}

	return true;
}

/**
 * Validate the Page Payload with the generated Page Schema and canonical Registry.
 *
 * @param mixed $page Page Payload.
 * @return true|WP_Error
 */
function stc_validate_cms_page_payload( $page ) {
	$page_schema = stc_cms_load_json_artifact( stc_generated_component_artifact_path( 'page-schema.generated.json' ) );
	if ( ! $page_schema ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The generated Page Schema is unavailable.', 'solo-to-china' ), 500 );
	}
	if ( ! is_array( $page ) || stc_cms_is_list( $page ) || array_diff( array_keys( $page ), array( 'metadata', 'blocks' ) ) || ! isset( $page['metadata'], $page['blocks'] ) ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The Page Payload shape is invalid.', 'solo-to-china' ), 422, 'page' );
	}

	$result = stc_cms_validate_schema_value( $page['metadata'], $page_schema['properties']['metadata'], 'page.metadata', 'INVALID_PAGE_SCHEMA' );
	if ( is_wp_error( $result ) ) {
		$data = $result->get_error_data();
		if ( isset( $data['path'] ) && false !== strpos( $data['path'], 'page.metadata.presentation' ) ) {
			return stc_cms_publish_error( 'INVALID_PRESENTATION', $result->get_error_message(), isset( $data['status'] ) ? $data['status'] : 422, $data['path'] );
		}
		return $result;
	}
	if ( ! is_array( $page['blocks'] ) || ! stc_cms_is_list( $page['blocks'] ) ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'page.blocks must be an ordered array.', 'solo-to-china' ), 422, 'page.blocks' );
	}
	if ( (string) $page['metadata']['contentType'] !== stc_sanitize_guide_type_meta( $page['metadata']['contentType'] ) ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The content type is not supported by the current Content Contract.', 'solo-to-china' ), 422, 'page.metadata.contentType' );
	}

	foreach ( $page['blocks'] as $index => $block ) {
		$result = stc_cms_validate_page_block( $block, $index );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}

	return true;
}

/**
 * Validate a complete CMS Publish Package before any WordPress write.
 *
 * @param mixed $package Decoded request body.
 * @return true|WP_Error
 */
function stc_validate_cms_publish_package( $package ) {
	$schema = stc_cms_load_json_artifact( stc_cms_publish_package_schema_path() );
	if ( ! $schema ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The Publish Package Schema is unavailable.', 'solo-to-china' ), 500 );
	}
	if ( ! is_array( $package ) || stc_cms_is_list( $package ) ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The Publish Package must be a JSON object.', 'solo-to-china' ), 400 );
	}

	$required = array( 'contract', 'page', 'seo', 'schema_jsonld', 'media', 'publication' );
	if ( array_diff( array_keys( $package ), $required ) || array_diff( $required, array_keys( $package ) ) ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The Publish Package has missing or unknown top-level fields.', 'solo-to-china' ), 422 );
	}

	$contract_result = stc_cms_validate_schema_value( $package['contract'], $schema['properties']['contract'], 'contract', 'CONTRACT_VERSION_MISMATCH' );
	if ( is_wp_error( $contract_result ) ) {
		return $contract_result;
	}
	if (
		STC_COMPONENT_REGISTRY_VERSION !== $package['contract']['componentContractVersion'] ||
		STC_COMPONENT_REGISTRY_VERSION !== $package['contract']['pageSchemaVersion'] ||
		! hash_equals( stc_cms_component_contract_checksum(), strtolower( $package['contract']['contractChecksum'] ) )
	) {
		return stc_cms_publish_error( 'CONTRACT_VERSION_MISMATCH', __( 'The Publish Package provenance does not match the deployed frontend contract.', 'solo-to-china' ), 409 );
	}

	$page_result = stc_validate_cms_page_payload( $package['page'] );
	if ( is_wp_error( $page_result ) ) {
		return $page_result;
	}

	if ( ! isset( $package['publication']['status'] ) || 'draft' !== $package['publication']['status'] ) {
		return stc_cms_publish_error( 'POST_NOT_DRAFT', __( 'CMS delivery is restricted to WordPress drafts.', 'solo-to-china' ), 409, 'publication.status' );
	}
	foreach ( array( 'seo', 'schema_jsonld', 'media', 'publication' ) as $field ) {
		$result = stc_cms_validate_schema_value( $package[ $field ], $schema['properties'][ $field ], $field, 'INVALID_PAGE_SCHEMA' );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
	}
	if ( strlen( wp_json_encode( $package['schema_jsonld'] ) ) > 131072 ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The JSON-LD payload is too large.', 'solo-to-china' ), 413, 'schema_jsonld' );
	}

	return true;
}

/**
 * Open a Gutenberg block comment with deterministic JSON attributes.
 *
 * @param string               $name Block name without wp: prefix.
 * @param array<string, mixed> $attributes Block attributes.
 * @return string
 */
function stc_cms_open_block( $name, $attributes = array() ) {
	$json = $attributes ? ' ' . wp_json_encode( $attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
	return '<!-- wp:' . $name . $json . ' -->';
}

/**
 * Close a Gutenberg block comment.
 *
 * @param string $name Block name without wp: prefix.
 * @return string
 */
function stc_cms_close_block( $name ) {
	return '<!-- /wp:' . $name . ' -->';
}

/**
 * Return a safe optional anchor for markup and Gutenberg attributes.
 *
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_component_anchor( $data ) {
	return empty( $data['anchor'] ) ? '' : sanitize_title( $data['anchor'] );
}

/**
 * Serialize a paragraph Gutenberg block.
 *
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_paragraph( $data ) {
	$anchor = stc_cms_component_anchor( $data );
	$attrs  = $anchor ? array( 'anchor' => $anchor ) : array();
	$id     = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	return stc_cms_open_block( 'paragraph', $attrs ) . '<p' . $id . '>' . $data['content'] . '</p>' . stc_cms_close_block( 'paragraph' );
}

/**
 * Serialize a Heading component.
 *
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_heading( $data ) {
	$level  = (int) $data['level'];
	$anchor = stc_cms_component_anchor( $data );
	$attrs  = array( 'level' => $level );
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$id = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	return stc_cms_open_block( 'heading', $attrs ) . '<h' . $level . $id . ' class="wp-block-heading">' . esc_html( $data['text'] ) . '</h' . $level . '>' . stc_cms_close_block( 'heading' );
}

/**
 * Serialize an ordered or unordered List component.
 *
 * @param array<string, mixed> $data Component data.
 * @param string               $variant Component variant.
 * @return string
 */
function stc_cms_serialize_list( $data, $variant ) {
	$ordered = 'ordered' === $variant;
	$anchor  = stc_cms_component_anchor( $data );
	$attrs   = $ordered ? array( 'ordered' => true ) : array();
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$tag   = $ordered ? 'ol' : 'ul';
	$id    = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	$items = '';
	foreach ( $data['items'] as $item ) {
		$items .= stc_cms_open_block( 'list-item' ) . '<li>' . esc_html( $item ) . '</li>' . stc_cms_close_block( 'list-item' );
	}
	return stc_cms_open_block( 'list', $attrs ) . '<' . $tag . $id . ' class="wp-block-list">' . $items . '</' . $tag . '>' . stc_cms_close_block( 'list' );
}

/**
 * Serialize a WordPress Media Library image as an editable core/image block.
 *
 * @param array<string, mixed> $data Component data.
 * @param string               $variant Component variant.
 * @return string|WP_Error
 */
function stc_cms_serialize_image( $data, $variant ) {
	$media_id = (int) $data['media_id'];
	if ( ! wp_attachment_is_image( $media_id ) ) {
		return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'The image component does not reference a valid WordPress image.', 'solo-to-china' ), 422, 'page.blocks[].data.media_id' );
	}
	$url = wp_get_attachment_url( $media_id );
	if ( ! $url ) {
		return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'The image URL is unavailable.', 'solo-to-china' ), 422, 'page.blocks[].data.media_id' );
	}

	$role    = empty( $data['role'] ) ? $variant : $data['role'];
	$anchor  = stc_cms_component_anchor( $data );
	$class   = 'stc-content-image stc-content-image--' . sanitize_html_class( $role );
	$attrs   = array( 'id' => $media_id, 'sizeSlug' => 'full', 'linkDestination' => 'none', 'className' => $class );
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$id      = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	$caption = empty( $data['caption'] ) ? '' : '<figcaption class="wp-element-caption">' . esc_html( $data['caption'] ) . '</figcaption>';
	return stc_cms_open_block( 'image', $attrs ) . '<figure' . $id . ' class="wp-block-image size-full ' . esc_attr( $class ) . '"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $data['alt'] ) . '" class="wp-image-' . $media_id . '"/>' . $caption . '</figure>' . stc_cms_close_block( 'image' );
}

/**
 * Wrap semantic child blocks in the Theme's established component group.
 *
 * @param string               $modifier Component class modifier.
 * @param string               $inner Gutenberg child blocks.
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_semantic_group( $modifier, $inner, $data ) {
	$anchor = stc_cms_component_anchor( $data );
	$class  = 'stc-content-block stc-content-block--' . sanitize_html_class( $modifier );
	$attrs  = array( 'className' => $class, 'layout' => array( 'type' => 'constrained' ) );
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$id = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	return stc_cms_open_block( 'group', $attrs ) . '<div' . $id . ' class="wp-block-group ' . esc_attr( $class ) . '">' . $inner . '</div>' . stc_cms_close_block( 'group' );
}

/**
 * Serialize a heading child block used inside semantic groups.
 *
 * @param string $text Heading text.
 * @param int    $level Heading level.
 * @return string
 */
function stc_cms_serialize_child_heading( $text, $level ) {
	$attrs = 2 === $level ? array() : array( 'level' => $level );
	return stc_cms_open_block( 'heading', $attrs ) . '<h' . $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . $level . '>' . stc_cms_close_block( 'heading' );
}

/**
 * Serialize a paragraph child block used inside semantic groups.
 *
 * @param string $content Validated inline HTML.
 * @return string
 */
function stc_cms_serialize_child_paragraph( $content ) {
	return stc_cms_open_block( 'paragraph' ) . '<p>' . $content . '</p>' . stc_cms_close_block( 'paragraph' );
}

/**
 * Serialize a simple semantic component with title and body.
 *
 * @param string               $component_id Component ID.
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_callout( $component_id, $data ) {
	$defaults = array(
		'quick_answer' => array( 'Quick answer', 2, 'answer' ),
		'tip'          => array( 'Solo traveler tip', 3, 'content' ),
		'warning'      => array( 'Before you continue', 3, 'content' ),
	);
	$config = $defaults[ $component_id ];
	$title  = empty( $data['title'] ) ? $config[0] : $data['title'];
	$inner  = stc_cms_serialize_child_heading( $title, $config[1] );
	$inner .= stc_cms_serialize_child_paragraph( $data[ $config[2] ] );
	return stc_cms_serialize_semantic_group( str_replace( '_', '-', $component_id ), $inner, $data );
}

/**
 * Serialize a semantic list component.
 *
 * @param string               $component_id Component ID.
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_semantic_list( $component_id, $data ) {
	$defaults = array(
		'key_takeaways' => array( 'Key takeaways', false ),
		'steps'         => array( 'Step by step', true ),
		'checklist'     => array( 'Checklist', false ),
	);
	$config  = $defaults[ $component_id ];
	$title   = empty( $data['title'] ) ? $config[0] : $data['title'];
	$ordered = $config[1];
	$items   = '';
	foreach ( $data['items'] as $item ) {
		$items .= stc_cms_open_block( 'list-item' ) . '<li>' . esc_html( $item ) . '</li>' . stc_cms_close_block( 'list-item' );
	}
	$attrs = $ordered ? array( 'ordered' => true ) : array();
	$tag   = $ordered ? 'ol' : 'ul';
	$inner = stc_cms_serialize_child_heading( $title, 2 );
	$inner .= stc_cms_open_block( 'list', $attrs ) . '<' . $tag . ' class="wp-block-list">' . $items . '</' . $tag . '>' . stc_cms_close_block( 'list' );
	return stc_cms_serialize_semantic_group( str_replace( '_', '-', $component_id ), $inner, $data );
}

/**
 * Serialize Quick Facts using the existing semantic group structure.
 *
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_quick_facts( $data ) {
	$title = empty( $data['title'] ) ? 'At a glance' : $data['title'];
	$facts = '';
	foreach ( $data['items'] as $item ) {
		$fact  = stc_cms_serialize_child_paragraph( '<strong>' . esc_html( $item['label'] ) . '</strong>' );
		$fact .= stc_cms_serialize_child_paragraph( esc_html( $item['value'] ) );
		$facts .= stc_cms_open_block( 'group', array( 'className' => 'stc-content-block__fact', 'layout' => array( 'type' => 'constrained' ) ) ) . '<div class="wp-block-group stc-content-block__fact">' . $fact . '</div>' . stc_cms_close_block( 'group' );
	}
	$grid  = stc_cms_open_block( 'group', array( 'className' => 'stc-content-block__facts', 'layout' => array( 'type' => 'grid', 'minimumColumnWidth' => '12rem' ) ) ) . '<div class="wp-block-group stc-content-block__facts">' . $facts . '</div>' . stc_cms_close_block( 'group' );
	$inner = stc_cms_serialize_child_heading( $title, 2 ) . $grid;
	return stc_cms_serialize_semantic_group( 'quick-facts', $inner, $data );
}

/**
 * Serialize the Comparison Table semantic component.
 *
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_comparison_table( $data ) {
	$caption = empty( $data['caption'] ) ? 'Compare options' : $data['caption'];
	$head    = '';
	foreach ( $data['columns'] as $column ) {
		$head .= '<th scope="col">' . esc_html( $column ) . '</th>';
	}
	$body = '';
	foreach ( $data['rows'] as $row ) {
		$cells = '';
		foreach ( $row as $index => $cell ) {
			$cells .= 0 === $index ? '<th scope="row">' . esc_html( $cell ) . '</th>' : '<td>' . esc_html( $cell ) . '</td>';
		}
		$body .= '<tr>' . $cells . '</tr>';
	}
	$table = stc_cms_open_block( 'table', array( 'className' => 'stc-content-block__table' ) ) . '<figure class="wp-block-table stc-content-block__table"><table class="has-fixed-layout"><thead><tr>' . $head . '</tr></thead><tbody>' . $body . '</tbody></table><figcaption class="wp-element-caption">' . esc_html( $caption ) . '</figcaption></figure>' . stc_cms_close_block( 'table' );
	$inner = stc_cms_serialize_child_heading( 'Compare options', 2 ) . $table;
	return stc_cms_serialize_semantic_group( 'comparison', $inner, $data );
}

/**
 * Serialize FAQ items as editable Details blocks.
 *
 * @param array<string, mixed> $data Component data.
 * @return string
 */
function stc_cms_serialize_faq( $data ) {
	$title = empty( $data['title'] ) ? 'Frequently asked questions' : $data['title'];
	$inner = stc_cms_serialize_child_heading( $title, 2 );
	foreach ( $data['items'] as $item ) {
		$answer = stc_cms_serialize_child_paragraph( $item['answer'] );
		$inner .= stc_cms_open_block( 'details' ) . '<details class="wp-block-details"><summary>' . esc_html( $item['question'] ) . '</summary>' . $answer . '</details>' . stc_cms_close_block( 'details' );
	}
	return stc_cms_serialize_semantic_group( 'faq', $inner, $data );
}

/**
 * Encode a dynamic component payload for a single editable Shortcode block.
 *
 * @param array<string, mixed> $value Component envelope.
 * @return string
 */
function stc_cms_base64url_encode( $value ) {
	return rtrim( strtr( base64_encode( wp_json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ), '+/', '-_' ), '=' );
}

/**
 * Decode a dynamic component payload.
 *
 * @param string $encoded URL-safe base64 encoded JSON.
 * @return array<string, mixed>
 */
function stc_cms_base64url_decode( $encoded ) {
	if ( ! is_string( $encoded ) || ! preg_match( '/^[A-Za-z0-9_-]+$/', $encoded ) ) {
		return array();
	}
	$padding = strlen( $encoded ) % 4;
	if ( $padding ) {
		$encoded .= str_repeat( '=', 4 - $padding );
	}
	$raw     = base64_decode( strtr( $encoded, '-_', '+/' ), true );
	$decoded = false === $raw ? null : json_decode( $raw, true );
	return is_array( $decoded ) ? $decoded : array();
}

/**
 * Render a CMS-created dynamic component through its existing Theme renderer.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_cms_component_shortcode( $attributes ) {
	$attributes = shortcode_atts( array( 'payload' => '' ), (array) $attributes, 'stc_cms_component' );
	$block      = stc_cms_base64url_decode( $attributes['payload'] );
	if ( ! $block || is_wp_error( stc_cms_validate_page_block( $block, 0 ) ) ) {
		return '';
	}

	$definition = stc_get_component_definition( $block['type'] );
	$callback   = 'stc_render_' . $block['type'] . '_component';
	if ( ! $definition || 'shortcode' !== $definition['render_mode'] || ! is_callable( $callback ) ) {
		return '';
	}

	return (string) call_user_func( $callback, $block['data'] );
}
add_shortcode( 'stc_cms_component', 'stc_render_cms_component_shortcode' );

/**
 * Serialize one validated component according to its Registry render mode.
 *
 * @param array<string, mixed> $block Validated component envelope.
 * @return string|WP_Error
 */
function stc_serialize_cms_component_to_post_content( $block ) {
	$definition = stc_get_component_definition( $block['type'] );
	$data       = $block['data'];
	$variant    = $block['variant'];
	$mode       = isset( $definition['render_mode'] ) ? $definition['render_mode'] : '';

	if ( 'shortcode' === $mode ) {
		$shortcode = '[stc_cms_component payload="' . stc_cms_base64url_encode( $block ) . '"]';
		return stc_cms_open_block( 'shortcode' ) . $shortcode . stc_cms_close_block( 'shortcode' );
	}
	if ( 'gutenberg_core' === $mode ) {
		switch ( $block['type'] ) {
			case 'paragraph':
				return stc_cms_serialize_paragraph( $data );
			case 'heading':
				return stc_cms_serialize_heading( $data );
			case 'list':
				return stc_cms_serialize_list( $data, $variant );
			case 'image':
				return stc_cms_serialize_image( $data, $variant );
		}
	}
	if ( 'semantic_group' === $mode ) {
		if ( in_array( $block['type'], array( 'quick_answer', 'tip', 'warning' ), true ) ) {
			return stc_cms_serialize_callout( $block['type'], $data );
		}
		if ( in_array( $block['type'], array( 'key_takeaways', 'steps', 'checklist' ), true ) ) {
			return stc_cms_serialize_semantic_list( $block['type'], $data );
		}
		if ( 'quick_facts' === $block['type'] ) {
			return stc_cms_serialize_quick_facts( $data );
		}
		if ( 'comparison_table' === $block['type'] ) {
			return stc_cms_serialize_comparison_table( $data );
		}
		if ( 'faq' === $block['type'] ) {
			return stc_cms_serialize_faq( $data );
		}
	}

	return stc_cms_publish_error( 'UNKNOWN_COMPONENT', __( 'The component has no WordPress serializer strategy.', 'solo-to-china' ), 422, 'page.blocks[].type' );
}

/**
 * Serialize page.blocks[] in exact editorial order to editable post_content.
 *
 * @param array<string, mixed> $page_payload Validated Page Payload.
 * @return string|WP_Error
 */
function stc_serialize_cms_page_to_post_content( $page_payload ) {
	$result = stc_validate_cms_page_payload( $page_payload );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$serialized = array();
	foreach ( $page_payload['blocks'] as $block ) {
		$content = stc_serialize_cms_component_to_post_content( $block );
		if ( is_wp_error( $content ) ) {
			return $content;
		}
		$serialized[] = $content;
	}

	return implode( "\n\n", $serialized );
}

/**
 * Sanitize scalar metadata stored by the CMS adapter.
 *
 * @param mixed $value Metadata value.
 * @return string
 */
function stc_cms_sanitize_text_meta( $value ) {
	return sanitize_text_field( (string) $value );
}

/**
 * Sanitize a JSON metadata snapshot without accepting invalid JSON.
 *
 * @param mixed $value JSON string.
 * @return string
 */
function stc_cms_sanitize_json_meta( $value ) {
	$value   = (string) $value;
	$decoded = json_decode( $value, true );
	return JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ? wp_json_encode( $decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
}

/**
 * Register private provenance, SEO, GEO, and idempotency metadata.
 */
function stc_register_cms_article_meta() {
	$text_keys = array(
		'_stc_cms_draft_id',
		'_stc_cms_page_id',
		'_stc_component_contract_version',
		'_stc_page_schema_version',
		'_stc_contract_checksum',
		'_stc_seo_title',
		'_stc_seo_description',
		'_stc_focus_keyword',
		'_stc_search_intent',
		'_stc_strategy_version',
		'_stc_canonical_url',
		'_stc_robots',
		'_stc_cms_synced_post_modified_gmt',
	);
	foreach ( $text_keys as $meta_key ) {
		register_post_meta(
			'post',
			$meta_key,
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'stc_cms_sanitize_text_meta',
				'auth_callback'     => 'stc_content_meta_auth_callback',
				'show_in_rest'      => false,
			)
		);
	}

	foreach ( array( '_stc_page_payload', '_stc_schema_jsonld', '_stc_secondary_keywords', '_stc_media_manifest' ) as $meta_key ) {
		register_post_meta(
			'post',
			$meta_key,
			array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'stc_cms_sanitize_json_meta',
				'auth_callback'     => 'stc_content_meta_auth_callback',
				'show_in_rest'      => false,
			)
		);
	}
}
add_action( 'init', 'stc_register_cms_article_meta' );

/**
 * Find an existing CMS-managed post using stable CMS identifiers.
 *
 * @param mixed $page_id Stable CMS page ID.
 * @param mixed $draft_id Optional CMS draft ID.
 * @return int|WP_Error
 */
function stc_cms_find_mapped_post_id( $page_id, $draft_id = '' ) {
	$clauses = array( 'relation' => 'OR' );
	$clauses[] = array( 'key' => '_stc_cms_page_id', 'value' => (string) $page_id, 'compare' => '=' );
	if ( '' !== (string) $draft_id ) {
		$clauses[] = array( 'key' => '_stc_cms_draft_id', 'value' => (string) $draft_id, 'compare' => '=' );
	}
	$posts = get_posts(
		array(
			'post_type'        => 'post',
			'post_status'      => 'any',
			'posts_per_page'   => 2,
			'fields'           => 'ids',
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'cache_results'    => false,
			'suppress_filters' => true,
			'meta_query'       => $clauses,
		)
	);
	if ( count( $posts ) > 1 ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'CMS identifiers resolve to more than one WordPress post.', 'solo-to-china' ), 409 );
	}

	return $posts ? (int) $posts[0] : 0;
}

/**
 * Resolve the explicit or idempotent update target.
 *
 * @param array<string, mixed> $package Publish Package.
 * @param int                  $route_post_id Optional PUT route post ID.
 * @return int|WP_Error
 */
function stc_cms_resolve_target_post_id( $package, $route_post_id = 0 ) {
	$publication = $package['publication'];
	$explicit_id = ! empty( $publication['existing_post_id'] ) ? (int) $publication['existing_post_id'] : 0;
	if ( $route_post_id && $explicit_id && $route_post_id !== $explicit_id ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'Route and package post identifiers disagree.', 'solo-to-china' ), 409 );
	}
	$target_id = $route_post_id ? $route_post_id : $explicit_id;
	if ( ! $target_id ) {
		$target_id = stc_cms_find_mapped_post_id(
			$package['page']['metadata']['pageId'],
			isset( $publication['cms_draft_id'] ) ? $publication['cms_draft_id'] : ''
		);
		if ( is_wp_error( $target_id ) ) {
			return $target_id;
		}
	}
	if ( ! $target_id ) {
		return 0;
	}

	$post = get_post( $target_id );
	if ( ! $post || 'post' !== $post->post_type ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The requested WordPress post does not exist.', 'solo-to-china' ), 404 );
	}
	if ( 'draft' !== $post->post_status ) {
		return stc_cms_publish_error( 'POST_NOT_DRAFT', __( 'CMS delivery cannot overwrite a non-draft post.', 'solo-to-china' ), 409 );
	}
	if ( ! current_user_can( 'edit_post', $target_id ) ) {
		return stc_cms_publish_error( 'rest_forbidden', __( 'You cannot edit this draft.', 'solo-to-china' ), 403 );
	}
	$stored_page_id = get_post_meta( $target_id, '_stc_cms_page_id', true );
	if ( '' !== $stored_page_id && (string) $stored_page_id !== (string) $package['page']['metadata']['pageId'] ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The draft belongs to a different CMS page.', 'solo-to-china' ), 409 );
	}

	return $target_id;
}

/**
 * Validate WordPress-specific media and taxonomy references before any write.
 *
 * @param array<string, mixed> $package Validated Publish Package.
 * @return true|WP_Error
 */
function stc_cms_validate_wordpress_references( $package ) {
	$metadata = $package['page']['metadata'];
	if ( ! empty( $metadata['featuredMediaId'] ) && ! wp_attachment_is_image( (int) $metadata['featuredMediaId'] ) ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'featuredMediaId is not a WordPress image.', 'solo-to-china' ), 422, 'page.metadata.featuredMediaId' );
	}
	foreach ( $package['page']['blocks'] as $index => $block ) {
		if ( 'image' === $block['type'] && ! wp_attachment_is_image( (int) $block['data']['media_id'] ) ) {
			return stc_cms_publish_error( 'INVALID_COMPONENT_DATA', __( 'An image block does not reference a WordPress image.', 'solo-to-china' ), 422, 'page.blocks[' . $index . '].data.media_id' );
		}
	}
	foreach ( $package['media'] as $index => $media ) {
		if ( ! wp_attachment_is_image( (int) $media['media_id'] ) ) {
			return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The media manifest contains an unavailable WordPress image.', 'solo-to-china' ), 422, 'media[' . $index . '].media_id' );
		}
	}
	if ( ! empty( $metadata['taxonomy'] ) ) {
		foreach ( array_keys( $metadata['taxonomy'] ) as $taxonomy ) {
			$taxonomy = sanitize_key( $taxonomy );
			$taxonomy = 'categories' === $taxonomy ? 'category' : ( 'tags' === $taxonomy ? 'post_tag' : $taxonomy );
			$object   = get_taxonomy( $taxonomy );
			if ( ! $object || ! in_array( 'post', $object->object_type, true ) ) {
				return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The requested taxonomy is not available for posts.', 'solo-to-china' ), 422, 'page.metadata.taxonomy.' . $taxonomy );
			}
		}
	}

	return true;
}

/**
 * Apply semantic content type and Page Payload taxonomies.
 *
 * @param int                  $post_id WordPress post ID.
 * @param array<string, mixed> $metadata Page metadata.
 * @return true|WP_Error
 */
function stc_cms_apply_taxonomy( $post_id, $metadata ) {
	$contract      = stc_get_content_contract();
	$content_type  = $metadata['contentType'];
	$category_slug = isset( $contract['guide_types'][ $content_type ]['category_slug'] ) ? $contract['guide_types'][ $content_type ]['category_slug'] : '';
	if ( $category_slug ) {
		$term = get_term_by( 'slug', $category_slug, 'category' );
		if ( ! $term ) {
			$created = wp_insert_term( ucwords( str_replace( '-', ' ', $category_slug ) ), 'category', array( 'slug' => $category_slug ) );
			if ( is_wp_error( $created ) ) {
				return $created;
			}
			$term = get_term( $created['term_id'], 'category' );
		}
		wp_set_post_categories( $post_id, array( (int) $term->term_id ), false );
	}

	if ( empty( $metadata['taxonomy'] ) ) {
		return true;
	}
	foreach ( $metadata['taxonomy'] as $taxonomy => $terms ) {
		$taxonomy = sanitize_key( $taxonomy );
		if ( 'categories' === $taxonomy ) {
			$taxonomy = 'category';
		} elseif ( 'tags' === $taxonomy ) {
			$taxonomy = 'post_tag';
		}
		$taxonomy_object = get_taxonomy( $taxonomy );
		if ( ! $taxonomy_object || ! in_array( 'post', $taxonomy_object->object_type, true ) ) {
			return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The requested taxonomy is not available for posts.', 'solo-to-china' ), 422, 'page.metadata.taxonomy.' . $taxonomy );
		}
		$assigned = wp_set_object_terms( $post_id, array_map( 'sanitize_text_field', $terms ), $taxonomy, 'category' === $taxonomy && (bool) $category_slug );
		if ( is_wp_error( $assigned ) ) {
			return $assigned;
		}
	}

	return true;
}

/**
 * Store all adapter-owned Page, provenance, SEO/GEO, and media metadata.
 *
 * @param int                  $post_id WordPress post ID.
 * @param array<string, mixed> $package Validated Publish Package.
 */
function stc_cms_store_publish_metadata( $post_id, $package ) {
	$page         = $package['page'];
	$metadata     = $page['metadata'];
	$presentation = isset( $metadata['presentation'] ) ? $metadata['presentation'] : array();
	$hero         = isset( $presentation['article_hero']['variant'] ) ? $presentation['article_hero']['variant'] : 'default';
	$seo          = $package['seo'];
	$page_seo     = isset( $metadata['seo'] ) ? $metadata['seo'] : array();

	update_post_meta( $post_id, '_stc_guide_type', $metadata['contentType'] );
	update_post_meta( $post_id, '_stc_content_contract_version', STC_CONTENT_CONTRACT_VERSION );
	update_post_meta( $post_id, '_stc_show_share', ! empty( $presentation['share_this_page'] ) );
	update_post_meta( $post_id, '_stc_show_toc', ! empty( $presentation['table_of_contents'] ) );
	update_post_meta( $post_id, '_stc_hero_variant', $hero );
	update_post_meta( $post_id, '_stc_cms_page_id', (string) $metadata['pageId'] );
	update_post_meta( $post_id, '_stc_cms_draft_id', isset( $package['publication']['cms_draft_id'] ) ? (string) $package['publication']['cms_draft_id'] : '' );
	update_post_meta( $post_id, '_stc_page_payload', wp_json_encode( $page, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	update_post_meta( $post_id, '_stc_component_contract_version', $package['contract']['componentContractVersion'] );
	update_post_meta( $post_id, '_stc_page_schema_version', $package['contract']['pageSchemaVersion'] );
	update_post_meta( $post_id, '_stc_contract_checksum', strtolower( $package['contract']['contractChecksum'] ) );
	update_post_meta( $post_id, '_stc_seo_title', $seo['meta_title'] );
	update_post_meta( $post_id, '_stc_seo_description', $seo['meta_description'] );
	update_post_meta( $post_id, '_stc_focus_keyword', isset( $seo['focus_keyword'] ) ? $seo['focus_keyword'] : '' );
	update_post_meta( $post_id, '_stc_secondary_keywords', wp_json_encode( isset( $seo['secondary_keywords'] ) ? $seo['secondary_keywords'] : array() ) );
	update_post_meta( $post_id, '_stc_search_intent', isset( $seo['search_intent'] ) ? $seo['search_intent'] : '' );
	update_post_meta( $post_id, '_stc_strategy_version', isset( $seo['strategy_version'] ) ? $seo['strategy_version'] : '' );
	$final_schema = stc_cms_finalize_schema( $package['schema_jsonld'], $post_id, $page );
	update_post_meta( $post_id, '_stc_schema_jsonld', wp_json_encode( $final_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	update_post_meta( $post_id, '_stc_media_manifest', wp_json_encode( $package['media'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	update_post_meta( $post_id, '_stc_canonical_url', get_permalink( $post_id ) );
	update_post_meta( $post_id, '_stc_robots', isset( $page_seo['robots'] ) ? $page_seo['robots'] : '' );
	update_post_meta( $post_id, '_stc_cms_synced_post_modified_gmt', (string) get_post_field( 'post_modified_gmt', $post_id ) );
}

/**
 * Bind generated schema to the WordPress title, permalink and modification time.
 *
 * @param array<string, mixed> $schema JSON-LD graph.
 * @param int                  $post_id WordPress post ID.
 * @param array<string, mixed> $page Final visible Page Payload.
 * @return array<string, mixed>
 */
function stc_cms_finalize_schema( $schema, $post_id, $page ) {
	if ( ! is_array( $schema ) ) {
		return array();
	}
	$permalink = get_permalink( $post_id );
	$title     = get_the_title( $post_id );
	$modified  = get_post_modified_time( DATE_W3C, true, $post_id );
	if ( empty( $schema['@graph'] ) || ! is_array( $schema['@graph'] ) ) {
		return $schema;
	}
	foreach ( $schema['@graph'] as &$node ) {
		$type = isset( $node['@type'] ) ? $node['@type'] : '';
		if ( 'WebPage' === $type ) {
			$node['@id'] = $permalink;
			$node['name'] = $title;
		}
		if ( 'Article' === $type ) {
			$node['headline']     = $title;
			$node['dateModified'] = $modified;
			$node['mainEntityOfPage'] = array( '@type' => 'WebPage', '@id' => $permalink );
		}
		if ( 'BreadcrumbList' === $type && ! empty( $node['itemListElement'] ) ) {
			$last = count( $node['itemListElement'] ) - 1;
			$node['itemListElement'][ $last ]['item'] = $permalink;
		}
	}
	unset( $node );
	return $schema;
}

/**
 * Serialize first-write resolution for one CMS identity across PHP workers.
 *
 * @param array<string, mixed> $package Validated Publish Package.
 * @param int                  $route_post_id Optional PUT route post ID.
 * @return WP_REST_Response|WP_Error
 */
function stc_cms_upsert_article( $package, $route_post_id = 0 ) {
	$draft_id = isset( $package['publication']['cms_draft_id'] ) ? (string) $package['publication']['cms_draft_id'] : '';
	$lock     = stc_cms_acquire_write_lock( (string) $package['page']['metadata']['pageId'] . '|' . $draft_id, 10 );
	if ( ! $lock ) {
		return stc_cms_publish_error( 'CMS_WRITE_BUSY', __( 'Another delivery for this CMS page is still in progress. Retry this request.', 'solo-to-china' ), 409 );
	}
	try {
		return stc_cms_upsert_article_locked( $package, $route_post_id );
	} finally {
		stc_cms_release_write_lock( $lock );
	}
}

/**
 * Acquire a portable cross-worker lock through the unique options index.
 *
 * @param string $identity Stable CMS page/draft identity.
 * @param int    $timeout_seconds Maximum wait.
 * @return array<string, string>|false
 */
function stc_cms_acquire_write_lock( $identity, $timeout_seconds = 10 ) {
	global $wpdb;
	$key      = '_stc_cms_lock_' . substr( hash( 'sha256', $identity ), 0, 48 );
	$token    = wp_generate_uuid4();
	$deadline = microtime( true ) + max( 1, (int) $timeout_seconds );
	do {
		$value = wp_json_encode(
			array(
				'token'      => $token,
				'expires_at' => time() + 30,
			)
		);
		if ( add_option( $key, $value, '', false ) ) {
			return array( 'key' => $key, 'value' => $value );
		}
		$current_value = (string) get_option( $key, '' );
		$current       = json_decode( $current_value, true );
		if ( is_array( $current ) && isset( $current['expires_at'] ) && (int) $current['expires_at'] < time() ) {
			stc_cms_delete_write_lock_value( $key, $current_value );
		}
		usleep( 100000 );
	} while ( microtime( true ) < $deadline );
	return false;
}

/**
 * Release only the lock value owned by this request.
 *
 * @param array<string, string> $lock Acquired lock identity.
 */
function stc_cms_release_write_lock( $lock ) {
	if ( empty( $lock['key'] ) || empty( $lock['value'] ) ) {
		return;
	}
	stc_cms_delete_write_lock_value( $lock['key'], $lock['value'] );
}

/**
 * Conditionally delete one lock value and invalidate WordPress option caches.
 *
 * @param string $key Option name.
 * @param string $value Exact owner value.
 * @return bool
 */
function stc_cms_delete_write_lock_value( $key, $value ) {
	global $wpdb;
	$deleted = $wpdb->delete( $wpdb->options, array( 'option_name' => $key, 'option_value' => $value ), array( '%s', '%s' ) );
	if ( $deleted ) {
		wp_cache_delete( $key, 'options' );
		wp_cache_delete( 'notoptions', 'options' );
	}
	return (bool) $deleted;
}

/**
 * Create or update a CMS-managed WordPress draft while holding its identity lock.
 *
 * @param array<string, mixed> $package Validated Publish Package.
 * @param int                  $route_post_id Optional PUT route post ID.
 * @return WP_REST_Response|WP_Error
 */
function stc_cms_upsert_article_locked( $package, $route_post_id = 0 ) {
	$references = stc_cms_validate_wordpress_references( $package );
	if ( is_wp_error( $references ) ) {
		return $references;
	}
	$content = stc_serialize_cms_page_to_post_content( $package['page'] );
	if ( is_wp_error( $content ) ) {
		return $content;
	}
	$target_id = stc_cms_resolve_target_post_id( $package, $route_post_id );
	if ( is_wp_error( $target_id ) ) {
		return $target_id;
	}

	$metadata = $package['page']['metadata'];
	$postarr  = array(
		'post_type'    => 'post',
		'post_status'  => 'draft',
		'post_title'   => sanitize_text_field( $metadata['title'] ),
		'post_name'    => sanitize_title( $metadata['slug'] ),
		'post_excerpt' => isset( $metadata['excerpt'] ) ? sanitize_textarea_field( $metadata['excerpt'] ) : '',
		'post_content' => $content,
	);
	$updated = (bool) $target_id;
	if ( $updated ) {
		$postarr['ID'] = $target_id;
		$post_id       = wp_update_post( wp_slash( $postarr ), true );
	} else {
		$postarr['post_author'] = get_current_user_id();
		$post_id                = wp_insert_post( wp_slash( $postarr ), true );
	}
	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}
	// Persist the stable identity immediately. If taxonomy, thumbnail, or later
	// metadata work fails, a retry resolves this exact draft instead of creating
	// a second post.
	update_post_meta( $post_id, '_stc_cms_page_id', (string) $metadata['pageId'] );
	update_post_meta( $post_id, '_stc_cms_draft_id', isset( $package['publication']['cms_draft_id'] ) ? (string) $package['publication']['cms_draft_id'] : '' );

	$taxonomy_result = stc_cms_apply_taxonomy( $post_id, $metadata );
	if ( is_wp_error( $taxonomy_result ) ) {
		return $taxonomy_result;
	}
	if ( ! empty( $metadata['featuredMediaId'] ) ) {
		if ( ! wp_attachment_is_image( (int) $metadata['featuredMediaId'] ) ) {
			return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'featuredMediaId is not a WordPress image.', 'solo-to-china' ), 422, 'page.metadata.featuredMediaId' );
		}
		set_post_thumbnail( $post_id, (int) $metadata['featuredMediaId'] );
	} else {
		delete_post_thumbnail( $post_id );
	}
	stc_cms_store_publish_metadata( $post_id, $package );

	$post = get_post( $post_id );
	return new WP_REST_Response(
		array(
			'post_id'          => (int) $post_id,
			'status'           => $post->post_status,
			'edit_url'         => get_edit_post_link( $post_id, 'raw' ),
			'preview_url'      => get_preview_post_link( $post ),
			'slug'             => $post->post_name,
			'contract_version' => STC_COMPONENT_REGISTRY_VERSION,
			'updated'          => $updated,
		),
		$updated ? 200 : 201
	);
}

/**
 * Handle authenticated POST/PUT CMS Article requests.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_upsert_cms_article( $request ) {
	if ( strlen( (string) $request->get_body() ) > STC_CMS_PUBLISH_MAX_BYTES ) {
		return stc_cms_publish_error( 'INVALID_PAGE_SCHEMA', __( 'The Publish Package exceeds the 1 MiB limit.', 'solo-to-china' ), 413 );
	}
	$package = $request->get_json_params();
	$result  = stc_validate_cms_publish_package( $package );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	$route_post_id = (int) $request->get_param( 'post_id' );
	return stc_cms_upsert_article( $package, $route_post_id );
}

/**
 * Require post editing capabilities; Application Passwords authenticate upstream.
 *
 * @param WP_REST_Request $request Request object.
 * @return bool|WP_Error
 */
function stc_cms_articles_permission( $request ) {
	$post_id = (int) $request->get_param( 'post_id' );
	$allowed = $post_id ? current_user_can( 'edit_post', $post_id ) : current_user_can( 'edit_posts' );
	return $allowed ? true : stc_cms_publish_error( 'rest_forbidden', __( 'An authenticated editor is required.', 'solo-to-china' ), 403 );
}

/**
 * Serve the public read-only Publish Package Schema.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function stc_rest_get_cms_publish_package_schema( $request ) {
	return stc_rest_get_generated_artifact( $request, stc_cms_publish_package_schema_path(), 'stc_cms_publish_package_schema_unavailable' );
}

/**
 * Register the dedicated CMS Article API without changing public Contract routes.
 */
function stc_register_cms_article_routes() {
	register_rest_route(
		'stc/v1',
		'/cms-publish-package-schema',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'stc_rest_get_cms_publish_package_schema',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'stc/v1',
		'/cms-articles',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'stc_rest_upsert_cms_article',
			'permission_callback' => 'stc_cms_articles_permission',
		)
	);
	register_rest_route(
		'stc/v1',
		'/cms-articles/(?P<post_id>[1-9][0-9]*)',
		array(
			'methods'             => 'PUT',
			'callback'            => 'stc_rest_upsert_cms_article',
			'permission_callback' => 'stc_cms_articles_permission',
		)
	);
}
add_action( 'rest_api_init', 'stc_register_cms_article_routes' );

/**
 * Build JSON-LD markup from structured stored data.
 *
 * @param int $post_id WordPress post ID.
 * @return string
 */
function stc_get_cms_jsonld_markup( $post_id ) {
	if ( stc_cms_seo_plugin_active() ) {
		return '';
	}
	$synced_modified = (string) get_post_meta( $post_id, '_stc_cms_synced_post_modified_gmt', true );
	$current_modified = (string) get_post_field( 'post_modified_gmt', $post_id );
	if ( $synced_modified && $current_modified && $synced_modified !== $current_modified ) {
		return '';
	}
	$decoded = json_decode( (string) get_post_meta( $post_id, '_stc_schema_jsonld', true ), true );
	if ( ! is_array( $decoded ) || ! $decoded ) {
		return '';
	}
	$json = wp_json_encode( $decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP );
	return $json ? '<script type="application/ld+json">' . $json . '</script>' . "\n" : '';
}

/**
 * Output validated structured JSON-LD without accepting executable markup.
 */
function stc_output_cms_jsonld() {
	if ( is_singular( 'post' ) ) {
		echo stc_get_cms_jsonld_markup( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON_HEX_TAG prevents script termination.
	}
}
add_action( 'wp_head', 'stc_output_cms_jsonld', 20 );

/**
 * Output the adapter-owned SEO description until an SEO plugin mapper is selected.
 */
function stc_output_cms_meta_description() {
	if ( ! is_singular( 'post' ) || stc_cms_seo_plugin_active() ) {
		return;
	}
	$description = get_post_meta( get_the_ID(), '_stc_seo_description', true );
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'stc_output_cms_meta_description', 19 );

/**
 * Detect SEO plugins that own canonical, social and structured metadata output.
 *
 * @return bool
 */
function stc_cms_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' );
}

/**
 * Output social metadata for CMS-managed posts when no SEO plugin owns it.
 */
function stc_output_cms_social_meta() {
	if ( ! is_singular( 'post' ) || stc_cms_seo_plugin_active() ) {
		return;
	}
	$post_id     = get_the_ID();
	$title       = get_post_meta( $post_id, '_stc_seo_title', true );
	$description = get_post_meta( $post_id, '_stc_seo_description', true );
	$canonical   = get_permalink( $post_id );
	$image       = get_the_post_thumbnail_url( $post_id, 'full' );
	echo '<meta property="og:type" content="article">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ? $title : get_the_title( $post_id ) ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
	echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'stc_output_cms_social_meta', 19 );

/**
 * Apply the stored robots policy through WordPress core's single robots tag.
 *
 * @param array<string, bool|string> $robots Existing directives.
 * @return array<string, bool|string>
 */
function stc_filter_cms_robots( $robots ) {
	if ( ! is_singular( 'post' ) || stc_cms_seo_plugin_active() ) {
		return $robots;
	}
	$value = strtolower( (string) get_post_meta( get_the_ID(), '_stc_robots', true ) );
	if ( false !== strpos( $value, 'noindex' ) ) {
		$robots['noindex'] = true;
	} elseif ( false !== strpos( $value, 'index' ) ) {
		$robots['index'] = true;
	}
	if ( false !== strpos( $value, 'nofollow' ) ) {
		$robots['nofollow'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'stc_filter_cms_robots' );

/**
 * Apply the canonical STC SEO title to WordPress document title output.
 *
 * @param array<string, string> $parts Existing title parts.
 * @return array<string, string>
 */
function stc_filter_cms_document_title( $parts ) {
	if ( is_singular( 'post' ) ) {
		$title = get_post_meta( get_the_ID(), '_stc_seo_title', true );
		if ( $title ) {
			$parts['title'] = $title;
		}
	}
	return $parts;
}
add_filter( 'document_title_parts', 'stc_filter_cms_document_title' );
