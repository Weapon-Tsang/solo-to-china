<?php
/**
 * Shortcodes for SoloToChina tools.
 *
 * @package SoloToChinaTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function stc_tools_group_attractions_by_city( $attractions ) {
	$grouped_attractions = [];

	foreach ( $attractions as $attraction ) {
		$city = $attraction['city'];

		if ( ! isset( $grouped_attractions[ $city ] ) ) {
			$grouped_attractions[ $city ] = [];
		}

		$grouped_attractions[ $city ][] = $attraction;
	}

	return $grouped_attractions;
}

function stc_tools_render_ticket_tool( $attributes = array() ) {
	$attributes = shortcode_atts(
		array(
			'attraction_slug' => '',
			'heading_level'   => '2',
		),
		(array) $attributes,
		'solo_to_china_ticket_tool'
	);

	$attractions = stc_tools_get_attractions();
	$attractions_by_city = stc_tools_group_attractions_by_city( $attractions );
	$requested_attraction = sanitize_title( $attributes['attraction_slug'] );
	$heading_tag          = '3' === (string) $attributes['heading_level'] ? 'h3' : 'h2';
	$available_slugs      = wp_list_pluck( $attractions, 'slug' );

	if ( ! in_array( $requested_attraction, $available_slugs, true ) ) {
		$requested_attraction = '';
	}

	ob_start();
	?>
	<form class="stc-ticket-tool" action="#" method="get" data-stc-ticket-tool>
		<div class="stc-ticket-tool__heading">
			<<?php echo esc_html( $heading_tag ); ?>><?php esc_html_e( 'Ticket Booking Window', 'solo-to-china-tools' ); ?></<?php echo esc_html( $heading_tag ); ?>>
			<p><?php esc_html_e( 'Choose your attraction and visit date to see when you should start checking tickets.', 'solo-to-china-tools' ); ?></p>
		</div>
		<label>
			<span>Select attraction</span>
			<select name="stc_attraction" required>
				<?php foreach ( $attractions_by_city as $city => $city_attractions ) : ?>
					<optgroup label="<?php echo esc_attr( $city ); ?>">
						<?php foreach ( $city_attractions as $attraction ) : ?>
							<option
								value="<?php echo esc_attr( $attraction['slug'] ); ?>"
								data-name="<?php echo esc_attr( $attraction['name'] ); ?>"
								data-city="<?php echo esc_attr( $attraction['city'] ); ?>"
								data-booking-note="<?php echo esc_attr( $attraction['booking_note'] ); ?>"
								data-passport-note="<?php echo esc_attr( $attraction['passport_note'] ); ?>"
								data-lead-days="<?php echo esc_attr( (string) $attraction['booking_lead_days'] ); ?>"
								<?php selected( $requested_attraction, $attraction['slug'] ); ?>
							>
								<?php echo esc_html( $attraction['name'] ); ?>
							</option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		</label>
		<label>
			<span>Select visit date</span>
			<input type="date" name="stc_visit_date" required>
		</label>
		<button type="submit">Check booking date</button>
		<div class="stc-ticket-result" data-stc-ticket-result role="status" aria-live="polite" aria-atomic="true"></div>
		<a class="stc-ticket-tool__booking-link" href="https://www.trip.com/" target="_blank" rel="sponsored noopener" data-stc-ticket-link hidden><?php esc_html_e( 'Check tickets on Trip.com', 'solo-to-china-tools' ); ?> <span aria-hidden="true">&#8599;</span></a>
		<p class="stc-ticket-tool__disclosure" data-stc-ticket-disclosure hidden><?php esc_html_e( 'Affiliate link. SoloToChina may earn a commission at no extra cost to you.', 'solo-to-china-tools' ); ?></p>
		<p class="stc-ticket-tool__note"><?php esc_html_e( 'Ticket rules and booking windows can change. Verify current information before purchasing.', 'solo-to-china-tools' ); ?></p>
	</form>
	<?php
	return ob_get_clean();
}
