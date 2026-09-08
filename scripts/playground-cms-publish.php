<?php
/**
 * Real WordPress runtime verification for the CMS Publish Package adapter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function stc_playground_cms_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function stc_playground_cms_clone( $value ) {
	return json_decode( wp_json_encode( $value ), true );
}

function stc_playground_cms_request( $method, $route, $package ) {
	$request = new WP_REST_Request( $method, $route );
	$request->set_header( 'content-type', 'application/json' );
	$request->set_body( wp_json_encode( $package ) );
	return rest_do_request( $request );
}

function stc_playground_cms_expect_error( $package, $code, $label ) {
	$response = stc_playground_cms_request( 'POST', '/stc/v1/cms-articles', $package );
	$data     = $response->get_data();
	stc_playground_cms_assert( $response->get_status() >= 400 && isset( $data['code'] ) && $code === $data['code'], $label . ' did not return ' . $code );
}

function stc_playground_cms_block_index( $blocks, $type ) {
	foreach ( $blocks as $index => $block ) {
		if ( $type === $block['type'] ) {
			return $index;
		}
	}
	return -1;
}

function stc_playground_verify_cms_publish_path() {
	wp_set_current_user( 1 );
	$media_id = stc_playground_install_media_fixture();
	stc_playground_cms_assert( $media_id > 0, 'CMS adapter fixture image is unavailable.' );

	$blocks = array();
	foreach ( stc_get_cms_component_definitions( 'page_block' ) as $definition ) {
		$example = $definition['example'];
		$variant = isset( $example['variant'] ) ? $example['variant'] : $definition['variants'][0];
		unset( $example['type'], $example['variant'] );
		if ( 'image' === $definition['id'] ) {
			$example['media_id'] = $media_id;
			$example['role']     = $variant;
		}
		$blocks[] = array( 'type' => $definition['id'], 'variant' => $variant, 'data' => $example );
	}
	stc_playground_cms_assert( 21 === count( $blocks ), 'CMS adapter did not resolve all 21 page-block capabilities.' );

	$package = array(
		'contract'      => array(
			'componentContractVersion' => STC_COMPONENT_REGISTRY_VERSION,
			'pageSchemaVersion'         => STC_COMPONENT_REGISTRY_VERSION,
			'contractChecksum'          => stc_cms_component_contract_checksum(),
		),
		'page'          => array(
			'metadata' => array(
				'pageId'          => 'runtime-page-001',
				'title'           => 'CMS Runtime Draft',
				'slug'            => 'cms-runtime-draft',
				'contentType'     => 'attraction-guide',
				'excerpt'         => 'A draft created through the Contract-aware CMS Article API.',
				'featuredMediaId' => $media_id,
				'presentation'    => array(
					'article_hero'          => array( 'variant' => 'attraction' ),
					'share_this_page'       => true,
					'table_of_contents'     => true,
				),
			),
			'blocks'   => $blocks,
		),
		'seo'           => array(
			'meta_title'        => 'CMS Runtime Draft SEO',
			'meta_description'  => 'Runtime verification for the CMS WordPress delivery path.',
			'focus_keyword'      => 'China travel draft',
			'secondary_keywords' => array( 'WordPress', 'CMS' ),
			'search_intent'      => 'informational',
			'strategy_version'   => 'runtime-v1',
		),
		'schema_jsonld' => array(
			'@context' => 'https://schema.org',
			'@type'    => 'Article',
			'headline' => 'CMS Runtime Draft',
		),
		'media'         => array(
			array(
				'media_id'  => $media_id,
				'url'       => wp_get_attachment_url( $media_id ),
				'alt'       => 'Visitors approaching the Forbidden City in Beijing',
				'caption'   => 'Runtime media fixture',
				'role'      => 'featured',
				'placement' => 'article_hero',
			),
		),
		'publication'   => array( 'status' => 'draft', 'existing_post_id' => null, 'cms_draft_id' => 'runtime-draft-001' ),
	);

	$response = stc_playground_cms_request( 'POST', '/stc/v1/cms-articles', $package );
	$data     = $response->get_data();
	stc_playground_cms_assert( 201 === $response->get_status(), 'Valid Publish Package did not create a draft. HTTP ' . $response->get_status() . ': ' . wp_json_encode( $data ) );
	stc_playground_cms_assert( ! empty( $data['post_id'] ) && 'draft' === $data['status'] && false === $data['updated'], 'Create response shape is invalid.' );
	stc_playground_cms_assert( ! empty( $data['edit_url'] ) && ! empty( $data['preview_url'] ), 'Create response omitted edit or preview URL.' );
	$post_id = (int) $data['post_id'];
	$post    = get_post( $post_id );
	stc_playground_cms_assert( 'draft' === $post->post_status && 'CMS Runtime Draft' === $post->post_title, 'Created post is not the expected draft.' );
	$parsed_blocks = array_values( array_filter( parse_blocks( $post->post_content ), static function ( $parsed ) { return ! empty( $parsed['blockName'] ); } ) );
	stc_playground_cms_assert( count( $parsed_blocks ) === count( $blocks ), 'Serialized Gutenberg block count does not preserve Page Payload order.' );
	stc_playground_cms_assert( '_stc_cms_component' !== substr( $post->post_content, 0, 18 ), 'Page Payload was stored as one opaque runtime blob.' );
	stc_playground_cms_assert( false !== strpos( $post->post_content, '<!-- wp:paragraph' ) && false !== strpos( $post->post_content, '<!-- wp:group' ) && false !== strpos( $post->post_content, '<!-- wp:shortcode -->[stc_cms_component' ), 'Expected Gutenberg and dynamic representations are missing.' );
	stc_playground_cms_assert( false !== strpos( $post->post_content, '<table class="has-fixed-layout">' ) && false !== strpos( $post->post_content, '<figcaption class="wp-element-caption">Transport comparison</figcaption>' ), 'Comparison Table is not serialized in Gutenberg-compatible form.' );

	$snapshot = json_decode( get_post_meta( $post_id, '_stc_page_payload', true ), true );
	stc_playground_cms_assert( $snapshot['blocks'] === $blocks, 'Stored Page Payload provenance changed block order or data.' );
	stc_playground_cms_assert( 'attraction-guide' === get_post_meta( $post_id, '_stc_guide_type', true ), 'contentType did not map to guide meta.' );
	stc_playground_cms_assert( true === (bool) get_post_meta( $post_id, '_stc_show_share', true ), 'Share presentation metadata was not mapped.' );
	stc_playground_cms_assert( true === (bool) get_post_meta( $post_id, '_stc_show_toc', true ), 'TOC presentation metadata was not mapped.' );
	stc_playground_cms_assert( 'attraction' === get_post_meta( $post_id, '_stc_hero_variant', true ), 'Hero presentation metadata was not mapped.' );
	stc_playground_cms_assert( STC_COMPONENT_REGISTRY_VERSION === get_post_meta( $post_id, '_stc_component_contract_version', true ), 'Contract provenance was not stored.' );
	stc_playground_cms_assert( 'CMS Runtime Draft SEO' === get_post_meta( $post_id, '_stc_seo_title', true ), 'SEO metadata was not stored.' );
	stc_playground_cms_assert( get_post_thumbnail_id( $post_id ) === $media_id, 'Featured media was not applied.' );
	$jsonld_markup = stc_get_cms_jsonld_markup( $post_id );
	stc_playground_cms_assert( false !== strpos( $jsonld_markup, '<script type="application/ld+json">' ) && false !== strpos( $jsonld_markup, '"@type":"Article"' ), 'Structured JSON-LD output is unavailable.' );

	$GLOBALS['post'] = $post;
	setup_postdata( $post );
	$rendered = apply_filters( 'the_content', $post->post_content );
	wp_reset_postdata();
	foreach ( array(
		'Carry the passport used for the reservation.',
		'wp-block-heading',
		'wp-block-list',
		'stc-content-image',
		'stc-content-block--quick-answer',
		'stc-content-block--key-takeaways',
		'stc-content-block--quick-facts',
		'stc-content-block--tip',
		'stc-content-block--warning',
		'stc-content-block--steps',
		'stc-content-block--checklist',
		'stc-content-block--faq',
		'stc-content-block--comparison',
		'stc-dynamic-component--planner',
		'stc-dynamic-component--ticket',
		'stc-dynamic-component--affiliate',
		'stc-commercial-component--affiliate_booking_card',
		'stc-commercial-component--affiliate_search_card',
		'stc-commercial-component--affiliate_banner',
		'stc-commercial-component--affiliate_promotion_card',
		'data-stc-commercial="true"',
	) as $token ) {
		stc_playground_cms_assert( false !== strpos( $rendered, $token ), 'Theme rendering is missing: ' . $token );
	}

	$second = stc_playground_cms_request( 'POST', '/stc/v1/cms-articles', $package );
	$second_data = $second->get_data();
	stc_playground_cms_assert( 200 === $second->get_status() && $post_id === (int) $second_data['post_id'] && true === $second_data['updated'], 'Idempotent POST created a duplicate post.' );
	$mapped = get_posts( array( 'post_type' => 'post', 'post_status' => 'any', 'fields' => 'ids', 'meta_key' => '_stc_cms_page_id', 'meta_value' => 'runtime-page-001' ) );
	stc_playground_cms_assert( 1 === count( $mapped ), 'Idempotent POST left duplicate mapped posts.' );

	$package['page']['metadata']['title'] = 'CMS Runtime Draft Updated';
	$package['publication']['existing_post_id'] = $post_id;
	$put = stc_playground_cms_request( 'PUT', '/stc/v1/cms-articles/' . $post_id, $package );
	stc_playground_cms_assert( 200 === $put->get_status() && 'CMS Runtime Draft Updated' === get_the_title( $post_id ), 'Explicit PUT did not update the draft.' );

	$invalid = stc_playground_cms_clone( $package );
	$invalid['page']['blocks'][0]['type'] = 'unknown_component';
	stc_playground_cms_expect_error( $invalid, 'UNKNOWN_COMPONENT', 'Unknown component' );

	$invalid = stc_playground_cms_clone( $package );
	$invalid['page']['blocks'][0]['variant'] = 'unsupported';
	stc_playground_cms_expect_error( $invalid, 'UNSUPPORTED_VARIANT', 'Unsupported variant' );

	$invalid = stc_playground_cms_clone( $package );
	unset( $invalid['page']['blocks'][0]['data']['content'] );
	stc_playground_cms_expect_error( $invalid, 'INVALID_COMPONENT_DATA', 'Missing required field' );

	$invalid = stc_playground_cms_clone( $package );
	$invalid['page']['blocks'][0]['data']['html'] = '<script>alert(1)</script>';
	stc_playground_cms_expect_error( $invalid, 'INVALID_COMPONENT_DATA', 'Unknown/raw HTML field' );

	$invalid = stc_playground_cms_clone( $package );
	$commercial_index = stc_playground_cms_block_index( $invalid['page']['blocks'], 'affiliate_booking_card' );
	$invalid['page']['blocks'][ $commercial_index ]['data']['target_url'] = 'https://trip.com.evil.example/';
	stc_playground_cms_expect_error( $invalid, 'UNSAFE_AFFILIATE_URL', 'Unsafe affiliate URL' );

	$invalid = stc_playground_cms_clone( $package );
	$search_index = stc_playground_cms_block_index( $invalid['page']['blocks'], 'affiliate_search_card' );
	unset( $invalid['page']['blocks'][ $search_index ]['data']['target_url'] );
	$invalid['page']['blocks'][ $search_index ]['data']['embed_config'] = array( 'embed_type' => 'search_box', 'src' => 'https://pages.trip.com/search', 'width' => 10, 'height' => 240, 'language' => 'en', 'theme' => 'light', 'variant' => 'standard' );
	stc_playground_cms_expect_error( $invalid, 'INVALID_COMMERCIAL_COMPONENT', 'Invalid commercial embed' );

	$invalid = stc_playground_cms_clone( $package );
	$invalid['contract']['componentContractVersion'] = '0.0.0';
	stc_playground_cms_expect_error( $invalid, 'CONTRACT_VERSION_MISMATCH', 'Contract mismatch' );

	$invalid = stc_playground_cms_clone( $package );
	$invalid['page']['metadata']['presentation']['article_hero']['variant'] = 'unknown';
	stc_playground_cms_expect_error( $invalid, 'INVALID_PRESENTATION', 'Invalid presentation' );

	$invalid = stc_playground_cms_clone( $package );
	$invalid['publication']['status'] = 'publish';
	stc_playground_cms_expect_error( $invalid, 'POST_NOT_DRAFT', 'Publish attempt' );

	wp_update_post( array( 'ID' => $post_id, 'post_status' => 'publish' ) );
	$published_guard = stc_playground_cms_request( 'PUT', '/stc/v1/cms-articles/' . $post_id, $package );
	$published_data  = $published_guard->get_data();
	stc_playground_cms_assert( 409 === $published_guard->get_status() && 'POST_NOT_DRAFT' === $published_data['code'], 'Published post overwrite was not blocked.' );
	wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ) );

	update_option( 'stc_playground_cms_publish_verified', array( 'post_id' => $post_id, 'block_count' => count( $blocks ), 'contract_checksum' => stc_cms_component_contract_checksum() ), false );
}
