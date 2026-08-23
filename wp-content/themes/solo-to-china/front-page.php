<?php
/**
 * SoloToChina front page.
 *
 * @package SoloToChina
 */

get_header();

$survival_items = [
	[ 'title' => 'Payment', 'copy' => 'Cards & mobile payments', 'icon' => 'payment' ],
	[ 'title' => 'Essential Apps', 'copy' => 'Navigation, transport, translate', 'icon' => 'apps' ],
	[ 'title' => 'eSIM', 'copy' => 'Stay connected anywhere.', 'icon' => 'esim' ],
	[ 'title' => 'Visa', 'copy' => 'Requirements & application tips', 'icon' => 'visa' ],
	[ 'title' => 'VPN / Internet', 'copy' => 'Access, safety & connection tips', 'icon' => 'vpn' ],
];

$cities = [
	[ 'name' => 'Beijing', 'copy' => 'History & culture', 'class' => 'beijing' ],
	[ 'name' => 'Shanghai', 'copy' => 'Modern & vibrant', 'class' => 'shanghai' ],
	[ 'name' => 'Guangzhou', 'copy' => 'Business & shopping', 'class' => 'guangzhou' ],
	[ 'name' => 'Chengdu', 'copy' => 'Pandas & laid-back', 'class' => 'chengdu' ],
	[ 'name' => 'Chongqing', 'copy' => 'Mountains & rivers', 'class' => 'chongqing' ],
	[ 'name' => "Xi'an", 'copy' => 'Ancient capital', 'class' => 'xian' ],
	[ 'name' => 'Hangzhou', 'copy' => 'Natural beauty', 'class' => 'hangzhou' ],
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

?>

<main id="main">
	<section class="stc-hero">
		<div class="stc-hero__content">
			<h1>China,<br>clearly planned</h1>
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
		<div class="stc-city-grid-shell" data-stc-city-grid-shell>
			<div id="home-city-grid" class="stc-card-grid stc-card-grid--cities" data-stc-city-grid>
				<?php foreach ( $cities as $city ) : ?>
					<article class="stc-image-card stc-image-card--<?php echo esc_attr( $city['class'] ); ?>">
						<span class="stc-image-card__media" aria-hidden="true"></span>
						<a class="stc-image-card__link" href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">
							<span class="stc-image-card__content">
								<strong><?php echo esc_html( $city['name'] ); ?></strong>
								<span><?php echo esc_html( $city['copy'] ); ?></span>
							</span>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="stc-city-grid-reveal">
				<button type="button" data-stc-city-reveal aria-controls="home-city-grid" aria-expanded="false">
					<span data-stc-city-reveal-label>+4 More Cities</span>
				</button>
			</div>
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
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="stc-planner" aria-labelledby="planner-title">
		<div class="stc-planner__intro">
			<span class="stc-planner__icon" aria-hidden="true">
				<svg viewBox="0 0 48 48" focusable="false"><rect x="6" y="9" width="32" height="31" rx="3"/><path d="M14 5v8M30 5v8M6 18h32M13 25h5M22 25h5M13 32h5"/><circle cx="36" cy="35" r="9"/><path d="m32.5 35 2.5 2.5 4.5-5"/></svg>
			</span>
			<div>
				<h2 id="planner-title">Plan your trip</h2>
				<p>Build your itinerary and book with confidence.</p>
			</div>
		</div>
		<div class="stc-planner__partner">
			<span>Start planning on</span>
			<strong>Trip.com</strong>
			<a class="stc-button stc-button--secondary" href="https://www.trip.com/" target="_blank" rel="sponsored noopener">Start planning on Trip.com</a>
			<p>Opens in a new tab. We may earn a commission at no extra cost to you.</p>
		</div>
		<span class="stc-planner__art" aria-hidden="true"></span>
	</section>

	<section class="stc-ticket-band" aria-labelledby="ticket-title">
		<div class="stc-ticket-band__intro">
			<span class="stc-ticket-band__icon" aria-hidden="true"></span>
			<div>
				<h2 id="ticket-title">Ticket Tool / Reminder</h2>
				<p>Check attraction ticket dates and set a free reminder.</p>
			</div>
		</div>
		<div class="stc-ticket-band__steps">
			<p><span class="stc-ticket-band__step-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg></span><span><strong>Check ticket date</strong><span>See availability and important notes.</span></span></p>
			<p><span class="stc-ticket-band__step-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg></span><span><strong>Set free reminder</strong><span>Get notified before your visit.</span></span></p>
		</div>
		<div class="stc-ticket-band__action">
			<a class="stc-button stc-button--gold" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Check ticket date / Set reminder</a>
			<p>No login required. Free to use.</p>
		</div>
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
