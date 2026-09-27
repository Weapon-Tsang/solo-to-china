<?php
/** Versioned, derived-only search storage. No article writes and no external lookups. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

const STC_SEARCH_RULE = 'search-projection-3';

/** Explicit migration, also used by the first normal article save after activation. */
function stc_search_install() {
	if ( '1' === get_option( 'stc_search_schema' ) ) { return true; }
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	dbDelta( "CREATE TABLE {$wpdb->prefix}stc_search_projection (
		post_id bigint(20) unsigned NOT NULL,
		generation varchar(36) NOT NULL,
		rule_version varchar(64) NOT NULL,
		capability varchar(64) NOT NULL,
		input_hash varchar(64) NOT NULL,
		public_text longtext NOT NULL,
		PRIMARY KEY  (post_id)
	) $charset;" );
	dbDelta( "CREATE TABLE {$wpdb->prefix}stc_search_dependency (
		generation varchar(36) NOT NULL,
		resource varchar(191) NOT NULL,
		version varchar(36) NOT NULL,
		PRIMARY KEY  (generation,resource),
		KEY resource (resource)
	) $charset;" );
	if ( $wpdb->last_error ) { return false; }
	update_option( 'stc_search_schema', '1', false );
	return true;
}

/** Include actual renderer/helper bytes, so a same-version code replacement fails closed. */
function stc_search_capability( $tools = true ) {
	static $values = array();
	if ( isset( $values[ (int) $tools ] ) ) { return $values[ (int) $tools ]; }
	$parts = array( STC_SEARCH_RULE, defined( 'STC_COMPONENT_REGISTRY_VERSION' ) ? STC_COMPONENT_REGISTRY_VERSION : '', $tools ? 'tools:' . (int) function_exists( 'stc_tools_get_place' ) : 'editorial' );
	foreach ( array( __FILE__, __DIR__ . '/search.php', __DIR__ . '/experience-components.php', __DIR__ . '/entity-links.php', __DIR__ . '/content-renderers.php' ) as $file ) { $parts[] = hash_file( 'sha256', $file ); }
	if ( $tools && defined( 'STC_TOOLS_PATH' ) && function_exists( 'stc_tools_get_place' ) ) {
		foreach ( array( 'includes/places.php', 'includes/shortcodes.php', 'data/destinations-v1.json' ) as $file ) { $parts[] = hash_file( 'sha256', STC_TOOLS_PATH . $file ); }
	}
	$values[ (int) $tools ] = hash( 'sha256', implode( '|', $parts ) );
	return $values[ (int) $tools ];
}

function stc_search_resource( $name ) { return '_stc_search_epoch_' . $name; }

/** Read epochs directly: persistent option caches must never validate stale work. */
function stc_search_watch( $name, &$dependencies ) {
	global $wpdb;
	$key = stc_search_resource( $name );
	if ( isset( $dependencies[ $key ] ) ) { return; }
	add_option( $key, wp_generate_uuid4(), '', false );
	$dependencies[ $key ] = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name=%s", $key ) );
}

function stc_search_input_hash( $post ) {
	return hash( 'sha256', wp_json_encode( array( $post->post_content, $post->post_title, $post->post_excerpt, $post->post_modified_gmt, $post->post_status, $post->post_password, $post->post_type ) ) );
}

/** SQL predicate shared by admission, public search and bounded rebuild discovery. */
function stc_search_valid_sql( $alias ) {
	global $wpdb;
	return $wpdb->prepare( "$alias.rule_version=%s AND $alias.capability IN (%s,%s)", STC_SEARCH_RULE, stc_search_capability(), stc_search_capability( false ) ) .
		" AND EXISTS (SELECT 1 FROM {$wpdb->prefix}stc_search_dependency sx WHERE sx.generation=$alias.generation)" .
		" AND NOT EXISTS (SELECT 1 FROM {$wpdb->prefix}stc_search_dependency sd LEFT JOIN {$wpdb->options} so ON so.option_name=sd.resource WHERE sd.generation=$alias.generation AND (so.option_value IS NULL OR so.option_value<>sd.version))";
}

