<?php
/** Public guide search. The main WordPress query remains the source of results. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once __DIR__ . '/search-projection.php';

/** Normalize only the public search term before WP_Query parses it. */
function stc_normalize_search_request( $vars ) {
	if ( is_admin() || ! array_key_exists( 's', $vars ) || defined( 'STC_CMS_SCOPED_PREVIEW' ) ) {
		return $vars;
	}

	$raw   = $vars['s'];
	$error = '';
	$term  = '';
	if ( ! is_string( $raw ) ) {
		$error = 'invalid';
	} else {
		$unescaped = wp_unslash( $raw );
		$term      = wp_check_invalid_utf8( $unescaped, true );
		if ( '' === $term && '' !== $unescaped ) {
			$error = 'invalid';
		} else {
			$term = preg_replace( '/[\p{Cc}\p{Cf}]+/u', ' ', $term );
			$term = trim( sanitize_text_field( $term ) );
			$length = function_exists( 'mb_strlen' ) ? mb_strlen( $term, 'UTF-8' ) : preg_match_all( '/./us', $term );
			if ( false === $length || $length > 120 ) {
				$error = 'long';
			}
		}
	}

	$GLOBALS['stc_search_input_state'] = array( 'term' => $error ? '' : $term, 'error' => $error );
	$vars['s'] = $error ? '' : $term;
	// A search URL cannot turn into a preview or a single-object request.
	foreach ( array( 'p', 'page_id', 'name', 'pagename', 'attachment', 'attachment_id', 'preview', 'post_type', 'post_status', 'post__in', 'post__not_in', 'include', 'perm', 'posts_per_page', 'offset', 'orderby', 'order' ) as $key ) {
		unset( $vars[ $key ] );
	}
	return $vars;
}
add_filter( 'request', 'stc_normalize_search_request', 20 );

/** Enforce the public collection even for logged-in administrators. */
function stc_scope_public_search( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() || defined( 'STC_CMS_SCOPED_PREVIEW' ) || ! empty( $GLOBALS['stc_cms_preview_ticket'] ) ) {
		return;
	}
	$query->set( 'post_type', 'post' );
	$query->set( 'post_status', 'publish' );
	$query->set( 'has_password', false );
	$query->set( 'posts_per_page', 12 );
	$query->set( 'ignore_sticky_posts', true );
	$query->set( 'no_found_rows', false );
	$query->set( 'suppress_filters', false );
	$query->set( 'orderby', '' ); // Let WordPress rank search terms before date.
	$query->set( 'post__in', array() );
	$query->set( 'post__not_in', array() );
	$state = $GLOBALS['stc_search_input_state'] ?? array();
	if ( ! empty( $state['error'] ) || '' === trim( (string) $query->get( 's' ) ) ) {
		$query->set( 'post__in', array( 0 ) );
	}
}
add_action( 'pre_get_posts', 'stc_scope_public_search', PHP_INT_MAX );

