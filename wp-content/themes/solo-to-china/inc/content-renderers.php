<?php
/**
 * Safe renderers for dynamic Content Contract components.
 *
 * The Theme owns presentation and delegates Ticket behavior to the Tools plugin.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validate an external component URL and require HTTPS.
 *
 * @param string $url Candidate destination.
 * @return string
 */
function stc_validate_https_component_url( $url ) {
	$url    = esc_url_raw( trim( (string) $url ) );
	$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
	$host   = wp_parse_url( $url, PHP_URL_HOST );

	if ( 'https' !== strtolower( (string) $scheme ) || empty( $host ) ) {
		return '';
	}

	return $url;
}

/**
 * Return the centrally maintained affiliate host allowlist.
 *
 * Hosts are compared as DNS labels, never by substring containment.
 *
 * @return string[]
 */
function stc_affiliate_allowed_hosts() {
	return array( 'trip.com', 'tripcdn.com', 'ctrip.com' );
}

/**
 * Determine whether a hostname is an allowlisted root or legitimate subdomain.
 *
 * @param string $host Candidate hostname.
 * @return bool
 */
function stc_is_allowed_affiliate_host( $host ) {
	$host = strtolower( rtrim( trim( (string) $host ), '.' ) );
	if ( '' === $host ) {
		return false;
	}

	foreach ( stc_affiliate_allowed_hosts() as $allowed_host ) {
		$suffix = '.' . $allowed_host;
		if ( $host === $allowed_host || ( strlen( $host ) > strlen( $suffix ) && substr( $host, -strlen( $suffix ) ) === $suffix ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Validate an official affiliate URL without accepting credentials or unsafe schemes.
 *
 * @param string $url Candidate URL.
 * @return string
 */
function stc_validate_affiliate_url( $url ) {
	$url = stc_validate_https_component_url( $url );
	if ( '' === $url ) {
		return '';
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );
	$user = wp_parse_url( $url, PHP_URL_USER );
	$pass = wp_parse_url( $url, PHP_URL_PASS );

	if ( ! stc_is_allowed_affiliate_host( $host ) || null !== $user || null !== $pass ) {
		return '';
	}

	return $url;
}

/**
 * Reject unknown shortcode/data keys before applying defaults.
 *
 * @param array<string, mixed> $attributes Input attributes.
 * @param string[]             $allowed Allowed keys.
 * @return bool
 */
function stc_commercial_attributes_are_known( $attributes, $allowed ) {
	return 0 === count( array_diff( array_keys( (array) $attributes ), $allowed ) );
}

/**
 * Upgrade the exact historical default at the commercial field boundary.
 * Editorially written disclosures are intentionally left untouched.
 *
 * @param string $disclosure Persisted disclosure text.
 * @return string
 */
function stc_normalize_commercial_disclosure( $disclosure ) {
	$disclosure = sanitize_text_field( (string) $disclosure );
	return $disclosure;
}

/** Human-facing product label; never expose the storage enum as marketing copy. */
function stc_commercial_category_label( $category ) {
	$labels = array(
		'HOTEL' => 'Hotels & Homes', 'FLIGHT' => 'Flights', 'TRAIN' => 'Trains',
		'ATTRACTION' => 'Attractions & Tickets', 'TOUR_ACTIVITY' => 'Tours & Tickets',
		'FLIGHT_HOTEL' => 'Flights & Hotels', 'CAR_RENTAL' => 'Car Rentals',
		'AIRPORT_TRANSFER' => 'Airport Transfers', 'PLANNER' => 'Trip Planning',
	);
	$key = strtoupper( str_replace( ' ', '_', sanitize_text_field( (string) $category ) ) );
	return isset( $labels[ $key ] ) ? $labels[ $key ] : ucwords( strtolower( str_replace( '_', ' ', $key ) ) );
}

/** Display-only adapter for two exact historical system title templates. */
function stc_commercial_display_title( $data ) {
	$title = (string) $data['title'];
	$destination = ! empty( $data['destination'] ) ? $data['destination'] : ( 'DESTINATION' === $data['scope_type'] ? $data['scope_key'] : '' );
	$templates = array(
		'ATTRACTION' => array( '/^Tickets and attractions for (.+)$/i', 'Find your next attraction' ),
		'TOUR_ACTIVITY' => array( '/^Tours and activities for (.+)$/i', 'Find your next experience' ),
	);
	$category = $data['product_category'];
	if ( $destination && isset( $templates[ $category ] ) && preg_match( $templates[ $category ][0], $title, $matches ) && sanitize_title( $matches[1] ) === sanitize_title( $destination ) ) {
		return $templates[ $category ][1];
	}
	return $title;
}

/** Replace only the category-matched generated filler, never editorial prose. */
function stc_commercial_display_description( $data ) {
	$description = (string) $data['description'];
	$category = (string) $data['product_category'];
	$defaults = array(
		'HOTEL' => 'Compare location, arrival access and current terms.',
		'FLIGHT' => 'Compare current flight options and fare terms.',
		'TRAIN' => 'Check the route, schedule and current ticket terms.',
		'ATTRACTION' => 'Check entry conditions and current ticket options.',
		'TOUR_ACTIVITY' => 'Check what is included and current booking terms.',
		'FLIGHT_HOTEL' => 'Compare flight and stay options for your dates.',
		'CAR_RENTAL' => 'Compare pickup terms and current rental options.',
		'AIRPORT_TRANSFER' => 'Compare pickup details and current transfer terms.',
		'PLANNER' => 'Explore options that fit your route and dates.',
	);
	$historical = 'Check current ' . strtolower( $category ) . ' details and availability before booking.';
	return isset( $defaults[ $category ] ) && $description === $historical ? $defaults[ $category ] : $description;
}

/** B-ticket headlines fixed by the requested product category. */
function stc_commercial_ticket_offer( $category ) {
	$offers = array(
		'HOTEL'            => array( 'Up to', '20% OFF', 'New users' ),
		'FLIGHT'           => array( '', '10% OFF', '' ),
		'TRAIN'            => array( '', '10% OFF', '' ),
		'AIRPORT_TRANSFER' => array( '', '15% OFF', '' ),
		'ATTRACTION'       => array( '', '10% OFF', '' ),
		'TOUR_ACTIVITY'    => array( '', '10% OFF', '' ),
	);
	return isset( $offers[ $category ] ) ? $offers[ $category ] : array();
}

/** Inline category pictograms; the claim stub uses the three filled B-reference icons. */
function stc_commercial_ticket_icon( $category ) {
	$paths = array(
		'HOTEL'            => '<g fill="currentColor" stroke="none"><path d="M2 14.5h20v5H2zM2 9h2.7v10.5H2zM5.5 11.5h13.3c1.8 0 2.9.9 2.9 2.7v1.2H5.5zM6 8.4h5.2c1.2 0 1.8.7 1.8 2v.5H6z"/><path d="M3 19h2v2H3zm16 0h2v2h-2z"/></g>',
		'FLIGHT'           => '<path fill="currentColor" stroke="none" d="M21.9 2.1c-.7-.6-1.6-.4-2.5.4l-5.3 5-9-1.2-1.6 1.5 7 3.7-5.1 5.2-2.8-.2-1.1 1.1 3.9 1.9 1.9 3.9 1.1-1.1-.2-2.8 5.2-5.1 3.7 7 1.5-1.6-1.2-9 5-5.3c.8-.9 1-1.8.4-2.5z"/>',
		'TRAIN'            => '<g fill="currentColor" stroke="none"><path d="M8 2h8v2H8zM7 4.5h10c1.2 0 2 .9 2 2v12H5v-12c0-1.1.8-2 2-2zM3 20h18v2H3z"/><path d="M7 17.5 5 21h2.5l2-3.5zm10 0 2 3.5h-2.5l-2-3.5z"/></g><g fill="#f7fbff" stroke="none"><path d="M7.5 7h9v5h-9z"/><circle cx="9" cy="15.5" r="1"/><circle cx="15" cy="15.5" r="1"/></g>',
		'AIRPORT_TRANSFER' => '<path d="M5 16h14l-2-7H7l-2 7Zm-2 0h18v4H3zM7 9l2-4h6l2 4M7 20v2m10-2v2"/>',
		'ATTRACTION'       => '<path d="M3 10h18M5 10l7-6 7 6M6 10v10m4-10v10m4-10v10M3 20h18"/>',
		'TOUR_ACTIVITY'    => '<path d="m3 20 5-11 4 6 4-9 5 14H3Zm5-11 2-4 2 3"/>',
		'PLANNER'          => '<rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 10h16m-12 5h3m3 0h2"/>',
		'FLIGHT_HOTEL'     => '<path d="M2 12h10m-8 0 3-3m-3 3 3 3m-1-5 7-4 2 1-3 3 5 1 3-2 2 1-3 4-7-1-3 4m-5 2h17M4 21v-3h16v3"/>',
		'CAR_RENTAL'      => '<path d="M4 15 6 9h12l2 6M4 15h16v5H4zM7 20v2m10-2v2M7 17h2m6 0h2M8 9l1-3h6l1 3"/>',
	);
	$path = isset( $paths[ $category ] ) ? $paths[ $category ] : '<path d="M4 6h16v12H4zM8 10h8m-8 4h5"/>';
	return '<svg data-stc-icon-category="' . esc_attr( $category ) . '" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg>';
}

/** Give each asset one stable, pseudorandom Chinese scene across cached views. */
function stc_commercial_ticket_scene( $data ) {
	$scenes = array( 'temple-of-heaven', 'great-wall', 'zhangjiajie' );
	$key = $data['affiliate_asset_id'] . '|' . $data['slot_key'];
	$index = hexdec( substr( hash( 'sha256', $key ), 0, 8 ) ) % count( $scenes );
	return $scenes[ $index ];
}

/**
 * Sanitize and validate the shared Commercial Block fields.
 *
 * @param array<string, mixed> $attributes Input attributes.
 * @param string[]             $allowed Allowed keys for this component.
 * @param string[]             $required Required non-empty keys.
 * @return array<string, string>
 */
function stc_prepare_commercial_attributes( $attributes, $allowed, $required ) {
	$attributes = (array) $attributes;
	if ( ! stc_commercial_attributes_are_known( $attributes, $allowed ) ) {
		return array();
	}

	$defaults = array_fill_keys( $allowed, '' );
	$values   = shortcode_atts( $defaults, $attributes );
	$output   = array();
	$max_lengths = array(
		'affiliate_asset_id' => 120, 'provider' => 80, 'asset_type' => 40, 'product_category' => 40,
		'title' => 160, 'description' => 500, 'price_text' => 120, 'cta_label' => 80,
		'target_url' => 2048, 'image_url' => 2048, 'alt_text' => 200, 'disclosure' => 300,
		'scope_type' => 40, 'scope_key' => 160, 'slot_key' => 120, 'placement' => 40,
		'strategy_version' => 40, 'valid_from' => 40, 'valid_until' => 40,
		'entity' => 160, 'route' => 160, 'destination' => 160, 'anchor' => 120,
	);

	foreach ( $values as $key => $value ) {
		if ( 'embed_config' === $key ) {
			$output[ $key ] = $value;
			continue;
		}
		if ( ! is_scalar( $value ) || wp_strip_all_tags( (string) $value ) !== (string) $value ) {
			return array();
		}
		$output[ $key ] = sanitize_text_field( (string) $value );
		if ( 'disclosure' === $key ) {
			$output[ $key ] = stc_normalize_commercial_disclosure( $output[ $key ] );
		}
		$length = function_exists( 'mb_strlen' ) ? mb_strlen( $output[ $key ], 'UTF-8' ) : strlen( $output[ $key ] );
		$limit  = isset( $max_lengths[ $key ] ) ? $max_lengths[ $key ] : 500;
		if ( $length > $limit ) {
			return array();
		}
	}

	foreach ( $required as $key ) {
		if ( ! isset( $output[ $key ] ) || '' === trim( (string) $output[ $key ] ) ) {
			return array();
		}
	}

	$product_categories = array( 'HOTEL', 'FLIGHT', 'TRAIN', 'ATTRACTION', 'TOUR_ACTIVITY', 'FLIGHT_HOTEL', 'CAR_RENTAL', 'AIRPORT_TRANSFER', 'PLANNER' );
	$scope_types       = array( 'ENTITY', 'ROUTE', 'AREA', 'DESTINATION', 'COUNTRY', 'CATEGORY', 'GLOBAL' );
	if ( ! in_array( $output['product_category'], $product_categories, true ) || ! in_array( $output['scope_type'], $scope_types, true ) || ! in_array( $output['placement'], array( 'contextual', 'end_resource' ), true ) ) {
		return array();
	}

	return $output;
}

/**
 * Parse and validate a structured affiliate embed configuration.
 *
 * @param mixed  $raw Raw JSON string or associative array.
 * @param string $expected_type Allowed embed type for this renderer.
 * @return array<string, mixed>
 */
function stc_parse_affiliate_embed_config( $raw, $expected_type ) {
	$config = is_array( $raw ) ? $raw : json_decode( wp_unslash( (string) $raw ), true );
	$keys   = array( 'embed_type', 'src', 'width', 'height', 'language', 'theme', 'variant' );

	if ( ! is_array( $config ) || count( $config ) !== count( $keys ) || array_diff( array_keys( $config ), $keys ) || array_diff( $keys, array_keys( $config ) ) ) {
		return array();
	}

	$width  = filter_var( $config['width'], FILTER_VALIDATE_INT );
	$height = filter_var( $config['height'], FILTER_VALIDATE_INT );
	$src    = stc_validate_affiliate_url( $config['src'] );
	if (
		$expected_type !== $config['embed_type'] || '' === $src ||
		false === $width || $width < 240 || $width > 1600 ||
		false === $height || $height < 80 || $height > 800 ||
		! in_array( $config['language'], array( 'en', 'zh-CN', 'zh-TW' ), true ) ||
		! in_array( $config['theme'], array( 'light', 'dark' ), true ) ||
		! in_array( $config['variant'], array( 'compact', 'standard' ), true )
	) {
		return array();
	}

	return array(
		'embed_type' => $expected_type,
		'src'        => $src,
		'width'      => $width,
		'height'     => $height,
		'language'   => $config['language'],
		'theme'      => $config['theme'],
		'variant'    => $config['variant'],
	);
}

/**
 * Produce data attributes for privacy-minimal impression/click attribution.
 *
 * @param array<string, string> $data Commercial data.
 * @param string                $component Component ID.
 * @param string                $variant Component variant.
 * @return string
 */
function stc_commercial_event_data_attributes( $data, $component, $variant ) {
	$post_id    = (string) get_the_ID();
	$post_status = function_exists( 'get_post_status' ) ? get_post_status( (int) $post_id ) : 'publish';
	$event_data = array(
		'component'          => $component,
		'component-variant'  => $variant,
		'affiliate-asset-id' => $data['affiliate_asset_id'],
		'provider'           => $data['provider'],
		'category'           => $data['product_category'],
		'slot-key'           => $data['slot_key'],
		'placement'          => $data['placement'],
		'strategy-version'   => $data['strategy_version'],
		'entity'             => isset( $data['entity'] ) ? $data['entity'] : '',
		'route'              => isset( $data['route'] ) ? $data['route'] : '',
		'destination'        => isset( $data['destination'] ) ? $data['destination'] : '',
		'article-id'         => 'draft' === $post_status ? '' : $post_id,
		'draft-id'           => 'draft' === $post_status ? $post_id : '',
	);
	$attributes = ' data-stc-commercial="true"';
	foreach ( $event_data as $key => $value ) {
		if ( '' !== $value ) {
			$attributes .= ' data-stc-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}
	}

	return $attributes;
}

/**
 * Render a validated commercial component shell.
 *
 * @param array<string, string> $data Commercial data.
 * @param string                $component Component ID.
 * @param string                $variant Variant ID.
 * @param string                $media_html Already escaped, renderer-owned media HTML.
 * @return string
 */
function stc_render_commercial_component_shell( $data, $component, $variant, $media_html = '' ) {
	$component_id = stc_get_component_id( isset( $data['anchor'] ) ? $data['anchor'] : '', 'stc-commercial-' );
	$title_id     = $component_id . '-title';
	$target_url   = isset( $data['target_url'] ) ? $data['target_url'] : '';
	$cta_label    = isset( $data['cta_label'] ) ? $data['cta_label'] : '';
	$category     = $data['product_category'];
	$offer        = stc_commercial_ticket_offer( $category );
	$scene        = stc_commercial_ticket_scene( $data );
	$is_planner   = 'PLANNER' === $category;
	$display_title = $is_planner ? 'Build your itinerary with Trip.Planner.' : stc_commercial_display_title( $data );
	$display_description = 'Explore more of China for less- your next adventure awaits.';
	$button_label = $offer ? __( 'Claim bonus', 'solo-to-china' ) : ( $is_planner ? __( 'Open Trip.Planner', 'solo-to-china' ) : $cta_label );

	ob_start();
	?>
	<aside id="<?php echo esc_attr( $component_id ); ?>" class="stc-dynamic-component stc-commercial-component stc-commercial-component--ticket stc-commercial-component--scene-<?php echo esc_attr( $scene ); ?> stc-commercial-component--<?php echo esc_attr( $component ); ?> stc-commercial-component--<?php echo esc_attr( $variant ); ?><?php echo $offer ? ' stc-commercial-component--offer' : ''; ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>"<?php echo stc_commercial_event_data_attributes( $data, $component, $variant ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped attributes. ?>>
		<div class="stc-dynamic-component__body">
			<?php if ( $offer ) : ?><span class="stc-commercial-component__badge"><?php echo esc_html__( 'Limited offer', 'solo-to-china' ); ?></span><?php endif; ?>
			<h2 id="<?php echo esc_attr( $title_id ); ?>" data-stc-toc-exclude><?php if ( $offer ) : ?><?php if ( $offer[0] ) : ?><span class="stc-commercial-component__offer-prefix"><?php echo esc_html( $offer[0] ); ?></span> <?php endif; ?><strong class="stc-commercial-component__offer-amount"><?php echo esc_html( $offer[1] ); ?></strong><?php else : ?><?php echo esc_html( $display_title ); ?><?php endif; ?></h2>
			<p class="stc-commercial-component__category"><?php if ( $offer && $offer[2] ) : ?><span><?php echo esc_html( $offer[2] ); ?> - </span><?php endif; ?><?php echo esc_html( stc_commercial_category_label( $category ) ); ?></p>
			<p class="stc-commercial-component__detail"><?php echo esc_html( $display_description ); ?></p>
			<?php if ( $media_html ) : ?>
				<div class="stc-commercial-component__media"><?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Renderer-owned HTML is escaped at construction. ?></div>
			<?php endif; ?>
		</div>
		<div class="stc-commercial-component__claim">
			<span class="stc-commercial-component__perforation" aria-hidden="true"></span>
			<p class="stc-dynamic-component__eyebrow"><?php echo esc_html( $data['provider'] ); ?></p>
			<div class="stc-commercial-component__claim-top">
				<div class="stc-commercial-component__products">
					<?php foreach ( array( 'FLIGHT' => 'Flights', 'HOTEL' => 'Hotels', 'TRAIN' => 'Tickets' ) as $icon_category => $icon_label ) : ?>
						<span class="stc-commercial-component__product"><?php echo stc_commercial_ticket_icon( $icon_category ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed inline SVG map. ?><span><?php echo esc_html( $icon_label ); ?></span></span>
					<?php endforeach; ?>
				</div>
				<span class="stc-commercial-component__stamp" aria-hidden="true">
					<svg viewBox="0 0 112 80" focusable="false">
						<defs><path id="<?php echo esc_attr( $component_id ); ?>-stamp-arc" d="M12 38a27 27 0 0 1 54 0"/></defs>
						<g fill="none" stroke="currentColor">
							<path d="M39 4.5 43.6 4.8 47.9 6.9 52.5 7.5 56 10.5 60.9 11.4 63.6 15.4 67.3 18.3 68.8 22.8 71 26.8 72.9 30.9 73.6 35.4 75 40 73 44.5 73.2 49.2 70.3 53 69.8 57.8 66.8 61.3 64.1 65.1 60.4 67.8 56.1 69.6 52.5 72.6 47.9 73.2 43.7 75.9 39 74.6 34.4 75.1 30.2 73 25.6 72.2 21.3 70.6 17.6 67.8 13.7 65.3 12 60.7 8.4 57.7 7.5 53.1 4.3 49.3 4.3 44.6 3.7 40 4.6 35.5 5.9 31.1 6.3 26.4 9 22.7 10.3 18 14.8 15.8 17.5 12 21.9 10.4 25.5 7.4 29.8 5.8 34.4 5.2Z" stroke-width="1.1"/>
							<circle cx="39" cy="40" r="30.5" stroke-width=".85" stroke-dasharray="37 2 24 1 31 2 21 2 28 2"/>
							<circle cx="39" cy="40" r="27.5" stroke-width=".5" stroke-dasharray=".7 2.1" opacity=".7"/>
							<path class="stc-commercial-component__stamp-tail" d="M61 18c13-6 31 7 50-2M64 27c17-5 27 7 47 1M64 38c15-8 29 5 47-3M60 49c16-8 30 5 51-3" stroke-width="1.25" stroke-linecap="round" opacity=".72"/>
							<path d="M12 51c3 5 5 8 9 11m38-50 5 6M26 9l4 2m38 47-4 5" stroke-width=".6" opacity=".35"/>
						</g>
						<g fill="currentColor" opacity=".35"><circle cx="19" cy="24" r=".6"/><circle cx="57" cy="61" r=".7"/><circle cx="27" cy="65" r=".45"/><circle cx="67" cy="33" r=".55"/><circle cx="11" cy="43" r=".45"/><circle cx="48" cy="10" r=".5"/></g>
						<text font-size="4.2" letter-spacing="1.1" fill="currentColor"><textPath href="#<?php echo esc_attr( $component_id ); ?>-stamp-arc" startOffset="50%" text-anchor="middle">SOLO TO CHINA</textPath></text>
						<text x="39" y="34" text-anchor="middle" font-size="10.5" font-weight="800" fill="currentColor"><tspan x="39">GOOD</tspan><tspan x="39" dy="11">TRIPS</tspan><tspan x="39" dy="11">AHEAD</tspan></text>
					</svg>
				</span>
			</div>
			<?php if ( $target_url && $button_label ) : ?><a class="stc-button stc-button--primary stc-dynamic-component__action" data-stc-commercial-click href="<?php echo esc_url( $target_url ); ?>" target="_blank" rel="sponsored nofollow noopener"><?php echo esc_html( $button_label ); ?> <span aria-hidden="true">&#8594;</span></a><?php endif; ?>
			<p class="stc-commercial-component__signature"><?php echo esc_html__( 'Save more. Travel further.', 'solo-to-china' ); ?></p>
		</div>
	</aside>
	<?php

	return ob_get_clean();
}

/**
 * Create a safe component ID from an optional public anchor.
 *
 * @param string $anchor Optional public anchor.
 * @param string $prefix Unique fallback prefix.
 * @return string
 */
function stc_get_component_id( $anchor, $prefix ) {
	$anchor = sanitize_title( (string) $anchor );

	return $anchor ? $anchor : wp_unique_id( $prefix );
}

/**
 * Render the Planner CTA component.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_planner_cta_component( $attributes ) {
	$attributes = shortcode_atts(
		array(
			'title'       => '',
			'description' => '',
			'cta_label'   => '',
			'target_url'  => '',
			'provider'    => '',
			'disclosure'  => '',
			'anchor'      => '',
		),
		(array) $attributes,
		'stc_planner_cta'
	);

	$title       = sanitize_text_field( $attributes['title'] );
	$description = sanitize_text_field( $attributes['description'] );
	$cta_label   = sanitize_text_field( $attributes['cta_label'] );
	$target_url  = stc_validate_https_component_url( $attributes['target_url'] );
	$provider    = sanitize_text_field( $attributes['provider'] );

	if ( '' === $title || '' === $description || '' === $cta_label || '' === $target_url ) {
		return '';
	}

	$component_id = stc_get_component_id( $attributes['anchor'], 'stc-planner-' );
	$title_id     = $component_id . '-title';

	ob_start();
	?>
	<aside id="<?php echo esc_attr( $component_id ); ?>" class="stc-dynamic-component stc-dynamic-component--planner" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<div class="stc-dynamic-component__body">
			<p class="stc-dynamic-component__eyebrow"><?php echo esc_html( $provider ? $provider : __( 'Trip planning', 'solo-to-china' ) ); ?></p>
			<h2 id="<?php echo esc_attr( $title_id ); ?>" data-stc-toc-exclude><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $description ); ?></p>
		</div>
		<a class="stc-button stc-button--primary stc-dynamic-component__action" href="<?php echo esc_url( $target_url ); ?>" target="_blank" rel="sponsored nofollow noopener"><?php echo esc_html( $cta_label ); ?></a>
	</aside>
	<?php

	return ob_get_clean();
}

/**
 * Render a contextual Ticket Booking Window and delegate its form to the Plugin.
 *
 * The historical shortcode name remains as a compatibility adapter.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_ticket_reminder_component( $attributes ) {
	$attributes = shortcode_atts(
		array(
			'attraction_slug' => '',
			'title'           => __( 'Plan your ticket timing', 'solo-to-china' ),
			'description'     => __( 'Choose a visit date to see when you should start checking tickets.', 'solo-to-china' ),
			'anchor'          => '',
		),
		(array) $attributes,
		'stc_ticket_reminder'
	);

	$attraction_slug = sanitize_title( $attributes['attraction_slug'] );
	$title           = sanitize_text_field( $attributes['title'] );
	$description     = sanitize_text_field( $attributes['description'] );

	if ( '' === $attraction_slug ) {
		return '';
	}

	$component_id = stc_get_component_id( $attributes['anchor'], 'stc-ticket-' );
	$title_id     = $component_id . '-title';

	ob_start();
	?>
	<section id="<?php echo esc_attr( $component_id ); ?>" class="stc-dynamic-component stc-dynamic-component--ticket" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<div class="stc-dynamic-component__body">
			<p class="stc-dynamic-component__eyebrow"><?php esc_html_e( 'Ticket Booking Window', 'solo-to-china' ); ?></p>
			<h2 id="<?php echo esc_attr( $title_id ); ?>" data-stc-toc-exclude><?php echo esc_html( $title ); ?></h2>
			<?php if ( $description ) : ?>
				<p><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<div class="stc-dynamic-component__tool">
			<?php if ( shortcode_exists( 'solo_to_china_ticket_tool' ) ) : ?>
				<?php echo do_shortcode( '[solo_to_china_ticket_tool attraction_slug="' . esc_attr( $attraction_slug ) . '" heading_level="3"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted Plugin renderer escapes its own output. ?>
			<?php else : ?>
				<p class="stc-dynamic-component__fallback"><?php esc_html_e( 'Ticket timing is temporarily unavailable.', 'solo-to-china' ); ?> <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'Open Tools', 'solo-to-china' ); ?></a>.</p>
			<?php endif; ?>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

/** Render the reusable Destination / Taxi Card capability through the Tools Plugin. */
function stc_render_destination_card_component( $attributes ) {
	$attributes = shortcode_atts( array( 'entity_key' => '', 'title' => 'Show this to a driver', 'description' => 'A clear Chinese destination card for taxis and ride-hailing apps.', 'anchor' => '' ), (array) $attributes, 'stc_destination_card' );
	$entity_key = sanitize_title( $attributes['entity_key'] );
	$title = sanitize_text_field( $attributes['title'] );
	$description = sanitize_text_field( $attributes['description'] );
	if ( '' === $entity_key ) { return ''; }
	$component_id = stc_get_component_id( $attributes['anchor'], 'stc-destination-' );
	$title_id = $component_id . '-title';
	ob_start(); ?>
	<section id="<?php echo esc_attr( $component_id ); ?>" class="stc-dynamic-component stc-dynamic-component--destination" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<div class="stc-dynamic-component__body"><p class="stc-dynamic-component__eyebrow"><?php esc_html_e( 'Taxi Card', 'solo-to-china' ); ?></p><h2 id="<?php echo esc_attr( $title_id ); ?>" data-stc-toc-exclude><?php echo esc_html( $title ); ?></h2><?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?></div>
		<div class="stc-dynamic-component__tool"><?php if ( shortcode_exists( 'solo_to_china_taxi_card' ) ) { echo do_shortcode( '[solo_to_china_taxi_card entity_key="' . esc_attr( $entity_key ) . '" heading_level="3"]' ); } else { echo '<p class="stc-dynamic-component__fallback">' . esc_html__( 'Taxi Card is temporarily unavailable.', 'solo-to-china' ) . '</p>'; } ?></div>
	</section>
	<?php return ob_get_clean();
}

/**
 * Render a restrained contextual affiliate CTA.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_affiliate_cta_component( $attributes ) {
	$attributes = shortcode_atts(
		array(
			'category'    => '',
			'provider'    => '',
			'title'       => '',
			'description' => '',
			'price_text'  => '',
			'cta_label'   => '',
			'target_url'  => '',
			'disclosure'  => '',
			'anchor'      => '',
		),
		(array) $attributes,
		'stc_affiliate_cta'
	);

	$category    = sanitize_text_field( $attributes['category'] );
	$provider    = sanitize_text_field( $attributes['provider'] );
	$title       = sanitize_text_field( $attributes['title'] );
	$description = sanitize_text_field( $attributes['description'] );
	$price_text  = sanitize_text_field( $attributes['price_text'] );
	$cta_label   = sanitize_text_field( $attributes['cta_label'] );
	$target_url  = stc_validate_https_component_url( $attributes['target_url'] );

	if ( '' === $category || '' === $provider || '' === $title || '' === $description || '' === $cta_label || '' === $target_url ) {
		return '';
	}

	$component_id = stc_get_component_id( $attributes['anchor'], 'stc-affiliate-' );
	$title_id     = $component_id . '-title';

	ob_start();
	?>
	<aside id="<?php echo esc_attr( $component_id ); ?>" class="stc-dynamic-component stc-dynamic-component--affiliate" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<div class="stc-dynamic-component__body">
			<p class="stc-dynamic-component__eyebrow"><?php echo esc_html( $provider ); ?></p>
			<p class="stc-commercial-component__category"><?php echo esc_html( stc_commercial_category_label( $category ) ); ?></p>
			<h2 id="<?php echo esc_attr( $title_id ); ?>" data-stc-toc-exclude><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $description ); ?></p>
			<?php if ( $price_text ) : ?>
				<p class="stc-dynamic-component__price"><?php echo esc_html( $price_text ); ?></p>
			<?php endif; ?>
		</div>
		<a class="stc-button stc-button--secondary stc-dynamic-component__action" href="<?php echo esc_url( $target_url ); ?>" target="_blank" rel="sponsored nofollow noopener"><?php echo esc_html( $cta_label ); ?></a>
	</aside>
	<?php

	return ob_get_clean();
}

/**
 * Return the exact allowed fields shared by new Commercial Blocks.
 *
 * @return string[]
 */
function stc_commercial_common_fields() {
	return array( 'affiliate_asset_id', 'provider', 'asset_type', 'product_category', 'title', 'description', 'cta_label', 'target_url', 'disclosure', 'scope_type', 'scope_key', 'slot_key', 'placement', 'strategy_version', 'entity', 'route', 'destination', 'anchor' );
}

/**
 * Return fields that must be present on every new Commercial Block.
 *
 * @return string[]
 */
function stc_commercial_common_required_fields() {
	return array( 'affiliate_asset_id', 'provider', 'asset_type', 'product_category', 'title', 'description', 'scope_type', 'scope_key', 'slot_key', 'placement', 'strategy_version' );
}

/**
 * Render the high-intent affiliate booking card.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_affiliate_booking_card_component( $attributes ) {
	$allowed  = array_merge( stc_commercial_common_fields(), array( 'price_text' ) );
	$required = array_merge( stc_commercial_common_required_fields(), array( 'cta_label', 'target_url' ) );
	$data     = stc_prepare_commercial_attributes( $attributes, $allowed, $required );
	if ( ! $data || ! in_array( $data['asset_type'], array( 'DEEP_LINK', 'CATEGORY_LINK' ), true ) ) {
		return '';
	}

	$data['target_url'] = stc_validate_affiliate_url( $data['target_url'] );
	if ( '' === $data['target_url'] ) {
		return '';
	}

	return stc_render_commercial_component_shell( $data, 'affiliate_booking_card', 'default' );
}

/**
 * Render an affiliate search link or structured search box.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_affiliate_search_card_component( $attributes ) {
	$allowed  = array_merge( stc_commercial_common_fields(), array( 'embed_config' ) );
	$required = array_merge( stc_commercial_common_required_fields(), array( 'cta_label' ) );
	$data     = stc_prepare_commercial_attributes( $attributes, $allowed, $required );
	if ( ! $data || 'SEARCH_BOX' !== $data['asset_type'] ) {
		return '';
	}

	$has_link  = '' !== $data['target_url'];
	$has_embed = ! empty( $data['embed_config'] );
	if ( $has_link === $has_embed ) {
		return '';
	}

	if ( $has_link ) {
		$data['target_url'] = stc_validate_affiliate_url( $data['target_url'] );
		return $data['target_url'] ? stc_render_commercial_component_shell( $data, 'affiliate_search_card', 'link' ) : '';
	}

	$config = stc_parse_affiliate_embed_config( $data['embed_config'], 'search_box' );
	if ( ! $config ) {
		return '';
	}
	$data['target_url'] = '';
	$media = sprintf(
		'<iframe class="stc-commercial-component__embed" src="%1$s" title="%2$s" width="%3$d" height="%4$d" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-forms allow-scripts allow-popups allow-popups-to-escape-sandbox"></iframe>',
		esc_url( $config['src'] ),
		esc_attr( $data['cta_label'] ),
		$config['width'],
		$config['height']
	);

	return stc_render_commercial_component_shell( $data, 'affiliate_search_card', 'search_box', $media );
}

/**
 * Render a static or structured dynamic affiliate banner.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_affiliate_banner_component( $attributes ) {
	$allowed  = array_merge( stc_commercial_common_fields(), array( 'image_url', 'alt_text', 'embed_config' ) );
	$required = array_merge( stc_commercial_common_required_fields(), array( 'cta_label', 'target_url' ) );
	$data     = stc_prepare_commercial_attributes( $attributes, $allowed, $required );
	if ( ! $data || ! in_array( $data['asset_type'], array( 'STATIC_BANNER', 'DYNAMIC_BANNER' ), true ) || 'end_resource' !== $data['placement'] ) {
		return '';
	}

	$data['target_url'] = stc_validate_affiliate_url( $data['target_url'] );
	if ( '' === $data['target_url'] ) {
		return '';
	}

	if ( 'STATIC_BANNER' === $data['asset_type'] ) {
		if ( '' === $data['image_url'] || '' === $data['alt_text'] || ! empty( $data['embed_config'] ) ) {
			return '';
		}
		$image_url = stc_validate_affiliate_url( $data['image_url'] );
		if ( '' === $image_url ) {
			return '';
		}
		$media = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $data['alt_text'] ) . '" loading="lazy" decoding="async">';
		return stc_render_commercial_component_shell( $data, 'affiliate_banner', 'static', $media );
	}

	if ( '' !== $data['image_url'] || '' !== $data['alt_text'] ) {
		return '';
	}
	$config = stc_parse_affiliate_embed_config( $data['embed_config'], 'dynamic_banner' );
	if ( ! $config ) {
		return '';
	}
	$media = sprintf(
		'<iframe class="stc-commercial-component__embed" src="%1$s" title="%2$s" width="%3$d" height="%4$d" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-forms allow-scripts allow-popups allow-popups-to-escape-sandbox"></iframe>',
		esc_url( $config['src'] ),
		esc_attr( $data['title'] ),
		$config['width'],
		$config['height']
	);

	return stc_render_commercial_component_shell( $data, 'affiliate_banner', 'dynamic', $media );
}

/**
 * Validate an optional ISO-compatible promotion window.
 *
 * @param string $valid_from Inclusive start.
 * @param string $valid_until Inclusive end.
 * @return bool
 */
function stc_commercial_promotion_is_active( $valid_from, $valid_until ) {
	$date_time_pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})$/';
	if ( ( $valid_from && ! preg_match( $date_time_pattern, $valid_from ) ) || ( $valid_until && ! preg_match( $date_time_pattern, $valid_until ) ) ) {
		return false;
	}
	$from = $valid_from ? strtotime( $valid_from ) : false;
	$until = $valid_until ? strtotime( $valid_until ) : false;
	if ( ( $valid_from && false === $from ) || ( $valid_until && false === $until ) || ( false !== $from && false !== $until && $from > $until ) ) {
		return false;
	}

	$now = time();
	return ( false === $from || $now >= $from ) && ( false === $until || $now <= $until );
}

/**
 * Render a time-bounded affiliate promotion card.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_affiliate_promotion_card_component( $attributes ) {
	$allowed  = array_merge( stc_commercial_common_fields(), array( 'price_text', 'valid_from', 'valid_until' ) );
	$required = array_merge( stc_commercial_common_required_fields(), array( 'cta_label', 'target_url' ) );
	$data     = stc_prepare_commercial_attributes( $attributes, $allowed, $required );
	if ( ! $data || 'PROMOTION' !== $data['asset_type'] || ! stc_commercial_promotion_is_active( $data['valid_from'], $data['valid_until'] ) ) {
		return '';
	}

	$data['target_url'] = stc_validate_affiliate_url( $data['target_url'] );
	if ( '' === $data['target_url'] ) {
		return '';
	}

	return stc_render_commercial_component_shell( $data, 'affiliate_promotion_card', 'default' );
}

/**
 * Register public shortcode adapters for Contract dynamic components.
 *
 * @return void
 */
function stc_register_content_component_shortcodes() {
	add_shortcode( 'stc_planner_cta', 'stc_render_planner_cta_component' );
	add_shortcode( 'stc_ticket_reminder', 'stc_render_ticket_reminder_component' );
	add_shortcode( 'stc_destination_card', 'stc_render_destination_card_component' );
	add_shortcode( 'stc_affiliate_cta', 'stc_render_affiliate_cta_component' );
	add_shortcode( 'stc_affiliate_booking_card', 'stc_render_affiliate_booking_card_component' );
	add_shortcode( 'stc_affiliate_search_card', 'stc_render_affiliate_search_card_component' );
	add_shortcode( 'stc_affiliate_banner', 'stc_render_affiliate_banner_component' );
	add_shortcode( 'stc_affiliate_promotion_card', 'stc_render_affiliate_promotion_card_component' );
}
add_action( 'init', 'stc_register_content_component_shortcodes' );

/** Render a useful fallback for stored tool shortcodes if the plugin is inactive. */
add_action('init',function(){
 foreach(array('solo_to_china_place_finder'=>'Photo identification','solo_to_china_taxi_card'=>'Taxi Card','solo_to_china_ticket_tool'=>'Ticket timing','solo_to_china_tools_directory'=>'Interactive tools') as $code=>$label) {
  if(!shortcode_exists($code)) {add_shortcode($code,function() use($label){return '<p class="stc-dynamic-component__fallback">'.esc_html($label.' is temporarily unavailable. ').'<a href="'.esc_url(home_url('/survival-kit/')).'">Browse practical travel guides</a>.</p>';});}
 }
},99);