/** Prepare can be interrupted or sent to a later commit without rereading article content. */
function stc_search_prepare( $post_id ) {
	global $wpdb;
	if ( ! stc_search_install() ) { return new WP_Error( 'search_storage', 'Search storage unavailable.' ); }
	$dependencies = array();
	stc_search_watch( 'post:' . $post_id, $dependencies );
	clean_post_cache( $post_id );
	$post = get_post( $post_id );
	if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status || '' !== $post->post_password ) { return new WP_Error( 'search_nonpublic', 'Not a public guide.' ); }
	$previous = $wpdb->get_var( $wpdb->prepare( "SELECT generation FROM {$wpdb->prefix}stc_search_projection WHERE post_id=%d", $post_id ) );
	$text = stc_public_search_text_from_content( $post->post_content, $dependencies, $post_id );
	return array( 'post_id' => (int) $post_id, 'generation' => wp_generate_uuid4(), 'previous' => $previous, 'rule_version' => STC_SEARCH_RULE, 'capability' => stc_search_capability( isset( $dependencies[ stc_search_resource( 'capability:tools' ) ] ) ), 'input_hash' => stc_search_input_hash( $post ), 'public_text' => $text, 'dependencies' => $dependencies );
}

/** Atomic conditional publication. Dependencies are immutable and installed BEFORE admission. */
function stc_search_commit( $candidate ) {
	global $wpdb;
	if ( is_wp_error( $candidate ) ) { return $candidate->get_error_code() === 'search_nonpublic' ? 'SKIP_NONPUBLIC' : 'FAILED'; }
	$c = $candidate;
	if ( $c['rule_version'] !== STC_SEARCH_RULE || $c['capability'] !== stc_search_capability( isset( $c['dependencies'][ stc_search_resource( 'capability:tools' ) ] ) ) ) { return 'SKIP_CHANGED'; }
	$table = $wpdb->prefix . 'stc_search_projection';
	$deps = $wpdb->prefix . 'stc_search_dependency';
	foreach ( $c['dependencies'] as $resource => $version ) {
		if ( false === $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $deps (generation,resource,version) VALUES (%s,%s,%s)", $c['generation'], $resource, $version ) ) ) { return 'FAILED'; }
	}
	// Each predicate is evaluated in the SAME SQL statement as publication, not read-then-write.
	$guard = $wpdb->prepare( "EXISTS (SELECT 1 FROM {$wpdb->posts} sp WHERE sp.ID=%d AND sp.post_type='post' AND sp.post_status='publish' AND sp.post_password='')", $c['post_id'] );
	foreach ( $c['dependencies'] as $resource => $version ) {
		$guard .= $wpdb->prepare( " AND EXISTS (SELECT 1 FROM {$wpdb->options} se WHERE se.option_name=%s AND se.option_value=%s)", $resource, $version );
	}
	if ( null === $c['previous'] ) {
		$sql = $wpdb->prepare( "INSERT IGNORE INTO $table (post_id,generation,rule_version,capability,input_hash,public_text) SELECT %d,%s,%s,%s,%s,%s WHERE ", $c['post_id'], $c['generation'], $c['rule_version'], $c['capability'], $c['input_hash'], $c['public_text'] ) . $guard;
	} else {
		$sql = $wpdb->prepare( "UPDATE $table SET generation=%s,rule_version=%s,capability=%s,input_hash=%s,public_text=%s WHERE post_id=%d AND generation=%s AND ", $c['generation'], $c['rule_version'], $c['capability'], $c['input_hash'], $c['public_text'], $c['post_id'], $c['previous'] ) . $guard;
	}
	$result = $wpdb->query( $sql );
	if ( false === $result ) { return 'FAILED'; }
	if ( 0 === $result ) {
		// Keep failed work for bounded maintenance; a replay may name the live generation.
		return 'SKIP_CHANGED';
	}
	if ( $c['previous'] ) { $wpdb->delete( $deps, array( 'generation' => $c['previous'] ) ); }
	// A post-publication interruption needs no rollback. Retry discovers the current row.
	return 'APPLIED';
}

function stc_search_rebuild( $post_id ) {
	global $wpdb;
	$c = stc_search_prepare( $post_id );
	if ( is_wp_error( $c ) ) { return stc_search_commit( $c ); }
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}stc_search_projection p WHERE post_id=%d AND " . stc_search_valid_sql( 'p' ), $post_id ), ARRAY_A );
	if ( $row && $row['input_hash'] === $c['input_hash'] && $row['public_text'] === $c['public_text'] ) { return 'UNCHANGED'; }
	return stc_search_commit( $c );
}

