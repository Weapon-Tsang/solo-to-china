<?php
/** WordPress Playground assertions for the two guest-first web tools. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function stc_playground_web_tools_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function stc_playground_verify_web_tools() {
	$high = stc_tools_prepare_vision_result(
		array(
			'status'            => 'matched',
			'confidence'        => 'high',
			'match_level'       => 'attraction',
			'primary_candidate' => array(
				'candidate_entity_key' => 'forbidden-city',
				'canonical_name_en'    => 'Untrusted replacement',
				'canonical_name_zh'    => '不可信名称',
				'verified_address_zh'  => 'Untrusted provider address',
				'latitude'             => '39.0',
			),
		)
	);
	stc_playground_web_tools_assert( 'forbidden-city' === $high['primary_candidate']['entity_key'], 'High-confidence canonical candidate did not resolve.' );
	stc_playground_web_tools_assert( '故宫博物院' === $high['primary_candidate']['name_zh'], 'Provider text replaced the verified Chinese POI name.' );
	stc_playground_web_tools_assert( '北京市东城区景山前街4号' === $high['primary_candidate']['verified_address_zh'], 'Provider text replaced the verified address.' );
	stc_playground_web_tools_assert( ! isset( $high['primary_candidate']['latitude'] ), 'Provider coordinates leaked into the normalized place.' );
	stc_playground_web_tools_assert( 'UNKNOWN' === $high['primary_candidate']['sources']['viewpoint'], 'AI-only viewpoint was marked verified.' );

	$medium = stc_tools_prepare_vision_result(
		array(
			'status'                 => 'ambiguous',
			'confidence'             => 'medium',
			'match_level'            => 'neighborhood',
			'primary_candidate'      => array( 'candidate_entity_key' => 'hongya-cave' ),
			'alternative_candidates' => array( array( 'candidate_entity_key' => 'qiansimen-bridge' ) ),
		)
	);
	stc_playground_web_tools_assert( null === $medium['primary_candidate'], 'Medium-confidence result incorrectly selected a primary candidate.' );
	stc_playground_web_tools_assert( 2 === count( $medium['alternative_candidates'] ), 'Medium-confidence result did not preserve candidate choice.' );

	$low = stc_tools_prepare_vision_result(
		array(
			'status'            => 'possible',
			'confidence'        => 'low',
			'match_level'       => 'city',
			'primary_candidate' => array( 'candidate_entity_key' => 'the-bund' ),
		)
	);
	stc_playground_web_tools_assert( null === $low['primary_candidate'], 'Low-confidence result incorrectly promoted a place.' );

	$likely_viewpoint = stc_tools_normalize_provider_candidate(
		array( 'candidate_entity_key' => 'forbidden-city', 'viewpoint_name_en' => 'A provider guess' ),
		'high',
		'exact_viewpoint'
	);
	stc_playground_web_tools_assert( 'likely' === $likely_viewpoint['viewpoint_status'], 'Provider-only viewpoint was not kept uncertain.' );
	stc_playground_web_tools_assert( 'AI_INFERRED' === $likely_viewpoint['sources']['viewpoint'], 'Provider-only viewpoint source is wrong.' );

	$english = stc_tools_resolve_curated_place( 'Shanghai Hongqiao Railway Station' );
	$chinese = stc_tools_resolve_curated_place( '上海虹桥站' );
	$ambiguous = stc_tools_resolve_curated_place( 'West Lake' );
	$unknown = stc_tools_resolve_curated_place( 'Definitely Not A Verified Place' );
	stc_playground_web_tools_assert( 1 === count( $english ) && '上海虹桥站' === $english[0]['name_zh'], 'English destination did not resolve to the canonical Chinese station.' );
	stc_playground_web_tools_assert( 1 === count( $chinese ) && 'shanghai-hongqiao-railway-station' === $chinese[0]['entity_key'], 'Chinese destination input did not resolve.' );
	stc_playground_web_tools_assert( 2 === count( $ambiguous ), 'Ambiguous destination did not retain both cities.' );
	stc_playground_web_tools_assert( 0 === count( $unknown ), 'Unknown destination was fabricated.' );

	$jingshan = stc_tools_get_place( 'jingshan-park' );
	stc_playground_web_tools_assert( 'UNKNOWN' === $jingshan['sources']['address'] && '' === $jingshan['verified_address_zh'], 'Unverified street address was exposed.' );
	stc_playground_web_tools_assert( "景山公园\n北京" === stc_tools_copy_destination( $jingshan ), 'Address-free Taxi Card did not fall back to Chinese POI and city.' );
	stc_playground_web_tools_assert( ! empty( $high['primary_candidate']['related_attraction_guide']['url'] ), 'Published attraction guide was not enriched.' );
	stc_playground_web_tools_assert( ! empty( $high['primary_candidate']['related_city_guide']['url'] ), 'Published city guide was not enriched.' );

	$oversize = stc_tools_validate_image_upload(
		array(
			'tmp_name' => __FILE__,
			'name'     => 'oversize.png',
			'size'     => STC_TOOLS_MAX_IMAGE_BYTES + 1,
			'error'    => UPLOAD_ERR_OK,
		)
	);
	stc_playground_web_tools_assert( is_wp_error( $oversize ) && 'file_too_large' === $oversize->get_error_code(), 'Oversize upload was not rejected before processing.' );

	$bucket = 'playground_' . wp_generate_uuid4();
	stc_playground_web_tools_assert( true === stc_tools_rate_limit( $bucket, 2, MINUTE_IN_SECONDS ), 'First rate-limit request failed.' );
	stc_playground_web_tools_assert( true === stc_tools_rate_limit( $bucket, 2, MINUTE_IN_SECONDS ), 'Second rate-limit request failed.' );
	$limited = stc_tools_rate_limit( $bucket, 2, MINUTE_IN_SECONDS );
	stc_playground_web_tools_assert( is_wp_error( $limited ) && 'stc_rate_limited' === $limited->get_error_code(), 'Anonymous rate limit was not enforced.' );
}