/** Extract audited stored text without executing shortcodes or querying external data. */
function stc_public_search_text_from_content( $content, &$dependencies = array(), $post_id = 0 ) {
	// Hidden source markup must be removed before inspecting any embedded payload.
	$content = preg_replace( '/<!--.*?-->/s', ' ', $content );
	$content = preg_replace( '/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $content );
	$visible_dynamic_text = array();
	if ( preg_match_all( '/' . get_shortcode_regex( array( 'stc_cms_component', 'stc_destination_card', 'stc_ticket_reminder' ) ) . '/s', $content, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $match ) {
			if ( '[' === $match[1] && ']' === $match[6] ) { continue; }
			$attributes = shortcode_parse_atts( $match[3] );
			if ( 'stc_cms_component' === $match[2] ) {
				$block = stc_cms_base64url_decode( $attributes['payload'] ?? '' );
				if ( ! $block || is_wp_error( stc_cms_validate_page_block( $block, 0 ) ) ) { continue; }
				$type = $block['type'];
				$data = $block['data'];
			} else {
				$type = 'stc_destination_card' === $match[2] ? 'destination_card' : 'ticket_reminder';
				$data = $attributes;
			}
			// These introductory fields render even when the delegated tool is unavailable.
			if ( in_array( $type, array( 'destination_card', 'ticket_reminder' ), true ) ) {
				$key = 'destination_card' === $type ? 'entity_key' : 'attraction_slug';
				if ( '' !== sanitize_title( $data[ $key ] ?? '' ) ) {
					foreach ( array( 'title', 'description' ) as $field ) {
						if ( ! empty( $data[ $field ] ) ) { $visible_dynamic_text[] = sanitize_text_field( $data[ $field ] ); }
					}
					if ( 'destination_card' === $type ) {
						stc_search_watch( 'capability:tools', $dependencies );
						stc_search_watch( 'entity:' . sanitize_title( $data[ $key ] ), $dependencies );
						$place = function_exists( 'stc_tools_get_place' ) ? stc_tools_get_place( $data[ $key ] ) : null;
						if ( $place && shortcode_exists( 'solo_to_china_taxi_card' ) && 'VERIFIED' === $place['sources']['name_zh'] ) {
							foreach ( array( 'name_en', 'name_zh', 'city_en', 'city_zh' ) as $field ) { $visible_dynamic_text[] = $place[ $field ]; }
							foreach ( array( 'dropoff' => 'recommended_dropoff_zh', 'entrance' => 'recommended_entrance_zh', 'address' => 'verified_address_zh', 'arrival_note' => 'arrival_note' ) as $source => $field ) {
								if ( 'VERIFIED' === ( $place['sources'][ $source ] ?? '' ) ) { $visible_dynamic_text[] = $place[ $field ]; }
							}
						}
					}
				}
				continue;
			}
			if ( 'place_info_card' === $type ) {
				stc_search_watch( 'capability:tools', $dependencies );
				$key = sanitize_title( $data['entity_key'] ?? '' );
				stc_search_watch( 'entity:' . $key, $dependencies );
				stc_invalidate_entity_index();
				$place = function_exists( 'stc_tools_get_place' ) ? stc_tools_get_place( $key ) : null;
				$guide = stc_resolve_entity_guide( $key );
				if ( ! $place && ! $guide ) { continue; }
				$visible_dynamic_text[] = $data['title'] ?? ( $place ? $place['name_en'] : $guide['title'] );
				$visible_dynamic_text[] = $data['description'] ?? '';
				if ( $place ) { $visible_dynamic_text[] = $place['name_zh'] . ' ' . $place['city_zh']; }
				if ( $guide ) { $visible_dynamic_text[] = $guide['title']; }
				if ( ! empty( $data['media_id'] ) ) {
					stc_search_watch( 'post:' . absint( $data['media_id'] ), $dependencies );
					if ( wp_attachment_is_image( $data['media_id'] ) ) { $visible_dynamic_text[] = $data['caption'] ?? ''; }
				}
				continue;
			}
			if ( 'related_guides' === $type ) {
				$ids = array_map( 'absint', (array) ( $data['post_ids'] ?? array() ) );
				foreach ( (array) ( $data['entity_keys'] ?? array() ) as $key ) {
					stc_search_watch( 'entity:' . sanitize_title( $key ), $dependencies );
					stc_invalidate_entity_index();
					$guide = stc_resolve_entity_guide( $key, $data['guide_type'] ?? 'attraction-guide' );
					if ( $guide ) { $ids[] = $guide['id']; }
				}
				$ids = array_values( array_diff( array_unique( $ids ), array( $post_id ) ) );
				foreach ( $ids as $id ) { stc_search_watch( 'post:' . $id, $dependencies ); }
				$posts = $ids ? get_posts( array( 'post__in' => $ids, 'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false, 'numberposts' => 12, 'orderby' => 'post__in' ) ) : array();
				if ( $posts ) {
					$visible_dynamic_text[] = $data['title'] ?? 'Read next';
					foreach ( $posts as $related ) { $visible_dynamic_text[] = get_the_title( $related ); }
				}
				continue;
			}
			if ( 'annotated_image' === $type ) { stc_search_watch( 'post:' . absint( $data['media_id'] ?? 0 ), $dependencies ); }
			if ( 'annotated_image' !== $type || ! wp_attachment_is_image( $data['media_id'] ?? 0 ) ) { continue; }
			foreach ( array( 'title', 'caption' ) as $field ) {
				if ( ! empty( $data[ $field ] ) ) {
					$visible_dynamic_text[] = $data[ $field ];
				}
			}
			foreach ( (array) ( $data['annotations'] ?? array() ) as $note ) {
				$visible_dynamic_text[] = $note['text'];
			}
		}
	}
	$content = strip_shortcodes( $content );
	$content = preg_replace( '/\[[^\]]+\]/s', ' ', $content );
	$content = html_entity_decode( wp_strip_all_tags( $content ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) . ' ' . implode( ' ', $visible_dynamic_text );
	$content = preg_replace( '/\s+/u', ' ', $content );
	return is_string( $content ) ? trim( $content ) : '';
}

/** Refresh the bounded projection when one guide changes public state. */
function stc_refresh_public_search_text( $post_id, $post ) {
	// Historical caller compatibility; never publish a caller's stale WP_Post object.
	return stc_search_rebuild( $post_id );
}

/** Use the projection in place of raw post_content, which contains private block attributes. */
function stc_public_search_sql( $search, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() || defined( 'STC_CMS_SCOPED_PREVIEW' ) || ! empty( $GLOBALS['stc_cms_preview_ticket'] ) ) {
		return $search;
	}
	global $wpdb;
	$term = trim( (string) $query->get( 's' ) );
	if ( '' === $term || ! empty( $GLOBALS['stc_search_input_state']['error'] ) ) {
		return $search;
	}
	$terms = $query->get( 'search_terms' );
	if ( ! is_array( $terms ) || ! $terms ) {
		$terms = array( $term );
	}
	$clauses = array();
	$body = '0=1';
	if ( '1' === get_option( 'stc_search_schema' ) ) {
		$body = "EXISTS (SELECT 1 FROM {$wpdb->prefix}stc_search_projection AS stc_search_text WHERE stc_search_text.post_id={$wpdb->posts}.ID AND " . stc_search_valid_sql( 'stc_search_text' ) . ' AND stc_search_text.public_text LIKE %s)';
	}
	foreach ( $terms as $part ) {
		$like = '%' . $wpdb->esc_like( $part ) . '%';
		$body_part = '0=1' === $body ? $body : $wpdb->prepare( $body, $like );
		$clauses[] = $wpdb->prepare( "({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR ", $like, $like ) . $body_part . ')';
	}
	return ' AND (' . implode( ' AND ', $clauses ) . ') ';
}
add_filter( 'posts_search', 'stc_public_search_sql', PHP_INT_MAX, 2 );

/** Exclude password-protected posts at SQL level so counts and pagination agree. */
function stc_public_search_where( $where, $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() && ! defined( 'STC_CMS_SCOPED_PREVIEW' ) && empty( $GLOBALS['stc_cms_preview_ticket'] ) ) {
		global $wpdb;
		$where .= " AND {$wpdb->posts}.post_password = ''";
	}
	return $where;
}
add_filter( 'posts_where', 'stc_public_search_where', PHP_INT_MAX, 2 );

/** Make equal relevance/date scores deterministic across pages. */
function stc_public_search_orderby( $orderby, $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() && ! defined( 'STC_CMS_SCOPED_PREVIEW' ) && empty( $GLOBALS['stc_cms_preview_ticket'] ) ) {
		global $wpdb;
		$orderby .= ", {$wpdb->posts}.ID DESC";
	}
	return $orderby;
}
add_filter( 'posts_orderby', 'stc_public_search_orderby', PHP_INT_MAX, 2 );

/** A failed database search must not appear as a valid zero-result search. */
function stc_capture_search_query_error( $posts, $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		global $wpdb;
		if ( $wpdb->last_error ) {
			$GLOBALS['stc_search_query_failed'] = true;
		}
	}
	return $posts;
}
add_filter( 'posts_results', 'stc_capture_search_query_error', 10, 2 );

