<?php
/** Small CMS-selected editorial capabilities; no automatic article insertion. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function stc_experience_component_open( $type, $data ) {
	$id = empty( $data['anchor'] ) ? '' : ' id="' . esc_attr( sanitize_title( $data['anchor'] ) ) . '"';
	return '<section' . $id . ' class="stc-content-block stc-content-block--' . esc_attr( str_replace( '_', '-', $type ) ) . '">';
}

function stc_render_place_info_card_component( $data ) {
	$place = function_exists( 'stc_tools_get_place' ) ? stc_tools_get_place( $data['entity_key'] ?? '' ) : null;
	$guide = stc_resolve_entity_guide( $data['entity_key'] ?? '' );
	if ( ! $place && ! $guide ) { return ''; }
	$title = $data['title'] ?? ( $place ? $place['name_en'] : $guide['title'] );
	$html = stc_experience_component_open( 'place_info_card', $data ) . '<h2>' . esc_html( $title ) . '</h2>';
	if ( ! empty( $data['media_id'] ) && wp_attachment_is_image( $data['media_id'] ) ) {
		$html .= '<figure>' . wp_get_attachment_image( $data['media_id'], 'large', false, array( 'alt' => $data['alt'] ?? '', 'loading' => 'lazy', 'decoding' => 'async' ) );
		if ( ! empty( $data['caption'] ) ) { $html .= '<figcaption>' . esc_html( $data['caption'] ) . '</figcaption>'; }
		$html .= '</figure>';
	}
	if ( $place ) { $html .= '<p lang="zh">' . esc_html( $place['name_zh'] . ' · ' . $place['city_zh'] ) . '</p>'; }
	if ( ! empty( $data['description'] ) ) { $html .= '<p>' . esc_html( $data['description'] ) . '</p>'; }
	if ( $guide ) { $html .= '<p><a href="' . esc_url( $guide['url'] ) . '">Read ' . esc_html( $guide['title'] ) . '</a></p>'; }
	if ( $place && 'VERIFIED' === $place['sources']['name_zh'] ) { $html .= '<a href="' . esc_url( add_query_arg( 'entity_key', $place['entity_key'], home_url( '/tools/taxi-card/' ) ) ) . '">Show Taxi Card</a><p class="stc-field-note">Arrival details are verified separately from the place name.</p>'; }
	return $html . '</section>';
}

function stc_render_related_guides_component( $data ) {
	$ids = array_map( 'absint', (array) ( $data['post_ids'] ?? array() ) );
	foreach ( (array) ( $data['entity_keys'] ?? array() ) as $key ) {
		$guide = stc_resolve_entity_guide( $key, $data['guide_type'] ?? 'attraction-guide' );
		if ( $guide ) { $ids[] = $guide['id']; }
	}
	$ids = array_diff( array_unique( $ids ), array( get_the_ID() ) );
	if ( ! $ids ) { return ''; }
	$posts = get_posts( array( 'post__in' => array_values( $ids ), 'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false, 'numberposts' => 12, 'orderby' => 'post__in' ) );
	if ( ! $posts ) { return ''; }
	$html = stc_experience_component_open( 'related_guides', $data ) . '<h2>' . esc_html( $data['title'] ?? 'Read next' ) . '</h2><ul>';
	foreach ( $posts as $post ) { $html .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></li>'; }
	return $html . '</ul></section>';
}

function stc_render_annotated_image_component( $data ) {
	$id = absint( $data['media_id'] ?? 0 );
	if ( ! wp_attachment_is_image( $id ) ) { return ''; }
	$html = stc_experience_component_open( 'annotated_image', $data );
	if ( ! empty( $data['title'] ) ) { $html .= '<h2>' . esc_html( $data['title'] ) . '</h2>'; }
	$html .= '<figure><div class="stc-annotated-media"><a data-stc-enlarge href="' . esc_url( wp_get_attachment_url( $id ) ) . '" aria-label="Enlarge image">' . wp_get_attachment_image( $id, 'large', false, array( 'alt' => $data['alt'] ?? '', 'loading' => 'lazy', 'decoding' => 'async' ) ) . '</a>';
	foreach ( (array) ( $data['annotations'] ?? array() ) as $i => $note ) {
		// Only renderer-owned, bounded numeric coordinates become CSS. CMS cannot supply CSS.
		$x = max( 0, min( 1, (float) $note['x'] ) ) * 100;
		$y = max( 0, min( 1, (float) $note['y'] ) ) * 100;
		$html .= '<span class="stc-image-marker" aria-hidden="true" style="left:' . esc_attr( $x ) . '%;top:' . esc_attr( $y ) . '%">' . ( $i + 1 ) . '</span>';
	}
	$html .= '</div>';
	if ( ! empty( $data['caption'] ) ) { $html .= '<figcaption>' . esc_html( $data['caption'] ) . '</figcaption>'; }
	$html .= '</figure><ol class="stc-image-annotations">';
	foreach ( (array) ( $data['annotations'] ?? array() ) as $note ) { $html .= '<li>' . esc_html( $note['text'] ) . '</li>'; }
	return $html . '</ol></section>';
}

add_action( 'wp_enqueue_scripts', function () {
	if ( is_singular() && ! is_front_page() ) { wp_enqueue_style( 'stc-experience-components', get_template_directory_uri() . '/assets/css/experience-components.css', array( 'stc-main' ), STC_THEME_VERSION ); }
}, 40 );
add_action( 'after_setup_theme', function () { add_editor_style( get_template_directory_uri() . '/assets/css/experience-components.css' ); }, 30 );
