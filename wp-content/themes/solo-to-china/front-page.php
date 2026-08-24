<?php
/**
 * SoloToChina front page.
 *
 * @package SoloToChina
 */

get_header();

$survival_items = [
	[ 'title' => 'Payment', 'icon' => 'payment' ],
	[ 'title' => 'Apps', 'icon' => 'apps' ],
	[ 'title' => 'eSIM', 'icon' => 'esim' ],
	[ 'title' => 'Visa', 'icon' => 'visa' ],
	[ 'title' => 'VPN', 'icon' => 'vpn' ],
];

$cities = [
	[ 'name' => 'Beijing', 'copy' => 'History & culture', 'class' => 'beijing', 'image' => 'card-beijing-hd.webp' ],
	[ 'name' => 'Shanghai', 'copy' => 'Modern & vibrant', 'class' => 'shanghai', 'image' => 'card-shanghai-hd.webp' ],
	[ 'name' => 'Guangzhou', 'copy' => 'Business & shopping', 'class' => 'guangzhou', 'image' => 'card-guangzhou-hd.webp' ],
	[ 'name' => 'Chengdu', 'copy' => 'Pandas & laid-back', 'class' => 'chengdu', 'image' => 'card-chengdu-hd.webp' ],
	[ 'name' => 'Chongqing', 'copy' => 'Mountains & rivers', 'class' => 'chongqing', 'image' => 'card-chongqing-hd.webp' ],
	[ 'name' => "Xi'an", 'copy' => 'Ancient capital', 'class' => 'xian', 'image' => 'card-xian-hd.webp' ],
	[ 'name' => 'Hangzhou', 'copy' => 'Natural beauty', 'class' => 'hangzhou', 'image' => 'card-hangzhou-hd.webp' ],
	[ 'name' => 'Zhangjiajie', 'copy' => 'Otherworldly peaks', 'class' => 'zhangjiajie', 'image' => 'card-zhangjiajie-city-hd.webp' ],
];

