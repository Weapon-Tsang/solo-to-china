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
		),
		(array) $attributes,
		'solo_to_china_ticket_tool'
	);

	$attractions = stc_tools_get_attractions();
	$attractions_by_city = stc_tools_group_attractions_by_city( $attractions );
	$requested_attraction = sanitize_title( $attributes['attraction_slug'] );
	$available_slugs      = wp_list_pluck( $attractions, 'slug' );

	if ( ! in_array( $requested_attraction, $available_slugs, true ) ) {
		$requested_attraction = '';
	}

	ob_start();
	?>
	<form class="stc-ticket-tool" action="#" method="get" data-stc-ticket-tool>
		<label>
			<span>Select attraction</span>
			<select name="stc_attraction">
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
			<span>Select date</span>
			<input type="date" name="stc_visit_date" required>
		</label>
		<button type="submit">Check ticket date</button>
		<button type="button" data-stc-save-reminder>Save reminder</button>
		<p>No login required. Free to use.</p>
		<div class="stc-ticket-result" data-stc-ticket-result aria-live="polite"></div>
		<section class="stc-reminder-list" aria-labelledby="stc-reminder-list-title">
			<div class="stc-reminder-list__header">
				<h3 id="stc-reminder-list-title">Saved reminders</h3>
				<div class="stc-reminder-list__actions">
					<button type="button" data-stc-export-reminders>Export</button>
					<label>
						<span>Import</span>
						<input type="file" accept="application/json,.json" data-stc-import-reminders>
					</label>
					<button type="button" data-stc-clear-reminders>Clear all</button>
				</div>
			</div>
			<p class="stc-tool-local-note">Saved reminders stay in this browser. Export, import, or clear them anytime.</p>
			<div data-stc-reminder-list></div>
		</section>
	</form>
	<?php
	return ob_get_clean();
}
