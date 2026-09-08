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

function stc_tools_tool_icon( $name ) {
	$paths = array(
		'photo' => '<rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="8.5" cy="9" r="1.5"/><path d="m5 17 4.5-4.5 3.2 3.2 2.2-2.2L19 17"/>',
		'taxi'  => '<path d="M5 17h14l-1.2-7.2A2 2 0 0 0 15.8 8H8.2a2 2 0 0 0-2 1.8L5 17Z"/><path d="M7 8 8.5 5h7L17 8M4 13h16M7 17v2M17 17v2"/>',
		'ticket'=> '<path d="M4 7a2 2 0 0 0 2-2h12v4a2 2 0 0 0 0 4v4H6a2 2 0 0 0-2-2V7Z"/><path d="M10 8v6"/>',
	);
	return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $paths[ isset( $paths[ $name ] ) ? $name : 'ticket' ] . '</svg>';
}

function stc_tools_render_directory() {
	$cards = array(
		array( 'icon' => 'photo', 'title' => 'Find This Place', 'copy' => 'Upload up to four photos of the same location to identify a China travel spot.', 'label' => 'Find a place', 'url' => home_url( '/tools/find-this-place/' ) ),
		array( 'icon' => 'taxi', 'title' => 'Taxi Card', 'copy' => 'Turn a destination into a Chinese card you can show to a driver.', 'label' => 'Create taxi card', 'url' => home_url( '/tools/taxi-card/' ) ),
	);
	ob_start(); ?>
	<section class="stc-tools-directory" aria-labelledby="stc-tools-directory-title">
		<div class="stc-tools-directory__heading"><p><?php esc_html_e( 'Travel utilities', 'solo-to-china-tools' ); ?></p><h2 id="stc-tools-directory-title"><?php esc_html_e( 'Solve the next practical step', 'solo-to-china-tools' ); ?></h2></div>
		<div class="stc-tools-directory__grid">
			<?php foreach ( $cards as $card ) : ?><article class="stc-tool-card"><span class="stc-tool-card__icon"><?php echo stc_tools_tool_icon( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php echo esc_html( $card['title'] ); ?></h3><p><?php echo esc_html( $card['copy'] ); ?></p><a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['label'] ); ?><span aria-hidden="true">→</span></a></article><?php endforeach; ?>
		</div>
	</section>
	<section class="stc-tools-ticket" aria-labelledby="stc-tools-ticket-title"><div class="stc-tools-directory__heading"><p><?php esc_html_e( 'Plan ahead', 'solo-to-china-tools' ); ?></p><h2 id="stc-tools-ticket-title"><?php esc_html_e( 'Ticket Booking Window', 'solo-to-china-tools' ); ?></h2></div><?php echo stc_tools_render_ticket_tool( array( 'heading_level' => '3' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></section>
	<?php return ob_get_clean();
}

function stc_tools_render_place_finder() {
	ob_start(); ?>
	<section class="stc-place-finder" data-stc-place-finder>
		<header class="stc-tool-intro"><p class="stc-tool-intro__eyebrow"><?php esc_html_e( 'Photos to practical plan', 'solo-to-china-tools' ); ?></p><h2><?php esc_html_e( 'Upload photos of the same place.', 'solo-to-china-tools' ); ?></h2><p><?php esc_html_e( 'Different angles and visible signs give the AI more evidence and can reduce wrong matches.', 'solo-to-china-tools' ); ?></p></header>
		<form class="stc-place-finder__form" data-stc-place-form enctype="multipart/form-data" novalidate>
			<div class="stc-place-upload" data-stc-place-dropzone>
				<input class="stc-place-upload__input" type="file" name="images[]" id="stc-place-image" accept="image/jpeg,image/png,image/webp" multiple data-stc-place-input>
				<label class="stc-place-upload__label" for="stc-place-image"><span class="stc-place-upload__icon"><?php echo stc_tools_tool_icon( 'photo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><strong><?php esc_html_e( 'Upload 1–4 photos of the same place', 'solo-to-china-tools' ); ?></strong><span><?php esc_html_e( 'JPG, PNG or WebP · up to 20 MB each · 60 MB total', 'solo-to-china-tools' ); ?></span><small><?php esc_html_e( 'Best results: include a wide view, entrance or sign, and nearby streets from different angles.', 'solo-to-china-tools' ); ?></small></label>
				<div class="stc-place-upload__preview" data-stc-place-preview hidden><div class="stc-place-upload__previews" data-stc-place-previews></div><div class="stc-place-upload__actions"><button type="button" data-stc-place-change><?php esc_html_e( 'Choose different photos', 'solo-to-china-tools' ); ?></button><button type="button" data-stc-place-remove><?php esc_html_e( 'Remove all', 'solo-to-china-tools' ); ?></button></div></div>
			</div>
			<p class="stc-place-finder__privacy"><?php esc_html_e( 'Upload only photos from one location. They are processed together for identification, then discarded; they are not added to the Media Library.', 'solo-to-china-tools' ); ?></p>
			<div class="stc-place-finder__hint" data-stc-city-hint hidden><label for="stc-place-city-hint"><?php esc_html_e( 'Optional city hint', 'solo-to-china-tools' ); ?></label><input id="stc-place-city-hint" name="city_hint" type="text" maxlength="80" autocomplete="off" placeholder="e.g. Chongqing"></div>
			<button class="stc-tool-primary" type="submit" data-stc-place-submit disabled><?php esc_html_e( 'Find this place', 'solo-to-china-tools' ); ?></button>
			<p class="stc-tool-message" role="status" aria-live="polite" aria-atomic="true" data-stc-place-status></p>
		</form>
		<div class="stc-place-finder__result" data-stc-place-result aria-live="polite"></div>
		<noscript><p class="stc-tool-noscript"><?php esc_html_e( 'This tool requires JavaScript to upload and identify photos.', 'solo-to-china-tools' ); ?></p></noscript>
	</section>
	<?php return ob_get_clean();
}

function stc_tools_driver_card_markup( $place, $hidden = false ) {
	if ( ! is_array( $place ) || empty( $place['name_zh'] ) ) { return ''; }
	$address = isset( $place['sources']['address'] ) && 'VERIFIED' === $place['sources']['address'] ? $place['verified_address_zh'] : '';
	ob_start(); ?>
	<div class="stc-taxi-card__card" data-stc-taxi-card-output<?php echo $hidden ? ' hidden' : ''; ?>>
		<p class="stc-taxi-card__instruction"><?php esc_html_e( '请带我去这里', 'solo-to-china-tools' ); ?></p>
		<strong class="stc-taxi-card__chinese" data-stc-taxi-name-zh><?php echo esc_html( $place['name_zh'] ); ?></strong>
		<p class="stc-taxi-card__address" data-stc-taxi-address><?php echo esc_html( $address ? $address : $place['city_zh'] ); ?></p>
		<p class="stc-taxi-card__english"><span data-stc-taxi-name-en><?php echo esc_html( $place['name_en'] ); ?></span><span data-stc-taxi-city-en><?php echo esc_html( $place['city_en'] ); ?></span></p>
		<?php if ( ! empty( $place['recommended_dropoff_zh'] ) && isset( $place['sources']['dropoff'] ) && 'VERIFIED' === $place['sources']['dropoff'] ) : ?><p class="stc-taxi-card__dropoff"><?php esc_html_e( '司机下客点：', 'solo-to-china-tools' ); ?><?php echo esc_html( $place['recommended_dropoff_zh'] ); ?></p><?php endif; ?>
	</div>
	<?php return ob_get_clean();
}

function stc_tools_render_taxi_card( $attributes = array() ) {
	$attributes = shortcode_atts( array( 'entity_key' => '', 'heading_level' => '2' ), (array) $attributes, 'solo_to_china_taxi_card' );
	$requested = sanitize_title( $attributes['entity_key'] );
	if ( ! $requested && isset( $_GET['entity_key'] ) ) { $requested = sanitize_title( wp_unslash( $_GET['entity_key'] ) ); }
	$place = $requested ? stc_tools_get_place( $requested ) : null;
	$heading = '3' === (string) $attributes['heading_level'] ? 'h3' : 'h2';
	ob_start(); ?>
	<section class="stc-taxi-card" data-stc-taxi-tool data-initial-entity="<?php echo esc_attr( $requested ); ?>">
		<header class="stc-tool-intro"><p class="stc-tool-intro__eyebrow"><?php esc_html_e( 'Taxi Card', 'solo-to-china-tools' ); ?></p><<?php echo esc_html( $heading ); ?>><?php esc_html_e( 'Turn a destination into a clear Chinese place card.', 'solo-to-china-tools' ); ?></<?php echo esc_html( $heading ); ?>><p><?php esc_html_e( 'Use it with taxi drivers, hotel staff, or Chinese ride-hailing apps.', 'solo-to-china-tools' ); ?></p></header>
		<form class="stc-taxi-card__form" data-stc-taxi-form><label for="stc-taxi-query"><?php esc_html_e( 'Search destination', 'solo-to-china-tools' ); ?></label><div><input id="stc-taxi-query" name="query" type="search" maxlength="160" autocomplete="off" placeholder="Forbidden City, The Bund…"><button class="stc-tool-primary" type="submit"><?php esc_html_e( 'Create taxi card', 'solo-to-china-tools' ); ?></button></div><p class="stc-tool-message" role="status" aria-live="polite" data-stc-taxi-status></p></form>
		<div class="stc-taxi-card__choices" data-stc-taxi-choices hidden></div>
		<div class="stc-taxi-card__result" data-stc-taxi-result<?php echo $place ? '' : ' hidden'; ?>>
			<p class="stc-taxi-card__result-label"><?php esc_html_e( 'Show this to your driver', 'solo-to-china-tools' ); ?></p>
			<?php echo $place ? stc_tools_driver_card_markup( $place ) : stc_tools_driver_card_markup( array( 'name_zh' => '目的地', 'name_en' => 'Destination', 'city_zh' => '', 'city_en' => '', 'sources' => array( 'address' => 'UNKNOWN' ) ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="stc-taxi-card__actions"><button type="button" data-stc-taxi-copy><?php esc_html_e( 'Copy destination', 'solo-to-china-tools' ); ?></button><button type="button" data-stc-taxi-fullscreen><?php esc_html_e( 'Show full-screen card', 'solo-to-china-tools' ); ?></button></div>
		</div>
		<div class="stc-driver-mode" role="dialog" aria-modal="true" aria-labelledby="stc-driver-mode-title" data-stc-driver-mode hidden><div class="stc-driver-mode__inner"><p id="stc-driver-mode-title"><?php esc_html_e( '请带我去这里', 'solo-to-china-tools' ); ?></p><strong data-stc-driver-name-zh></strong><span data-stc-driver-address></span><span data-stc-driver-name-en></span><div><button type="button" data-stc-driver-copy><?php esc_html_e( 'Copy destination', 'solo-to-china-tools' ); ?></button><button type="button" data-stc-driver-close><?php esc_html_e( 'Close', 'solo-to-china-tools' ); ?></button></div></div></div>
	</section>
	<?php return ob_get_clean();
}
