<?php
/**
 * SoloToChina front page.
 *
 * @package SoloToChina
 */

get_header();

$survival_items = [
	[ 'title' => 'Payment', 'copy' => 'Cards and mobile payments.', 'icon' => 'payment' ],
	[ 'title' => 'Essential Apps', 'copy' => 'Maps, transport, translate.', 'icon' => 'apps' ],
	[ 'title' => 'eSIM', 'copy' => 'Stay connected anywhere.', 'icon' => 'esim' ],
	[ 'title' => 'Visa', 'copy' => 'Requirements and entry tips.', 'icon' => 'visa' ],
	[ 'title' => 'VPN / Internet', 'copy' => 'Access and connection tips.', 'icon' => 'vpn' ],
];

$cities = [
	[ 'name' => 'Beijing', 'copy' => 'History and culture', 'class' => 'beijing' ],
	[ 'name' => 'Shanghai', 'copy' => 'Modern and vibrant', 'class' => 'shanghai' ],
	[ 'name' => 'Guangzhou', 'copy' => 'Business and food', 'class' => 'guangzhou' ],
	[ 'name' => 'Chengdu', 'copy' => 'Pandas and laid-back life', 'class' => 'chengdu' ],
	[ 'name' => 'Chongqing', 'copy' => 'Mountains and rivers', 'class' => 'chongqing' ],
	[ 'name' => "Xi'an", 'copy' => 'Ancient capital', 'class' => 'xian' ],
	[ 'name' => 'Hangzhou', 'copy' => 'West Lake calm', 'class' => 'hangzhou' ],
	[ 'name' => 'Zhangjiajie', 'copy' => 'Otherworldly peaks', 'class' => 'zhangjiajie' ],
];

$attractions = [
	[ 'name' => 'Forbidden City', 'city' => 'Beijing', 'tag' => 'Booking required', 'class' => 'forbidden-city' ],
	[ 'name' => 'Great Wall', 'city' => 'Beijing', 'tag' => 'Best time: Apr-Oct', 'class' => 'great-wall' ],
	[ 'name' => 'Terracotta Warriors', 'city' => "Xi'an", 'tag' => 'Passport', 'class' => 'terracotta' ],
	[ 'name' => 'Zhangjiajie', 'city' => 'Hunan', 'tag' => 'Best time: Apr-Nov', 'class' => 'zhangjiajie' ],
	[ 'name' => 'West Lake', 'city' => 'Hangzhou', 'tag' => 'Best time: Mar-May', 'class' => 'west-lake' ],
	[ 'name' => 'Shanghai Disney Resort', 'city' => 'Shanghai', 'tag' => 'Booking required', 'class' => 'disney' ],
];

if ( ! function_exists( 'stc_render_home_save_guide_button' ) ) {
	function stc_render_home_save_guide_button( $title, $copy, $type ) {
		echo '<button class="stc-save-guide stc-save-guide--image-card" type="button" data-stc-save-guide data-guide-type="' . esc_attr( $type ) . '" data-guide-id="' . esc_attr( sanitize_title( $type . '-' . $title ) ) . '" data-guide-title="' . esc_attr( $title ) . '" data-guide-copy="' . esc_attr( $copy ) . '">Save</button>';
	}
}
?>

