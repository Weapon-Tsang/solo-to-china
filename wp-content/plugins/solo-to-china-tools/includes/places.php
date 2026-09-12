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
    $catalog = stc_tools_active_catalog();
    if ( $catalog ) { return array_column( $catalog['places'], null, 'entity_key' ); }
    return stc_tools_builtin_places();
}
function stc_tools_builtin_places() {
    static $places;
    if (null === $places) {
        $catalog=json_decode(file_get_contents(STC_TOOLS_PATH.'data/destinations-v1.json'),true);
        $places=is_array($catalog) && isset($catalog['places']) ? array_column($catalog['places'],null,'entity_key') : array();
    }
    return $places;
}

function stc_tools_public_place( $place ) {
	$defaults = array(
		'entity_type' => 'attraction', 'entity_key' => '', 'name_en' => '', 'name_zh' => '', 'city_en' => '', 'city_zh' => '', 'province_en' => '', 'province_zh' => '', 'country' => 'China',
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
	foreach ( array( 'name_zh', 'city', 'address', 'entrance', 'dropoff', 'arrival_note', 'viewpoint' ) as $source_field ) {
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

function stc_tools_find_guide( $entity_key, $fallback_slug, $type = 'attraction-guide' ) {
	if ( function_exists( 'stc_resolve_entity_guide' ) ) { return stc_resolve_entity_guide( $entity_key, $type, $fallback_slug ); }
    // Without the parent, require explicit type metadata and public content.
    $posts=get_posts(array('post_type'=>'post','post_status'=>'publish','has_password'=>false,'numberposts'=>-1,'meta_key'=>'_stc_entity_key','meta_value'=>sanitize_title($entity_key),'orderby'=>array('date'=>'DESC','ID'=>'ASC')));
    if ($fallback_slug) { $fallback=get_page_by_path(sanitize_title($fallback_slug),OBJECT,'post'); if ($fallback) {$posts[]=$fallback;} }
    foreach($posts as $post) {
      if ('publish'!==$post->post_status || $post->post_password || get_post_meta($post->ID,'_stc_guide_type',true)!==$type) {continue;}
      return array('id'=>$post->ID,'title'=>get_the_title($post),'url'=>get_permalink($post));
    }
    return null;
}

function stc_tools_enrich_place_guides( $place ) {
	$places = stc_tools_get_places();
	$source = isset( $places[ $place['entity_key'] ] ) ? $places[ $place['entity_key'] ] : array();
	$place['related_attraction_guide'] = stc_tools_find_guide( $place['entity_key'], isset( $source['attraction_guide_slug'] ) ? $source['attraction_guide_slug'] : '' );
	$place['related_city_guide'] = stc_tools_find_guide( sanitize_title( $place['city_en'] ), isset( $source['city_guide_slug'] ) ? $source['city_guide_slug'] : '', 'city-guide' );
	return $place;
}

function stc_tools_normalize_query($value) { return trim(preg_replace('/[\s,\x{FF0C}._-]+/u',' ',remove_accents(strtolower(sanitize_text_field($value))))); }

function stc_tools_resolve_curated_place( $query ) {
	$needle  = stc_tools_normalize_query($query);
	$matches = array();
	if ( '' === $needle ) {
		return array();
	}
	foreach ( stc_tools_get_places() as $place ) {
		$aliases = array_merge( $place['aliases'], array( $place['entity_key'], $place['name_en'], $place['name_zh'], $place['name_en'].' '.$place['city_en'], $place['city_en'].' '.$place['name_en'], $place['city_zh'].$place['name_zh'], $place['name_zh'].$place['city_zh'] ) );
		foreach ( $aliases as $alias ) {
			if ( $needle === stc_tools_normalize_query($alias) ) {
				$matches[] = stc_tools_enrich_place_guides( stc_tools_public_place( $place ) );
				break;
			}
		}
	}
	return $matches;
}

function stc_tools_copy_destination( $place ) {
	$lines = array_filter( array( $place['name_zh'], $place['city_zh'], 'VERIFIED' === ( $place['sources']['dropoff'] ?? '' ) ? $place['recommended_dropoff_zh'] : ( 'VERIFIED' === ( $place['sources']['entrance'] ?? '' ) ? $place['recommended_entrance_zh'] : '' ), 'VERIFIED' === $place['sources']['address'] ? $place['verified_address_zh'] : '', 'VERIFIED' === ( $place['sources']['arrival_note'] ?? '' ) ? $place['arrival_note'] : '' ) );
	return implode( "\n", $lines );
}

function stc_tools_arrival_summary($place) {
 $sources=$place['sources'];
 if ('VERIFIED'===($sources['dropoff'] ?? '') || 'VERIFIED'===($sources['entrance'] ?? '')) {return 'Arrival point confirmed in the destination record.';}
 if ('VERIFIED'===($sources['address'] ?? '') && !empty($place['verified_address_zh'])) {return 'Official address recorded. Visitor entrance and vehicle drop-off are not confirmed.';}
 return 'Chinese name confirmed. '.('VERIFIED'===($sources['city'] ?? '')?'City confirmed. ':'City not verified. ').'Specific address, entrance and drop-off are not verified.';
}
