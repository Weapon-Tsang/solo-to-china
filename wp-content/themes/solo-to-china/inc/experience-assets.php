<?php
/** Responsive bundled images and pre-head capability discovery. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function stc_render_theme_image( $stem, $alt = '', $hero = false ) {
	$path = get_template_directory() . '/assets/images/';
	static $manifest;
	if ( null === $manifest ) { $manifest = json_decode( file_get_contents( $path . 'responsive-images.json' ), true ); }
	$widths = $hero ? array( 640, 960, 1600 ) : array( 320, 480, 720 );
	$base = get_template_directory_uri() . '/assets/images/';
	$srcset = array();
	foreach ( $widths as $width ) {
		$file = $stem . '-' . $width . '.webp';
		if ( isset( $manifest[$file] ) ) { $srcset[] = $base . $file . ' ' . $width . 'w'; }
	}
	$file = $stem . '-' . $widths[0] . '.webp';
	if ( ! isset( $manifest[$file] ) ) { return; }
	$size = $manifest[$file];
	echo '<img class="' . ( $hero ? 'stc-hero__image' : 'stc-card-image' ) . '" src="' . esc_url( $base . $file ) . '" srcset="' . esc_attr( implode( ', ', $srcset ) ) . '" sizes="' . ( $hero ? '100vw' : '(max-width: 840px) calc(50vw - 26px), 300px' ) . '" width="' . (int) $size['width'] . '" height="' . (int) $size['height'] . '" alt="' . esc_attr( $alt ) . '" decoding="async" ' . ( $hero ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"' ) . '>';
}

/** Include reusable and nested Gutenberg blocks, without rendering the_content. */
function stc_asset_content( $content, &$seen = array() ) {
	$result = (string) $content;
	preg_match_all( '/\[stc_cms_component\s+payload="([A-Za-z0-9_-]+)"\]/', $content, $payloads );
	foreach ( $payloads[1] as $encoded ) {
		$block = stc_cms_base64url_decode( $encoded );
		if ( empty( $block['type'] ) ) { continue; }
		if ( 'destination_card' === $block['type'] ) { $result .= '[stc_destination_card]'; }
		if ( 'ticket_reminder' === $block['type'] ) { $result .= '[stc_ticket_reminder]'; }
		if ( 0 === strpos( $block['type'], 'affiliate_' ) ) { $result .= '[stc_affiliate]'; }
	}
	foreach ( parse_blocks( $content ) as $block ) {
		if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
			$id = (int) $block['attrs']['ref'];
			if ( ! isset( $seen[$id] ) ) {
				$seen[$id] = true;
				$reusable = get_post( $id );
				if ( $reusable ) { $result .= stc_asset_content( $reusable->post_content, $seen ); }
			}
		}
		if ( ! empty( $block['innerBlocks'] ) ) { $result .= stc_asset_content( serialize_blocks( $block['innerBlocks'] ), $seen ); }
	}
	return $result;
}

function stc_page_asset_content() {
	$post = get_post();
	return is_singular() && $post ? stc_asset_content( $post->post_content ) : '';
}