function stc_search_response_status() {
	if ( ! is_search() ) {
		return;
	}
	if ( ! empty( $GLOBALS['stc_search_query_failed'] ) ) {
		status_header( 503 );
		nocache_headers();
	} elseif ( ! empty( $GLOBALS['stc_search_input_state']['error'] ) ) {
		status_header( 400 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'stc_search_response_status' );

/** One form template is used in the header, lists, results, and dialog. */
function stc_render_search_form( $class = '' ) {
	get_search_form( array( 'class' => $class ) );
}

function stc_render_header_search() {
	echo '<div class="stc-header-search">';
	echo '<div class="stc-header-search__form">';
	stc_render_search_form( 'stc-search-form--header' );
	echo '</div>';
	echo '<a class="stc-search-trigger" href="' . esc_url( home_url( '/?s=' ) ) . '" aria-label="' . esc_attr__( 'Search guides', 'solo-to-china' ) . '" data-stc-search-trigger>';
	echo '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="10.75" cy="10.75" r="6.75"/><path d="m16 16 5 5"/></svg>';
	echo '</a></div>';
}

function stc_render_search_dialog() {
	echo '<dialog class="stc-search-dialog" aria-labelledby="stc-search-dialog-title" data-stc-search-dialog>';
	echo '<div class="stc-search-dialog__head"><h2 id="stc-search-dialog-title">' . esc_html__( 'Search guides', 'solo-to-china' ) . '</h2>';
	echo '<button type="button" class="stc-search-dialog__close" aria-label="' . esc_attr__( 'Close search', 'solo-to-china' ) . '" data-stc-search-close>&times;</button></div>';
	stc_render_search_form( 'stc-search-form--dialog' );
	echo '</dialog>';
}