$attractions = [
	[ 'name' => 'Forbidden City', 'city' => 'Beijing', 'tag' => 'Booking required', 'class' => 'forbidden-city', 'image' => 'card-forbidden-city-hd.webp' ],
	[ 'name' => 'Great Wall', 'city' => 'Beijing', 'tag' => 'Best time: Apr-Oct', 'class' => 'great-wall', 'image' => 'card-great-wall-hd.webp' ],
	[ 'name' => 'Terracotta Warriors', 'city' => "Xi'an", 'tag' => 'Passport', 'class' => 'terracotta', 'image' => 'card-terracotta-hd.webp' ],
	[ 'name' => 'Zhangjiajie', 'city' => 'Hunan', 'tag' => 'Best time: Apr-Nov', 'class' => 'zhangjiajie', 'image' => 'card-zhangjiajie-attraction-hd.webp' ],
	[ 'name' => 'West Lake', 'city' => 'Hangzhou', 'tag' => 'Best time: Mar-May', 'class' => 'west-lake', 'image' => 'card-west-lake-hd.webp' ],
	[ 'name' => 'Shanghai Disney Resort', 'city' => 'Shanghai', 'tag' => 'Booking required', 'class' => 'disney', 'image' => 'card-disney-hd.webp' ],
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
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="stc-section" aria-labelledby="cities-title">
		<div class="stc-section__header">
			<h2 id="cities-title">City Guides</h2>
			<a class="stc-section__view-all" href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>" aria-label="View all city guides">View all <span aria-hidden="true">&rsaquo;</span></a>
		</div>
		<div class="stc-guide-grid-shell" data-stc-guide-grid-shell data-stc-guide-label="Cities">
			<div id="home-city-grid" class="stc-card-grid stc-card-grid--cities" data-stc-guide-grid>
				<?php foreach ( $cities as $city ) : ?>
					<article class="stc-image-card stc-image-card--<?php echo esc_attr( $city['class'] ); ?>">
						<?php stc_render_guide_card_media( $city['image'], $city['name'] ); ?>
						<a class="stc-image-card__link" href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">
							<span class="stc-image-card__content">
								<strong><?php echo esc_html( $city['name'] ); ?></strong>
								<span><?php echo esc_html( $city['copy'] ); ?></span>
							</span>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="stc-guide-grid-reveal">
				<button type="button" data-stc-guide-reveal aria-controls="home-city-grid" aria-expanded="false">
					<span data-stc-guide-reveal-label>+4 More Cities</span>
					<svg class="stc-guide-grid-reveal__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 9 5 5 5-5"/></svg>
				</button>
			</div>
		</div>
	</section>

	<section class="stc-section" aria-labelledby="attractions-title">
		<div class="stc-section__header">
			<h2 id="attractions-title">Attraction Guides</h2>
			<a class="stc-section__view-all" href="<?php echo esc_url( home_url( '/attraction-guides/' ) ); ?>" aria-label="View all attraction guides">View all <span aria-hidden="true">&rsaquo;</span></a>
		</div>
		<div class="stc-guide-grid-shell" data-stc-guide-grid-shell data-stc-guide-label="Attractions">
			<div id="home-attraction-grid" class="stc-card-grid stc-card-grid--attractions" data-stc-guide-grid>
				<?php foreach ( $attractions as $attraction ) : ?>
					<article class="stc-image-card stc-image-card--<?php echo esc_attr( $attraction['class'] ); ?>">
						<?php stc_render_guide_card_media( $attraction['image'], $attraction['name'] ); ?>
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
			<div class="stc-guide-grid-reveal">
				<button type="button" data-stc-guide-reveal aria-controls="home-attraction-grid" aria-expanded="false">
					<span data-stc-guide-reveal-label>+2 More Attractions</span>
					<svg class="stc-guide-grid-reveal__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 9 5 5 5-5"/></svg>
				</button>
			</div>
		</div>
	</section>

	<section class="stc-planner" aria-labelledby="planner-title">
		<div class="stc-planner__intro">
			<span class="stc-planner__icon" aria-hidden="true">
				<svg viewBox="0 0 48 48" focusable="false"><rect x="6" y="9" width="32" height="31" rx="3"/><path d="M14 5v8M30 5v8M6 18h32M13 25h5M22 25h5M13 32h5"/><circle cx="36" cy="35" r="9"/><path d="m32.5 35 2.5 2.5 4.5-5"/></svg>
			</span>
			<div>
				<h2 id="planner-title">Plan Your Trip</h2>
				<p>Book hotels, trains &amp; flights with confidence.</p>
			</div>
		</div>
		<div class="stc-planner__partner">
			<strong>Trip.com</strong>
			<a class="stc-button stc-button--secondary" href="https://www.trip.com/" target="_blank" rel="sponsored noopener">Explore on Trip.com <span aria-hidden="true">&#8599;</span></a>
			<p class="stc-affiliate-disclosure">Opens in a new tab. We may earn a commission at no extra cost to you.</p>
		</div>
		<span class="stc-planner__art" aria-hidden="true"></span>
	</section>

	<section class="stc-ticket-band" aria-labelledby="ticket-title">
		<div class="stc-ticket-band__intro">
			<span class="stc-ticket-band__icon" aria-hidden="true"></span>
			<div>
				<h2 id="ticket-title">Ticket Date &amp; Availability</h2>
				<p>Check booking windows &amp; set free alerts before your visit.</p>
			</div>
		</div>
		<div class="stc-ticket-band__steps">
			<p><span class="stc-ticket-band__step-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg></span><strong>Real-time Dates</strong></p>
			<p><span class="stc-ticket-band__step-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg></span><strong>Free Alerts</strong></p>
		</div>
		<div class="stc-ticket-band__action">
			<a class="stc-button stc-button--gold" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Check Dates &amp; Set Alerts</a>
			<p class="stc-ticket-band__trust"><span aria-hidden="true">&#10003;</span> Free to use <span aria-hidden="true">&bull;</span> No login required</p>
		</div>
	</section>

	<section class="stc-faq" aria-labelledby="faq-title">
		<div class="stc-section__header">
			<h2 id="faq-title">FAQ</h2>
			<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">View all FAQs</a>
		</div>
		<div class="stc-faq__grid">
			<details><summary><span>Do I need a visa to visit China?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>Check entry rules before you travel.</p></div></details>
			<details><summary><span>How can I pay in China?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>Payment setup is a key first step.</p></div></details>
			<details><summary><span>Is China safe for solo travelers?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>Plan with practical local context.</p></div></details>
			<details><summary><span>Which apps are essential in China?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>Maps, translation, payments, and transport matter most.</p></div></details>
		</div>
	</section>
</main>

<?php
get_footer();
