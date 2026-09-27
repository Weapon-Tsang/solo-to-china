<?php
/** Derived-only, explicit-manifest runner. Load via an authorized WP CLI/eval context.
 * No web route, implicit site scan, content update, or automatic production execution.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function stc_search_backfill_manifest( $ids ) {
	$ids = array_values( array_unique( array_map( 'absint', $ids ) ) );
	if ( ! $ids || count( $ids ) > 25 || in_array( 0, $ids, true ) ) { throw new InvalidArgumentException( 'Use 1 to 25 explicit post IDs.' ); }
	$items = array();
	foreach ( $ids as $id ) {
		$c = stc_search_prepare( $id );
		$items[ $id ] = is_wp_error( $c ) ? array( 'error' => $c->get_error_code() ) : $c;
	}
	return array( 'rule' => STC_SEARCH_RULE, 'items' => $items );
}

/** Checkpoint is a per-ID ledger, never an offset that silently passes a conflict. */
function stc_search_backfill_apply( $manifest, $checkpoint_path ) {
	global $wpdb;
	if ( STC_SEARCH_RULE !== ( $manifest['rule'] ?? '' ) || empty( $manifest['items'] ) || count( $manifest['items'] ) > 25 ) { throw new InvalidArgumentException( 'Invalid or obsolete manifest.' ); }
	$lock = fopen( $checkpoint_path . '.lock', 'c' );
	if ( ! $lock || ! flock( $lock, LOCK_EX | LOCK_NB ) ) { throw new RuntimeException( 'Checkpoint busy.' ); }
	$ledger = array( 'manifest_hash' => hash( 'sha256', wp_json_encode( $manifest ) ), 'completed' => array(), 'conflicts' => array(), 'failed' => array() );
	try {
		foreach ( $manifest['items'] as $id => $candidate ) {
			if ( isset( $candidate['error'] ) ) { $status = 'search_nonpublic' === $candidate['error'] ? 'SKIP_NONPUBLIC' : 'FAILED'; }
			else {
				// Revalidate even after a previous APPLIED checkpoint; a later edit wins.
				$current = $wpdb->get_var( $wpdb->prepare( "SELECT generation FROM {$wpdb->prefix}stc_search_projection p WHERE post_id=%d AND generation=%s AND " . stc_search_valid_sql( 'p' ), $id, $candidate['generation'] ) );
				$status = $current ? 'UNCHANGED' : stc_search_commit( $candidate );
			}
			$bucket = in_array( $status, array( 'APPLIED', 'UNCHANGED' ), true ) ? 'completed' : ( 'FAILED' === $status ? 'failed' : 'conflicts' );
			$ledger[ $bucket ][ $id ] = array( 'status' => $status, 'generation' => $candidate['generation'] ?? null );
			$temp = $checkpoint_path . '.' . wp_generate_uuid4() . '.tmp';
			if ( false === file_put_contents( $temp, wp_json_encode( $ledger, JSON_PRETTY_PRINT ), LOCK_EX ) || ! rename( $temp, $checkpoint_path ) ) { throw new RuntimeException( 'Checkpoint could not be persisted; replay this exact manifest.' ); }
		}
	} finally { flock( $lock, LOCK_UN ); fclose( $lock ); }
	return $ledger;
}