<main id="main">
	<section class="stc-hero">
		<div class="stc-hero__content">
			<h1>China, clearly planned</h1>
			<p>Practical tips for independent travel.</p>
			<a class="stc-button stc-button--primary" href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">Start your China journey</a>
		</div>
	</section>

	<section class="stc-survival" aria-labelledby="survival-title">
		<h2 id="survival-title">Survival Kit</h2>
		<div class="stc-survival__grid">
			<?php foreach ( $survival_items as $item ) : ?>
				<a class="stc-survival-card" href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">
					<?php stc_render_survival_icon( $item['icon'] ); ?>
					<strong><?php echo esc_html( $item['title'] ); ?></strong>
					<span><?php echo esc_html( $item['copy'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="stc-section" aria-labelledby="cities-title">
		<div class="stc-section__header">
			<h2 id="cities-title">City Guides</h2>
			<a href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">View all city guides</a>
		</div>
		<div class="stc-card-grid stc-card-grid--cities">
			<?php foreach ( $cities as $city ) : ?>
				<article class="stc-image-card stc-image-card--<?php echo esc_attr( $city['class'] ); ?>">
					<span class="stc-image-card__media" aria-hidden="true"></span>
					<a class="stc-image-card__link" href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">
						<span class="stc-image-card__content">
							<strong><?php echo esc_html( $city['name'] ); ?></strong>
							<span><?php echo esc_html( $city['copy'] ); ?></span>
						</span>
					</a>
					<?php stc_render_home_save_guide_button( $city['name'], $city['copy'], 'City Guide' ); ?>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="stc-section" aria-labelledby="attractions-title">
		<div class="stc-section__header">
			<h2 id="attractions-title">Attraction Guides</h2>
			<a href="<?php echo esc_url( home_url( '/attraction-guides/' ) ); ?>">View all attractions</a>
		</div>
		<div class="stc-card-grid stc-card-grid--attractions">
			<?php foreach ( $attractions as $attraction ) : ?>
				<article class="stc-image-card stc-image-card--<?php echo esc_attr( $attraction['class'] ); ?>">
					<span class="stc-image-card__media" aria-hidden="true"></span>
					<span class="stc-image-card__tag"><?php echo esc_html( $attraction['tag'] ); ?></span>
					<a class="stc-image-card__link" href="<?php echo esc_url( home_url( '/attraction-guides/' ) ); ?>">
						<span class="stc-image-card__content">
							<strong><?php echo esc_html( $attraction['name'] ); ?></strong>
							<span><?php echo esc_html( $attraction['city'] ); ?></span>
						</span>
					</a>
					<?php stc_render_home_save_guide_button( $attraction['name'], $attraction['tag'] . ' in ' . $attraction['city'], 'Attraction Guide' ); ?>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="stc-planner" aria-labelledby="planner-title">
		<div>
			<h2 id="planner-title">Plan your trip</h2>
			<p>Build your itinerary and book with confidence.</p>
		</div>
		<div>
			<span>Start planning on</span>
			<strong>Trip.com</strong>
			<a class="stc-button stc-button--secondary" href="https://www.trip.com/" target="_blank" rel="sponsored noopener">Start planning on Trip.com</a>
			<p>Opens in a new tab. We may earn a commission at no extra cost to you.</p>
		</div>
	</section>

	<section class="stc-ticket-band" aria-labelledby="ticket-title">
		<div>
			<h2 id="ticket-title">Ticket Tool / Reminder</h2>
			<p>Check attraction ticket dates and set a free reminder.</p>
		</div>
		<?php
		if ( shortcode_exists( 'solo_to_china_ticket_tool' ) ) {
			echo do_shortcode( '[solo_to_china_ticket_tool]' );
		} else {
			echo '<a class="stc-button stc-button--gold" href="' . esc_url( home_url( '/tools/' ) ) . '">Check ticket date / Set reminder</a>';
		}
		?>
	</section>

	<section class="stc-faq" aria-labelledby="faq-title">
		<div class="stc-section__header">
			<h2 id="faq-title">FAQ</h2>
			<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">View all FAQs</a>
		</div>
		<div class="stc-faq__grid">
			<details><summary>Do I need a visa to visit China?</summary><p>Check entry rules before you travel.</p></details>
			<details><summary>How can I pay in China?</summary><p>Payment setup is a key first step.</p></details>
			<details><summary>Is China safe for solo travelers?</summary><p>Plan with practical local context.</p></details>
			<details><summary>Which apps are essential in China?</summary><p>Maps, translation, payments, and transport matter most.</p></details>
		</div>
	</section>
</main>

<?php
get_footer();
