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

	ob_start();
	?>
	<aside id="<?php echo esc_attr( $component_id ); ?>" class="stc-dynamic-component stc-commercial-component stc-commercial-component--<?php echo esc_attr( $component ); ?> stc-commercial-component--<?php echo esc_attr( $variant ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>"<?php echo stc_commercial_event_data_attributes( $data, $component, $variant ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped attributes. ?>>
		<div class="stc-dynamic-component__body">
			<p class="stc-dynamic-component__eyebrow"><?php echo esc_html( $data['provider'] . ' · ' . str_replace( '_', ' ', $data['product_category'] ) ); ?></p>
			<h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $data['title'] ); ?></h2>
			<p><?php echo esc_html( $data['description'] ); ?></p>
			<?php if ( $media_html ) : ?>
				<div class="stc-commercial-component__media"><?php echo $media_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Renderer-owned HTML is escaped at construction. ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $data['price_text'] ) ) : ?>
				<p class="stc-dynamic-component__price"><?php echo esc_html( $data['price_text'] ); ?></p>
			<?php endif; ?>
			<p class="stc-dynamic-component__disclosure"><?php echo esc_html( $data['disclosure'] ); ?></p>
		</div>
		<?php if ( $target_url && $cta_label ) : ?>
			<a class="stc-button stc-button--secondary stc-dynamic-component__action" data-stc-commercial-click href="<?php echo esc_url( $target_url ); ?>" target="_blank" rel="sponsored nofollow noopener"><?php echo esc_html( $cta_label ); ?></a>
		<?php endif; ?>
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
	$disclosure  = sanitize_text_field( $attributes['disclosure'] );

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
			<h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $description ); ?></p>
			<?php if ( $disclosure ) : ?>
				<p class="stc-dynamic-component__disclosure"><?php echo esc_html( $disclosure ); ?></p>
			<?php endif; ?>
		</div>
		<a class="stc-button stc-button--primary stc-dynamic-component__action" href="<?php echo esc_url( $target_url ); ?>" target="_blank" rel="sponsored nofollow noopener"><?php echo esc_html( $cta_label ); ?></a>
	</aside>
	<?php

	return ob_get_clean();
}

/**
 * Render a contextual Ticket Reminder wrapper and delegate its form to the Plugin.
 *
 * @param array<string, mixed> $attributes Shortcode attributes.
 * @return string
 */
function stc_render_ticket_reminder_component( $attributes ) {
	$attributes = shortcode_atts(
		array(
			'attraction_slug' => '',
			'title'           => __( 'Plan your ticket timing', 'solo-to-china' ),
			'description'     => __( 'Choose a visit date to see when to check availability and save a local reminder.', 'solo-to-china' ),
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
			<p class="stc-dynamic-component__eyebrow"><?php esc_html_e( 'Ticket reminder', 'solo-to-china' ); ?></p>
			<h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $description ) : ?>
				<p><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<div class="stc-dynamic-component__tool">
			<?php if ( shortcode_exists( 'solo_to_china_ticket_tool' ) ) : ?>
				<?php echo do_shortcode( '[solo_to_china_ticket_tool attraction_slug="' . esc_attr( $attraction_slug ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted Plugin renderer escapes its own output. ?>
			<?php else : ?>
				<p class="stc-dynamic-component__fallback"><?php esc_html_e( 'Ticket timing is temporarily unavailable.', 'solo-to-china' ); ?> <a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>"><?php esc_html_e( 'Open Tools', 'solo-to-china' ); ?></a>.</p>
			<?php endif; ?>
		</div>
	</section>
	<?php

	return ob_get_clean();
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
	$disclosure  = sanitize_text_field( $attributes['disclosure'] );

	if ( '' === $category || '' === $provider || '' === $title || '' === $description || '' === $cta_label || '' === $target_url ) {
		return '';
	}

	$component_id = stc_get_component_id( $attributes['anchor'], 'stc-affiliate-' );
	$title_id     = $component_id . '-title';

	ob_start();
	?>
	<aside id="<?php echo esc_attr( $component_id ); ?>" class="stc-dynamic-component stc-dynamic-component--affiliate" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<div class="stc-dynamic-component__body">
			<p class="stc-dynamic-component__eyebrow"><?php echo esc_html( $category . ' · ' . $provider ); ?></p>
			<h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $description ); ?></p>
			<?php if ( $price_text ) : ?>
				<p class="stc-dynamic-component__price"><?php echo esc_html( $price_text ); ?></p>
			<?php endif; ?>
			<p class="stc-dynamic-component__disclosure"><?php echo esc_html( $disclosure ? $disclosure : __( 'SoloToChina may earn a commission from this link at no extra cost to you.', 'solo-to-china' ) ); ?></p>
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
	return array( 'affiliate_asset_id', 'provider', 'asset_type', 'product_category', 'title', 'description', 'disclosure', 'scope_type', 'scope_key', 'slot_key', 'placement', 'strategy_version' );
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
	add_shortcode( 'stc_affiliate_cta', 'stc_render_affiliate_cta_component' );
	add_shortcode( 'stc_affiliate_booking_card', 'stc_render_affiliate_booking_card_component' );
	add_shortcode( 'stc_affiliate_search_card', 'stc_render_affiliate_search_card_component' );
	add_shortcode( 'stc_affiliate_banner', 'stc_render_affiliate_banner_component' );
	add_shortcode( 'stc_affiliate_promotion_card', 'stc_render_affiliate_promotion_card_component' );
}
add_action( 'init', 'stc_register_content_component_shortcodes' );
