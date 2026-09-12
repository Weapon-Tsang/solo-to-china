<?php
/** Published entity links shared by collections, editorial blocks and tools. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function stc_entity_index() {
	if ( isset( $GLOBALS['stc_entity_index'] ) ) { return $GLOBALS['stc_entity_index']; }
	$index = array();
	$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false, 'numberposts' => -1, 'orderby' => array( 'date' => 'DESC', 'ID' => 'ASC' ), 'meta_query' => array( array( 'key' => '_stc_entity_key', 'compare' => 'EXISTS' ) ) ) );
	foreach ( $posts as $post ) {
		$key = sanitize_title( get_post_meta( $post->ID, '_stc_entity_key', true ) );
		$type = stc_get_guide_type_slug( $post->ID );
		$slot = $type . ':' . $key;
		$priority = 'hub' === get_post_meta( $post->ID, '_stc_entity_role', true ) ? 2 : 1;
		if ( $key && ( ! isset( $index[$slot] ) || $priority > $index[$slot]['priority'] ) ) {
			$index[$slot] = array( 'id' => $post->ID, 'title' => get_the_title( $post ), 'url' => get_permalink( $post ), 'priority' => $priority );
		}
	}
	$GLOBALS['stc_entity_index'] = $index;
	return $index;
}

function stc_resolve_entity_guide( $entity_key, $type = 'attraction-guide', $fallback_slug = '' ) {
	$index = stc_entity_index();
	$slot = $type . ':' . sanitize_title( $entity_key );
	if ( isset( $index[$slot] ) ) { return $index[$slot]; }
	if ( ! $fallback_slug ) { return null; }
	$post = get_page_by_path( sanitize_title( $fallback_slug ), OBJECT, 'post' );
	return $post && 'publish' === $post->post_status && ! $post->post_password && $type === stc_get_guide_type_slug( $post->ID ) ? array( 'id' => $post->ID, 'title' => get_the_title( $post ), 'url' => get_permalink( $post ) ) : null;
}

function stc_invalidate_entity_index() { unset( $GLOBALS['stc_entity_index'] ); }
add_action( 'save_post', 'stc_invalidate_entity_index' );
add_action( 'transition_post_status', 'stc_invalidate_entity_index' );
add_action( 'deleted_post', 'stc_invalidate_entity_index' );
add_action( 'added_post_meta', 'stc_invalidate_entity_index' );
add_action( 'updated_post_meta', 'stc_invalidate_entity_index' );
add_action( 'deleted_post_meta', 'stc_invalidate_entity_index' );

function stc_register_entity_metadata() {
	foreach ( array( '_stc_entity_key', '_stc_entity_role' ) as $key ) {
		register_post_meta( 'post', $key, array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_title', 'auth_callback' => function () { return current_user_can( 'edit_posts' ); } ) );
	}
}
add_action( 'init', 'stc_register_entity_metadata', 20 );
