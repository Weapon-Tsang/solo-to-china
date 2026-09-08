<?php
/**
 * Canonical place data and normalized destination helpers.
 *
 * @package SoloToChinaTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function stc_tools_get_places() {
	return array(
		'forbidden-city' => array(
			'entity_key' => 'forbidden-city', 'name_en' => 'Forbidden City', 'name_zh' => '故宫博物院',
			'city_en' => 'Beijing', 'city_zh' => '北京', 'province_en' => 'Beijing', 'province_zh' => '北京市', 'country' => 'China',
			'aliases' => array( 'forbidden city', 'palace museum', '故宫', '故宫博物院' ),
			'verified_address_zh' => '北京市东城区景山前街4号', 'verified_address_en' => '4 Jingshan Front Street, Dongcheng District, Beijing',
			'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'VERIFIED', 'viewpoint' => 'UNKNOWN' ),
			'attraction_guide_slug' => 'forbidden-city-first-time-visitor-guide', 'city_guide_slug' => 'beijing-first-time-city-guide',
		),
		'jingshan-park' => array(
			'entity_key' => 'jingshan-park', 'name_en' => 'Jingshan Park', 'name_zh' => '景山公园',
			'city_en' => 'Beijing', 'city_zh' => '北京', 'province_en' => 'Beijing', 'province_zh' => '北京市', 'country' => 'China',
			'aliases' => array( 'jingshan park', '景山公园' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
			'city_guide_slug' => 'beijing-first-time-city-guide',
		),
		'hongya-cave' => array(
			'entity_key' => 'hongya-cave', 'name_en' => 'Hongya Cave', 'name_zh' => '洪崖洞民俗风貌区',
			'city_en' => 'Chongqing', 'city_zh' => '重庆', 'province_en' => 'Chongqing', 'province_zh' => '重庆市', 'country' => 'China',
			'aliases' => array( 'hongya cave', 'hongyadong', '洪崖洞', '洪崖洞民俗风貌区' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
			'city_guide_slug' => 'chongqing-city-guide',
		),
		'qiansimen-bridge' => array(
			'entity_key' => 'qiansimen-bridge', 'name_en' => 'Qiansimen Bridge', 'name_zh' => '千厮门嘉陵江大桥',
			'city_en' => 'Chongqing', 'city_zh' => '重庆', 'province_en' => 'Chongqing', 'province_zh' => '重庆市', 'country' => 'China',
			'aliases' => array( 'qiansimen bridge', '千厮门大桥', '千厮门嘉陵江大桥' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
			'city_guide_slug' => 'chongqing-city-guide',
		),
		'the-bund' => array(
			'entity_key' => 'the-bund', 'name_en' => 'The Bund', 'name_zh' => '外滩',
			'city_en' => 'Shanghai', 'city_zh' => '上海', 'province_en' => 'Shanghai', 'province_zh' => '上海市', 'country' => 'China',
			'aliases' => array( 'the bund', 'bund', '外滩' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
			'city_guide_slug' => 'shanghai-city-guide',
		),
		'shanghai-hongqiao-railway-station' => array(
			'entity_key' => 'shanghai-hongqiao-railway-station', 'name_en' => 'Shanghai Hongqiao Railway Station', 'name_zh' => '上海虹桥站',
			'city_en' => 'Shanghai', 'city_zh' => '上海', 'province_en' => 'Shanghai', 'province_zh' => '上海市', 'country' => 'China',
			'aliases' => array( 'shanghai hongqiao railway station', 'shanghai hongqiao station', '上海虹桥站', '虹桥火车站' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
			'city_guide_slug' => 'shanghai-city-guide',
		),
		'west-lake-hangzhou' => array(
			'entity_key' => 'west-lake-hangzhou', 'name_en' => 'West Lake', 'name_zh' => '西湖',
			'city_en' => 'Hangzhou', 'city_zh' => '杭州', 'province_en' => 'Zhejiang', 'province_zh' => '浙江省', 'country' => 'China',
			'aliases' => array( 'west lake', 'hangzhou west lake', '杭州西湖', '西湖' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
			'city_guide_slug' => 'hangzhou-city-guide',
		),
		'west-lake-huizhou' => array(
			'entity_key' => 'west-lake-huizhou', 'name_en' => 'West Lake', 'name_zh' => '西湖',
			'city_en' => 'Huizhou', 'city_zh' => '惠州', 'province_en' => 'Guangdong', 'province_zh' => '广东省', 'country' => 'China',
			'aliases' => array( 'west lake', 'huizhou west lake', '惠州西湖', '西湖' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
		),
		'mandarin-oriental-shanghai' => array(
			'entity_key' => 'mandarin-oriental-shanghai', 'name_en' => 'Mandarin Oriental Pudong, Shanghai', 'name_zh' => '上海浦东文华东方酒店',
			'city_en' => 'Shanghai', 'city_zh' => '上海', 'province_en' => 'Shanghai', 'province_zh' => '上海市', 'country' => 'China',
			'aliases' => array( 'mandarin oriental shanghai', 'mandarin oriental pudong shanghai', '上海浦东文华东方酒店' ), 'sources' => array( 'name_zh' => 'VERIFIED', 'city' => 'VERIFIED', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
			'city_guide_slug' => 'shanghai-city-guide',
		),
	);
}

function stc_tools_public_place( $place ) {
	$defaults = array(
		'entity_key' => '', 'name_en' => '', 'name_zh' => '', 'city_en' => '', 'city_zh' => '', 'province_en' => '', 'province_zh' => '', 'country' => 'China',
		'match_level' => 'unknown', 'confidence' => 'low', 'verified_address_zh' => '', 'verified_address_en' => '', 'recommended_entrance_zh' => '',
		'recommended_dropoff_zh' => '', 'arrival_note' => '', 'viewpoint_name_en' => '', 'viewpoint_name_zh' => '', 'viewpoint_description' => '', 'viewpoint_status' => 'unknown',
		'recognition_reason' => '', 'visible_clues' => array(), 'sources' => array( 'name_zh' => 'UNKNOWN', 'city' => 'UNKNOWN', 'address' => 'UNKNOWN', 'viewpoint' => 'UNKNOWN' ),
		'related_city_guide' => null, 'related_attraction_guide' => null,
	);
	$place = wp_parse_args( is_array( $place ) ? $place : array(), $defaults );
	$place = array_intersect_key( $place, $defaults );
	foreach ( array( 'entity_key', 'name_en', 'name_zh', 'city_en', 'city_zh', 'province_en', 'province_zh', 'country', 'verified_address_zh', 'verified_address_en', 'recommended_entrance_zh', 'recommended_dropoff_zh', 'arrival_note', 'viewpoint_name_en', 'viewpoint_name_zh', 'viewpoint_description', 'recognition_reason' ) as $field ) {
		$place[ $field ] = sanitize_text_field( (string) $place[ $field ] );
	}
	$place['entity_key'] = sanitize_title( $place['entity_key'] );
	$place['confidence'] = in_array( $place['confidence'], array( 'high', 'medium', 'low' ), true ) ? $place['confidence'] : 'low';
	$place['match_level'] = in_array( $place['match_level'], array( 'exact_viewpoint', 'attraction', 'neighborhood', 'city', 'unknown' ), true ) ? $place['match_level'] : 'unknown';
	$place['viewpoint_status'] = in_array( $place['viewpoint_status'], array( 'verified', 'likely', 'unknown' ), true ) ? $place['viewpoint_status'] : 'unknown';
	$place['visible_clues'] = array_slice( array_values( array_filter( array_map( 'sanitize_text_field', (array) $place['visible_clues'] ) ) ), 0, 8 );
	$place['sources'] = is_array( $place['sources'] ) ? $place['sources'] : array();
	foreach ( array( 'name_zh', 'city', 'address', 'viewpoint' ) as $source_field ) {
		$source_value = isset( $place['sources'][ $source_field ] ) ? strtoupper( sanitize_key( $place['sources'][ $source_field ] ) ) : 'UNKNOWN';
		$place['sources'][ $source_field ] = in_array( $source_value, array( 'VERIFIED', 'AI_INFERRED', 'UNKNOWN' ), true ) ? $source_value : 'UNKNOWN';
	}
	return $place;
}

function stc_tools_get_place( $entity_key ) {
	$places = stc_tools_get_places();
	$key    = sanitize_title( $entity_key );
	return isset( $places[ $key ] ) ? stc_tools_enrich_place_guides( stc_tools_public_place( $places[ $key ] ) ) : null;
}

function stc_tools_find_guide( $entity_key, $fallback_slug ) {
	$query = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'no_found_rows' => true, 'meta_key' => '_stc_entity_key', 'meta_value' => sanitize_title( $entity_key ) ) );
	$post  = $query->have_posts() ? $query->posts[0] : null;
	if ( ! $post && $fallback_slug ) {
		$fallback = get_posts(
			array(
				'name'           => sanitize_title( $fallback_slug ),
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			)
		);
		$post = $fallback ? $fallback[0] : null;
	}
	return $post ? array( 'title' => get_the_title( $post ), 'url' => get_permalink( $post ) ) : null;
}

function stc_tools_enrich_place_guides( $place ) {
	$places = stc_tools_get_places();
	$source = isset( $places[ $place['entity_key'] ] ) ? $places[ $place['entity_key'] ] : array();
	$place['related_attraction_guide'] = stc_tools_find_guide( $place['entity_key'], isset( $source['attraction_guide_slug'] ) ? $source['attraction_guide_slug'] : '' );
	$place['related_city_guide'] = stc_tools_find_guide( sanitize_title( $place['city_en'] ), isset( $source['city_guide_slug'] ) ? $source['city_guide_slug'] : '' );
	return $place;
}

function stc_tools_resolve_curated_place( $query ) {
	$needle  = remove_accents( strtolower( trim( sanitize_text_field( $query ) ) ) );
	$matches = array();
	if ( '' === $needle ) {
		return array();
	}
	foreach ( stc_tools_get_places() as $place ) {
		$aliases = array_merge( $place['aliases'], array( $place['entity_key'], $place['name_en'], $place['name_zh'] ) );
		foreach ( $aliases as $alias ) {
			if ( $needle === remove_accents( strtolower( $alias ) ) ) {
				$matches[] = stc_tools_enrich_place_guides( stc_tools_public_place( $place ) );
				break;
			}
		}
	}
	return $matches;
}

function stc_tools_copy_destination( $place ) {
	$lines = array_filter( array( $place['name_zh'], 'VERIFIED' === $place['sources']['address'] ? $place['verified_address_zh'] : $place['city_zh'] ) );
	return implode( "\n", $lines );
}
