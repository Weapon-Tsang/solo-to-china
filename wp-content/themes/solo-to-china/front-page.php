<?php
/**
 * SoloToChina front page.
 *
 * @package SoloToChina
 */

get_header();

$survival_items = [
	[ 'title' => 'Payment', 'icon' => 'payment', 'anchor' => 'payment' ],
	[ 'title' => 'Apps', 'icon' => 'apps', 'anchor' => 'essential-apps' ],
	[ 'title' => 'eSIM', 'icon' => 'esim', 'anchor' => 'esim' ],
	[ 'title' => 'Visa', 'icon' => 'visa', 'anchor' => 'visa' ],
	[ 'title' => 'VPN', 'icon' => 'vpn', 'anchor' => 'internet-access' ],
];

$cities = stc_get_site_collection( 'city-guides' );
$attractions = stc_get_site_collection( 'attraction-guides' );

?>

<main id="main">
	<section class="stc-hero">
		<?php stc_render_theme_image( 'hero-home', '', true ); ?>
		<div class="stc-hero__content">
			<h1>China,<br>clearly planned</h1>
			<p>Practical guides and useful tools for your first solo trip to China.</p>
			<a class="stc-button stc-button--primary" href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">Start with the essentials <span aria-hidden="true">&rarr;</span></a>
			<a class="stc-hero__secondary" href="<?php echo esc_url( home_url( '/tools/find-this-place/' ) ); ?>">Find a place from a photo &rarr;</a>
		</div>
	</section>

	<section class="stc-survival" aria-labelledby="survival-title">
		<h2 id="survival-title">Survival Kit</h2>
		<div class="stc-survival__grid">
			<?php foreach ( $survival_items as $item ) : ?>
				<a class="stc-survival-card" href="<?php echo esc_url( home_url( '/survival-kit/#' . $item['anchor'] ) ); ?>">
					<?php stc_render_survival_icon( $item['icon'] ); ?>
					<strong><?php echo esc_html( $item['title'] ); ?></strong>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ($cities) : ?>
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
						<a class="stc-image-card__link" href="<?php echo esc_url( $city['url'] ); ?>">
							<span class="stc-image-card__content">
								<strong><?php echo esc_html( $city['name'] ); ?></strong>
								<span><?php echo esc_html( $city['copy'] ); ?></span>
							</span>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="stc-guide-grid-reveal">
				<button type="button" hidden data-stc-guide-reveal aria-controls="home-city-grid" aria-expanded="false">
					<span data-stc-guide-reveal-label>+4 More Cities</span>
					<svg class="stc-guide-grid-reveal__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 9 5 5 5-5"/></svg>
				</button>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ($attractions) : ?>
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
						<a class="stc-image-card__link" href="<?php echo esc_url( $attraction['url'] ); ?>">
							<span class="stc-image-card__content">
								<strong><?php echo esc_html( $attraction['name'] ); ?></strong>
								<span><?php echo esc_html( $attraction['city'] ); ?></span>
							</span>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="stc-guide-grid-reveal">
				<button type="button" hidden data-stc-guide-reveal aria-controls="home-attraction-grid" aria-expanded="false">
					<span data-stc-guide-reveal-label>+2 More Attractions</span>
					<svg class="stc-guide-grid-reveal__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 9 5 5 5-5"/></svg>
				</button>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<section class="stc-section stc-home-tools" aria-labelledby="home-tools-title">
        <div class="stc-section__header"><h2 id="home-tools-title">A little help, right when you need it</h2></div>
        <div class="stc-tool-entry-grid">
            <a class="stc-tool-entry" href="<?php echo esc_url( home_url( '/tools/find-this-place/' ) ); ?>"><span aria-hidden="true">&#8982;</span><h3>Find This Place</h3><p>Bring a photo. Find likely places, useful guides, and a Chinese destination card.</p><strong>Choose your photos &rarr;</strong></a>
            <a class="stc-tool-entry" href="<?php echo esc_url( home_url( '/tools/taxi-card/' ) ); ?>"><span aria-hidden="true">&#8599;</span><h3>Show this to a driver</h3><p>Look up a destination and show its confirmed Chinese name. Arrival details are labelled separately.</p><strong>Make a Taxi Card &rarr;</strong></a>
        </div>
    </section>

	<?php stc_render_home_latest_guides(); ?>

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
			<a class="stc-button stc-button--secondary" href="<?php echo esc_url( stc_get_trip_planner_url() ); ?>" target="_blank" rel="sponsored nofollow noopener noreferrer">Open Trip.Planner <span aria-hidden="true">&#8599;</span></a>
			<p class="stc-affiliate-disclosure">Opens in a new tab. We may earn a commission at no extra cost to you.</p>
		</div>
		<span class="stc-planner__art" aria-hidden="true"></span>
	</section>

	<section class="stc-faq" aria-labelledby="faq-title">
		<div class="stc-section__header">
			<h2 id="faq-title">FAQ</h2>
			<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">View all FAQs</a>
		</div>
		<div class="stc-faq__grid">
			<details><summary><span>Do I need a visa to visit China?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>It depends on your passport, purpose, arrival date, and length of stay. Confirm the exact policy with the National Immigration Administration and the Chinese embassy or consulate responsible for your residence before buying non-refundable travel.</p><a class="stc-faq__answer-link" href="<?php echo esc_url( home_url( '/faq/#faq-entry-documents' ) ); ?>">Read the entry exceptions <span aria-hidden="true">&#8594;</span></a></div></details>
			<details><summary><span>How can I pay in China?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>Set up Alipay or WeChat with a supported international card before departure, but keep a second card and some RMB. A linked card can still be declined by its issuer or require identity verification.</p><a class="stc-faq__answer-link" href="<?php echo esc_url( home_url( '/survival-kit/#payment' ) ); ?>">Use the payment checklist <span aria-hidden="true">&#8594;</span></a></div></details>
			<details><summary><span>Is China safe for solo travelers?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>Many visitors travel independently without difficulty. Share your route, use licensed transport, keep your hotel address in Chinese, and carry separate payment and connection backups so a lost phone or failed app does not leave you stranded.</p><a class="stc-faq__answer-link" href="<?php echo esc_url( home_url( '/faq/#faq-getting-around-staying-safe' ) ); ?>">See the solo-travel safety plan <span aria-hidden="true">&#8594;</span></a></div></details>
			<details><summary><span>Which apps are essential in China?</span><?php stc_render_faq_chevron(); ?></summary><div class="stc-faq__answer"><p>Cover five jobs: payment, a China-compatible map, offline translation, ride hailing, and intercity transport. Create accounts before flying and keep offline copies of addresses and bookings.</p><a class="stc-faq__answer-link" href="<?php echo esc_url( home_url( '/survival-kit/#essential-apps' ) ); ?>">Build your app setup <span aria-hidden="true">&#8594;</span></a></div></details>
		</div>
	</section>
</main>

<?php
get_footer();