/** Invalidations are constant writes; fan-out refresh is capped at 25 per request. */
function stc_search_changed( $name ) {
	global $wpdb;
	$key = stc_search_resource( $name );
	$version = wp_generate_uuid4();
	if ( ! add_option( $key, $version, '', false ) ) {
		$wpdb->update( $wpdb->options, array( 'option_value' => $version ), array( 'option_name' => $key ) );
		wp_cache_delete( $key, 'options' );
	}
	$GLOBALS['stc_search_changed_resources'][ $key ] = true;
}

function stc_search_refresh_dependants() {
	global $wpdb;
	if ( '1' !== get_option( 'stc_search_schema' ) || empty( $GLOBALS['stc_search_changed_resources'] ) || ! empty( $GLOBALS['stc_search_refreshing'] ) ) { return; }
	$remaining = 25 - (int) ( $GLOBALS['stc_search_refreshed_count'] ?? 0 );
	if ( $remaining <= 0 ) { return; }
	$keys = array_keys( $GLOBALS['stc_search_changed_resources'] );
	$GLOBALS['stc_search_changed_resources'] = array();
	$in = implode( ',', array_fill( 0, count( $keys ), '%s' ) );
	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT p.post_id FROM {$wpdb->prefix}stc_search_projection p INNER JOIN {$wpdb->prefix}stc_search_dependency d ON d.generation=p.generation WHERE d.resource IN ($in) ORDER BY p.post_id LIMIT %d", array_merge( $keys, array( $remaining ) ) ) );
	$GLOBALS['stc_search_refreshing'] = true;
	try {
		foreach ( $ids as $id ) { stc_search_rebuild( $id ); $GLOBALS['stc_search_refreshed_count'] = 1 + (int) ( $GLOBALS['stc_search_refreshed_count'] ?? 0 ); }
	} finally { $GLOBALS['stc_search_refreshing'] = false; }
}

function stc_search_post_changed( $id, $post ) {
	if ( wp_is_post_revision( $id ) ) { return; }
	stc_search_changed( 'post:' . $id );
	$key = get_post_meta( $id, '_stc_entity_key', true );
	if ( $key ) { stc_search_changed( 'entity:' . sanitize_title( $key ) ); }
	stc_invalidate_entity_index();
	if ( 'post' === $post->post_type ) { stc_search_rebuild( $id ); }
	stc_search_refresh_dependants();
}
add_action( 'save_post', 'stc_search_post_changed', 30, 2 );
add_action( 'delete_attachment', function ( $id ) { stc_search_changed( 'post:' . $id ); }, 10 );
add_action( 'before_delete_post', function ( $id ) {
	stc_search_changed( 'post:' . $id );
	$key = get_post_meta( $id, '_stc_entity_key', true );
	$GLOBALS['stc_search_deleting_entities'][ $id ] = $key;
	if ( $key ) { stc_search_changed( 'entity:' . sanitize_title( $key ) ); }
} );
add_action( 'deleted_post', function ( $id ) {
	global $wpdb;
	// WP fires deleted_post before its final cache cleanup. Resolve from the new state.
	clean_post_cache( $id );
	// Metadata deletion hooks can rebuild while the post row still exists. Fence again
	// after the actual row deletion, even if those earlier hooks consumed the queue.
	stc_search_changed( 'post:' . $id );
	$key = $GLOBALS['stc_search_deleting_entities'][ $id ] ?? '';
	if ( $key ) { stc_search_changed( 'entity:' . sanitize_title( $key ) ); }
	stc_invalidate_entity_index();
	stc_search_refresh_dependants();
	if ( '1' === get_option( 'stc_search_schema' ) ) { $wpdb->delete( $wpdb->prefix . 'stc_search_projection', array( 'post_id' => $id ) ); }
} );

