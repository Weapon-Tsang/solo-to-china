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
		if ( isset( $example['media_id'] ) ) { $example['media_id'] = $media_id; }
		$blocks[] = array( 'type' => $definition['id'], 'variant' => $variant, 'data' => $example );
	}
	stc_playground_cms_assert( 26 === count( $blocks ), 'CMS adapter did not resolve all 26 page-block capabilities.' );

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
			'inLanguage' => 'en',
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
	stc_playground_cms_assert( 'en-US' === get_post_meta( $post_id, '_stc_content_language', true ), 'Schema language was not stored for document semantics.' );
	stc_playground_cms_assert( get_post_thumbnail_id( $post_id ) === $media_id, 'Featured media was not applied.' );
	$jsonld_markup = stc_get_cms_jsonld_markup( $post_id );
	stc_playground_cms_assert( false !== strpos( $jsonld_markup, '<script type="application/ld+json">' ) && false !== strpos( $jsonld_markup, '"@type":"Article"' ), 'Structured JSON-LD output is unavailable.' );

	$GLOBALS['post'] = $post;
	setup_postdata( $post );
	// Exercise the head filters in the same singular-query scope used by a real
	// preview request. Merely assigning the global post is not sufficient for
	// is_singular() and get_queried_object_id().
	$GLOBALS['wp_query']->queried_object    = $post;
	$GLOBALS['wp_query']->queried_object_id = $post_id;
	$GLOBALS['wp_query']->is_single         = true;
	$GLOBALS['wp_query']->is_singular       = true;
	$GLOBALS['wp_query']->is_home           = false;
	$GLOBALS['wp_query']->is_archive        = false;
	stc_playground_cms_assert( 'CMS Runtime Draft SEO' === stc_filter_rank_math_cms_title( 'fallback' ), 'Rank Math title mapper ignored CMS metadata.' );
	stc_playground_cms_assert( 'Runtime verification for the CMS WordPress delivery path.' === stc_filter_rank_math_cms_description( 'fallback' ), 'Rank Math description mapper ignored CMS metadata.' );
	stc_playground_cms_assert( get_permalink( $post_id ) === stc_filter_rank_math_cms_canonical( 'https://invalid.example/' ), 'Rank Math canonical mapper is not permalink-bound.' );
	$draft_robots = stc_filter_rank_math_cms_robots( array( 'index' => 'index', 'follow' => 'follow' ) );
	stc_playground_cms_assert( 'noindex' === $draft_robots['index'] && 'nofollow' === $draft_robots['follow'], 'Rank Math draft robots mapper did not fail closed.' );
	stc_playground_cms_assert( false !== strpos( stc_filter_cms_language_attributes( 'lang="zh-CN" dir="ltr"' ), 'lang="en-US"' ), 'Document language did not follow the CMS schema.' );
	stc_playground_cms_assert( (bool) preg_match( '/^[A-Z][a-z]+ [0-9]{1,2}, [0-9]{4}$/', stc_get_article_date_label( $post_id ) ), 'English CMS date is still localized by the administrator locale.' );
	if ( ! stc_cms_seo_plugin_active() ) {
		$draft_core_robots = stc_filter_cms_robots( array( 'index' => true, 'follow' => true ) );
		stc_playground_cms_assert( ! empty( $draft_core_robots['noindex'] ) && ! empty( $draft_core_robots['nofollow'] ), 'Core draft robots mapper did not fail closed.' );
	}
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
		'stc-content-block--route-timeline',
		'stc-content-block--checklist',
		'stc-content-block--faq',
		'stc-content-block--comparison',
		'stc-content-block--pros-cons',
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

	$page_hash = (string) get_post_meta( $post_id, '_stc_page_payload_hash', true );
	$ticket_request = new WP_REST_Request( 'POST', '/stc/v1/cms-articles/' . $post_id . '/preview-ticket' );
	$ticket_request->set_url_params( array( 'post_id' => $post_id ) );
	$ticket_request->set_header( 'content-type', 'application/json' );
	$ticket_request->set_body( wp_json_encode( array( 'cms_draft_id' => 'runtime-draft-001', 'cms_revision' => 1, 'page_payload_hash' => $page_hash ) ) );
	$ticket_response = rest_do_request( $ticket_request );
	$ticket_data = $ticket_response->get_data();
	stc_playground_cms_assert( 201 === $ticket_response->get_status() && false !== strpos( $ticket_data['preview_url'], '#stc_preview_ticket=' ), 'Scoped preview ticket was not minted in a URL fragment.' );
	stc_playground_cms_assert( false === strpos( wp_parse_url( $ticket_data['preview_url'], PHP_URL_QUERY ), 'stc_preview_ticket' ), 'Preview bearer leaked into the request query string.' );
	parse_str( (string) wp_parse_url( $ticket_data['preview_url'], PHP_URL_FRAGMENT ), $fragment );
	$exchange_request = new WP_REST_Request( 'POST', '/stc/v1/cms-preview/exchange' );
	$exchange_request->set_header( 'content-type', 'application/json' );
	$exchange_request->set_body( wp_json_encode( array( 'token' => $fragment['stc_preview_ticket'] ) ) );
	$exchange_response = rest_do_request( $exchange_request );
	$exchange_data = $exchange_response->get_data();
	stc_playground_cms_assert( 200 === $exchange_response->get_status() && false === strpos( $exchange_data['preview_url'], 'stc_preview_ticket' ) && false !== strpos( $exchange_data['preview_url'], 'stc_cms_preview=1' ), 'Preview exchange did not return a bearer-free scoped URL.' );
	$tampered_request = new WP_REST_Request( 'POST', '/stc/v1/cms-preview/exchange' );
	$tampered_request->set_header( 'content-type', 'application/json' );
	$tampered_request->set_body( wp_json_encode( array( 'token' => str_repeat( '0', 64 ) ) ) );
	stc_playground_cms_assert( 403 === rest_do_request( $tampered_request )->get_status(), 'Tampered preview token was accepted.' );
	$stale_request = new WP_REST_Request( 'POST', '/stc/v1/cms-articles/' . $post_id . '/preview-ticket' );
	$stale_request->set_url_params( array( 'post_id' => $post_id ) );
	$stale_request->set_header( 'content-type', 'application/json' );
	$stale_request->set_body( wp_json_encode( array( 'cms_draft_id' => 'runtime-draft-001', 'cms_revision' => 1, 'page_payload_hash' => str_repeat( '0', 64 ) ) ) );
	stc_playground_cms_assert( 409 === rest_do_request( $stale_request )->get_status(), 'Stale preview revision was accepted.' );

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

	global $wpdb;
	$wpdb->update( $wpdb->posts, array( 'post_modified_gmt' => gmdate( 'Y-m-d H:i:s', time() + 60 ) ), array( 'ID' => $post_id ), array( '%s' ), array( '%d' ) );
	clean_post_cache( $post_id );
	$external_guard = stc_playground_cms_request( 'PUT', '/stc/v1/cms-articles/' . $post_id, $package );
	$external_data  = $external_guard->get_data();
	stc_playground_cms_assert( 409 === $external_guard->get_status() && 'POST_EXTERNALLY_MODIFIED' === $external_data['code'], 'Externally modified draft overwrite was not blocked.' );

	wp_update_post( array( 'ID' => $post_id, 'post_status' => 'publish' ) );
	$published_robots = stc_filter_rank_math_cms_robots( array( 'index' => 'noindex', 'follow' => 'nofollow' ) );
	stc_playground_cms_assert( 'index' === $published_robots['index'] && 'follow' === $published_robots['follow'], 'An explicitly published CMS post remained noindex.' );
	if ( ! stc_cms_seo_plugin_active() ) {
		$published_core_robots = stc_filter_cms_robots( array( 'noindex' => true, 'nofollow' => true ) );
		stc_playground_cms_assert( ! empty( $published_core_robots['index'] ) && ! empty( $published_core_robots['follow'] ), 'An explicitly published CMS post remained noindex in the core mapper.' );
	}
	$published_guard = stc_playground_cms_request( 'PUT', '/stc/v1/cms-articles/' . $post_id, $package );
	$published_data  = $published_guard->get_data();
	stc_playground_cms_assert( 409 === $published_guard->get_status() && 'POST_NOT_DRAFT' === $published_data['code'], 'Published post overwrite was not blocked.' );
	wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ) );

	update_option( 'stc_playground_cms_publish_verified', array( 'post_id' => $post_id, 'block_count' => count( $blocks ), 'contract_checksum' => stc_cms_component_contract_checksum(), 'preview_url' => $ticket_data['preview_url'] ), false );
}
