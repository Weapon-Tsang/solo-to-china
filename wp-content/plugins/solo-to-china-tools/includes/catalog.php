<?php
/** Versioned destination catalog. Admin-only imports are validated before an atomic pointer change. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function stc_tools_validate_catalog( $catalog ) {
	if ( ! is_array( $catalog ) || array_diff( array_keys( $catalog ), array( 'schema_version', 'version', 'places' ) ) || '1.0' !== ( $catalog['schema_version'] ?? '' ) || ! is_string( $catalog['version'] ?? null ) || ! preg_match( '/^\d+\.\d+\.\d+$/', $catalog['version'] ) || empty( $catalog['places'] ) || ! is_array( $catalog['places'] ) || count( $catalog['places'] ) > 5000 || $catalog['places'] !== array_values($catalog['places']) ) { return new WP_Error( 'invalid_catalog', 'Invalid catalog version or records.', array( 'status' => 422 ) ); }
	$allowed = array( 'entity_key','entity_type','name_en','name_zh','city_en','city_zh','province_en','province_zh','country','aliases','verified_address_zh','verified_address_en','recommended_entrance_zh','recommended_dropoff_zh','arrival_note','viewpoint_name_en','viewpoint_name_zh','viewpoint_description','sources','evidence','attraction_guide_slug','city_guide_slug','related_entity_key' );
	$seen = array();
	foreach ( $catalog['places'] as $place ) {
		if ( ! is_array( $place ) || array_diff( array_keys( $place ), $allowed ) ) { return new WP_Error( 'invalid_catalog', 'Unknown destination fields.', array( 'status' => 422 ) ); }
		foreach ( array( 'entity_key','entity_type','name_en','name_zh','city_en','city_zh' ) as $key ) { if ( empty( $place[$key] ) || ! is_string( $place[$key] ) ) { return new WP_Error( 'invalid_catalog', 'Missing destination identity.', array( 'status' => 422 ) ); } }
		$key = $place['entity_key'];
		if ( !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/',$key) || $key !== sanitize_title( $key ) || isset( $seen[$key] ) || ! in_array( $place['entity_type'], array( 'city','attraction','neighborhood','exact_viewpoint','restaurant','hotel','station' ), true ) ) { return new WP_Error( 'invalid_catalog', 'Invalid or duplicate destination identity.', array( 'status' => 422 ) ); }
		$seen[$key] = true;
		foreach ( $place as $field => $value ) {
			if ( in_array( $field, array( 'aliases','sources','evidence' ), true ) ) { continue; }
			if ( ! is_string( $value ) || strlen( $value ) > 1500 || $value !== wp_strip_all_tags( $value ) ) { return new WP_Error( 'invalid_catalog', 'Invalid destination text.', array( 'status' => 422 ) ); }
		}
		if ( ! isset( $place['aliases'] ) || ! is_array( $place['aliases'] ) || count( $place['aliases'] ) > 30 || $place['aliases'] !== array_values($place['aliases']) ) { return new WP_Error( 'invalid_catalog', 'Invalid aliases.', array( 'status' => 422 ) ); }
		foreach ( $place['aliases'] as $alias ) { if ( ! is_string( $alias ) || strlen( $alias ) > 300 || $alias !== wp_strip_all_tags( $alias ) ) { return new WP_Error( 'invalid_catalog', 'Invalid alias.', array( 'status' => 422 ) ); } }
		if ( ! isset( $place['sources'], $place['evidence'] ) || ! is_array( $place['sources'] ) || ! is_array( $place['evidence'] ) ) { return new WP_Error( 'invalid_catalog', 'Field provenance is required.', array( 'status' => 422 ) ); }
		$fields = array( 'name_zh','city','address','entrance','dropoff','arrival_note','viewpoint' );
		if ( array_diff( array_keys( $place['sources'] ), $fields ) || array_diff( array_keys( $place['evidence'] ), $fields ) ) { return new WP_Error( 'invalid_catalog', 'Unknown provenance field.', array( 'status' => 422 ) ); }
		foreach ( $place['evidence'] as $evidence ) {
			if ( ! is_array( $evidence ) || array_diff( array_keys( $evidence ), array( 'source_url', 'checked_at', 'note' ) ) ) { return new WP_Error( 'invalid_catalog', 'Invalid evidence record.', array( 'status' => 422 ) ); }
			foreach ( $evidence as $value ) { if ( ! is_string( $value ) || strlen( $value ) > 1500 || $value !== wp_strip_all_tags( $value ) ) { return new WP_Error( 'invalid_catalog', 'Invalid evidence text.', array( 'status' => 422 ) ); } }
			$date = $evidence['checked_at'] ?? '';
			if ( 'https' !== wp_parse_url( $evidence['source_url'] ?? '', PHP_URL_SCHEME ) || ! wp_http_validate_url( $evidence['source_url'] ?? '' ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || ! strtotime( $date ) || gmdate( 'Y-m-d', strtotime( $date ) ) !== $date || $date > gmdate( 'Y-m-d' ) ) { return new WP_Error( 'invalid_catalog', 'Evidence requires a source and valid review date.', array( 'status' => 422 ) ); }
		}
		foreach ( $place['sources'] as $field => $status ) {
			if ( ! in_array( $status, array( 'VERIFIED','UNKNOWN' ), true ) ) { return new WP_Error( 'invalid_catalog', 'Catalog records must be reviewed or unknown.', array( 'status' => 422 ) ); }
			if ( 'VERIFIED' !== $status ) { continue; }
			$evidence = $place['evidence'][$field] ?? array();
            if (!is_array($evidence)) { return new WP_Error('invalid_catalog','Invalid evidence record.',array('status'=>422)); }
            foreach ($evidence as $value) { if (!is_string($value) || strlen($value)>1500) { return new WP_Error('invalid_catalog','Invalid evidence text.',array('status'=>422)); } }
            $required_values = array('name_zh'=>'name_zh','city'=>'city_zh','address'=>'verified_address_zh','entrance'=>'recommended_entrance_zh','dropoff'=>'recommended_dropoff_zh','arrival_note'=>'arrival_note','viewpoint'=>'viewpoint_name_zh');
            if (empty($place[$required_values[$field]])) { return new WP_Error('invalid_catalog','A verified field needs a value.',array('status'=>422)); }
			$date = $evidence['checked_at'] ?? '';
			if ( array_diff( array_keys( $evidence ), array( 'source_url','checked_at','note' ) ) || 'https' !== wp_parse_url( $evidence['source_url'] ?? '', PHP_URL_SCHEME ) || ! wp_http_validate_url( $evidence['source_url'] ?? '' ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || ! strtotime( $date ) || gmdate( 'Y-m-d', strtotime( $date ) ) !== $date || $date > gmdate( 'Y-m-d' ) ) { return new WP_Error( 'invalid_catalog', 'Verified fields require a source and valid review date.', array( 'status' => 422 ) ); }
		}
	}
	return $catalog;
}

function stc_tools_active_catalog() {
	$active = get_option( 'stc_destination_catalog_active', '' );
	$catalog = $active ? get_option( 'stc_destination_catalog_' . $active, null ) : null;
	return is_array( $catalog ) ? $catalog : null;
}

function stc_tools_import_catalog( $catalog ) {
	$valid = stc_tools_validate_catalog( $catalog );
	if ( is_wp_error( $valid ) ) { return $valid; }
	if (!($catalog_lease=stc_tools_acquire_lease('catalog-import',30))) { return new WP_Error('catalog_busy','Another catalog update is in progress.',array('status'=>409)); }
 try {
 $revision = hash( 'sha256', wp_json_encode( $valid ) );
	$active = get_option( 'stc_destination_catalog_active', '' );
	if ( $revision === $active ) { return array( 'revision' => $revision, 'version' => $valid['version'] ); }
	$option = 'stc_destination_catalog_' . $revision;
	if ( ! add_option( $option, $valid, '', false ) && get_option( $option ) !== $valid ) { return new WP_Error( 'catalog_storage', 'Catalog could not be stored.', array( 'status' => 503 ) ); }
	$old_previous=get_option('stc_destination_catalog_previous','');
    update_option( 'stc_destination_catalog_previous', $active, false );
    if (!update_option('stc_destination_catalog_active',$revision,false) && get_option('stc_destination_catalog_active','')!==$revision) {
        update_option('stc_destination_catalog_previous',$old_previous,false);
        return new WP_Error('catalog_storage','The active catalog could not be updated.',array('status'=>503));
    }
	return array( 'revision' => $revision, 'version' => $valid['version'], 'count' => count( $valid['places'] ) );
 } finally { stc_tools_release_lease($catalog_lease); }
}

function stc_tools_rollback_catalog() {
 if (!($catalog_lease=stc_tools_acquire_lease('catalog-import',30))) { return new WP_Error('catalog_busy','Another catalog update is in progress.',array('status'=>409)); }
 try {
	$previous = get_option( 'stc_destination_catalog_previous', '' );
	$active = get_option( 'stc_destination_catalog_active', '' );
	if ( $previous && ! is_array( get_option( 'stc_destination_catalog_' . $previous ) ) ) { return new WP_Error( 'invalid_revision', 'Previous catalog is unavailable.', array( 'status' => 422 ) ); }
	if ( ! update_option( 'stc_destination_catalog_active', $previous, false ) && get_option( 'stc_destination_catalog_active', '' ) !== $previous ) { return new WP_Error( 'catalog_storage', 'The prior catalog could not be activated.', array( 'status' => 503 ) ); }
	update_option( 'stc_destination_catalog_previous', $active, false );
	return array( 'revision' => $previous ?: 'builtin' );
 } finally { stc_tools_release_lease($catalog_lease); }
}

add_action( 'rest_api_init', function () {
	$permission = function () { return current_user_can( 'manage_options' ); };
	register_rest_route( 'stc/v1', '/destination-catalog', array( 'methods' => 'POST', 'permission_callback' => $permission, 'callback' => function ( $request ) { return stc_tools_import_catalog( $request->get_json_params() ); } ) );
	register_rest_route( 'stc/v1', '/destination-catalog/rollback', array( 'methods' => 'POST', 'permission_callback' => $permission, 'callback' => 'stc_tools_rollback_catalog' ) );
} );