// Capture the old entity binding before it is replaced; no speculative rebuild here.
function stc_search_before_meta_change( $check, $id, $key ) {
	if ( '_stc_entity_key' === $key ) {
		$old = get_post_meta( $id, $key, true );
		if ( $old ) { stc_search_changed( 'entity:' . sanitize_title( $old ) ); }
	}
	return $check;
}
add_filter( 'update_post_metadata', 'stc_search_before_meta_change', 10, 3 );
add_filter( 'delete_post_metadata', 'stc_search_before_meta_change', 10, 3 );
function stc_search_meta_changed( $meta_id, $id, $key, $value ) {
	if ( ! in_array( $key, array( '_stc_entity_key', '_stc_entity_role', '_stc_guide_type', '_wp_attached_file', '_wp_attachment_metadata' ), true ) ) { return; }
	stc_search_changed( 'post:' . $id );
	$entity = get_post_meta( $id, '_stc_entity_key', true );
	if ( $entity ) { stc_search_changed( 'entity:' . sanitize_title( $entity ) ); }
	if ( '_stc_entity_key' === $key && is_string( $value ) ) { stc_search_changed( 'entity:' . sanitize_title( $value ) ); }
	stc_invalidate_entity_index();
	stc_search_refresh_dependants();
}
foreach ( array( 'added_post_meta', 'updated_post_meta', 'deleted_post_meta' ) as $hook ) { add_action( $hook, 'stc_search_meta_changed', 30, 4 ); }
function stc_search_terms_changed( $id ) {
	$key = get_post_meta( $id, '_stc_entity_key', true );
	if ( $key ) { stc_search_changed( 'entity:' . sanitize_title( $key ) ); stc_invalidate_entity_index(); stc_search_refresh_dependants(); }
}
add_action( 'set_object_terms', 'stc_search_terms_changed', 30 );
add_action( 'deleted_term_relationships', 'stc_search_terms_changed', 30 );

/** Catalog pointer updates affect only changed entity records, including removed records. */
function stc_search_catalog_changed( $old, $new ) {
	if ( ! function_exists( 'stc_tools_builtin_places' ) ) { return; }
	$a = $old ? get_option( 'stc_destination_catalog_' . $old ) : null;
	$b = $new ? get_option( 'stc_destination_catalog_' . $new ) : null;
	$a = is_array( $a ) ? array_column( $a['places'], null, 'entity_key' ) : stc_tools_builtin_places();
	$b = is_array( $b ) ? array_column( $b['places'], null, 'entity_key' ) : stc_tools_builtin_places();
	stc_search_catalog_records_changed( $a, $b );
}
function stc_search_catalog_records_changed( $a, $b ) {
	foreach ( array_unique( array_merge( array_keys( $a ), array_keys( $b ) ) ) as $key ) {
		if ( ( $a[ $key ] ?? null ) !== ( $b[ $key ] ?? null ) ) { stc_search_changed( 'entity:' . $key ); }
	}
	stc_search_refresh_dependants();
}
add_action( 'update_option_stc_destination_catalog_active', 'stc_search_catalog_changed', 10, 2 );
add_action( 'add_option_stc_destination_catalog_active', function ( $name, $value ) { stc_search_catalog_changed( '', $value ); }, 10, 2 );
add_action( 'delete_option', function ( $name ) {
	if ( 'stc_destination_catalog_active' === $name ) { $GLOBALS['stc_search_deleted_catalog'] = get_option( $name ); }
} );
add_action( 'deleted_option', function ( $name ) {
	if ( 'stc_destination_catalog_active' === $name ) { stc_search_catalog_changed( $GLOBALS['stc_search_deleted_catalog'] ?? '', '' ); }
} );
add_action( 'update_option_active_plugins', function () { stc_search_changed( 'capability:tools' ); } );
add_action( 'updated_option', function ( $name, $old, $new ) {
	$active = get_option( 'stc_destination_catalog_active', '' );
	if ( $active && 'stc_destination_catalog_' . $active === $name && is_array( $old ) && is_array( $new ) ) {
		stc_search_catalog_records_changed( array_column( $old['places'], null, 'entity_key' ), array_column( $new['places'], null, 'entity_key' ) );
	}
}, 10, 3 );

/** Explicit bounded backfill/recovery. Caller persists successes and conflicts separately. */
function stc_search_rebuild_batch( $after, $upper, $limit = 25 ) {
	global $wpdb;
	if ( ! stc_search_install() ) { return array( 'error' => 'FAILED' ); }
	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE ID>%d AND ID<=%d AND post_type='post' AND post_status='publish' AND post_password='' ORDER BY ID LIMIT %d", absint( $after ), absint( $upper ), min( 25, max( 1, absint( $limit ) ) ) ) );
	$out = array();
	foreach ( $ids as $id ) { $out[ $id ] = stc_search_rebuild( $id ); }
	return $out;
}
