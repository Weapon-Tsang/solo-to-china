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
 * Register public shortcode adapters for Contract dynamic components.
 *
 * @return void
 */
function stc_register_content_component_shortcodes() {
	add_shortcode( 'stc_planner_cta', 'stc_render_planner_cta_component' );
	add_shortcode( 'stc_ticket_reminder', 'stc_render_ticket_reminder_component' );
	add_shortcode( 'stc_affiliate_cta', 'stc_render_affiliate_cta_component' );
}
add_action( 'init', 'stc_register_content_component_shortcodes' );
