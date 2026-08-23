<?php
/**
 * Page template for SoloToChina core landing pages.
 *
 * @package SoloToChina
 */

get_header();

$page_id = get_queried_object_id();
$slug    = get_post_field( 'post_name', $page_id );

$core_pages = [
	'survival-kit'      => [
		'title' => 'Survival Kit',
		'copy'  => 'Start here before your first independent trip to China.',
		'items' => [
			[ 'title' => 'Payment', 'copy' => 'Cards, Alipay, WeChat Pay, and cash backup.', 'icon' => 'payment' ],
			[ 'title' => 'Essential Apps', 'copy' => 'Maps, translation, ride hailing, and transport.', 'icon' => 'apps' ],
			[ 'title' => 'eSIM', 'copy' => 'Stay connected before you land.', 'icon' => 'esim' ],
			[ 'title' => 'Visa', 'copy' => 'Entry checks and document preparation.', 'icon' => 'visa' ],
			[ 'title' => 'VPN / Internet', 'copy' => 'Access, safety, and connection basics.', 'icon' => 'vpn' ],
		],
	],
	'city-guides'       => [
		'title' => 'City Guides',
		'copy'  => 'City hubs for planning where to stay, how to move, and what to do.',
		'items' => [
			[ 'title' => 'Beijing', 'copy' => 'History & culture', 'class' => 'beijing' ],
			[ 'title' => 'Shanghai', 'copy' => 'Modern & vibrant', 'class' => 'shanghai' ],
			[ 'title' => 'Guangzhou', 'copy' => 'Business & shopping', 'class' => 'guangzhou' ],
			[ 'title' => 'Chengdu', 'copy' => 'Pandas & laid-back', 'class' => 'chengdu' ],
			[ 'title' => 'Chongqing', 'copy' => 'Mountains & rivers', 'class' => 'chongqing' ],
			[ 'title' => "Xi'an", 'copy' => 'Ancient capital', 'class' => 'xian' ],
			[ 'title' => 'Hangzhou', 'copy' => 'Natural beauty', 'class' => 'hangzhou' ],
			[ 'title' => 'Zhangjiajie', 'copy' => 'Otherworldly peaks', 'class' => 'zhangjiajie' ],
		],
	],
	'attraction-guides' => [
		'title' => 'Attraction Guides',
		'copy'  => 'Ticket timing, passport notes, best seasons, and practical visit planning.',
		'items' => [
			[ 'title' => 'Forbidden City', 'copy' => 'Beijing', 'tag' => 'Booking required', 'class' => 'forbidden-city' ],
			[ 'title' => 'Great Wall', 'copy' => 'Beijing', 'tag' => 'Best time: Apr-Oct', 'class' => 'great-wall' ],
			[ 'title' => 'Terracotta Warriors', 'copy' => "Xi'an", 'tag' => 'Passport', 'class' => 'terracotta' ],
			[ 'title' => 'Zhangjiajie', 'copy' => 'Hunan', 'tag' => 'Best time: Apr-Nov', 'class' => 'zhangjiajie' ],
			[ 'title' => 'West Lake', 'copy' => 'Hangzhou', 'tag' => 'Best time: Mar-May', 'class' => 'west-lake' ],
			[ 'title' => 'Shanghai Disney Resort', 'copy' => 'Shanghai', 'tag' => 'Booking required', 'class' => 'disney' ],
		],
	],
	'planner'           => [
		'title' => 'Planner',
		'copy'  => 'Use SoloToChina content to decide, then book travel products with a clear plan.',
	],
	'tools'             => [
		'title' => 'Tools',
		'copy'  => 'Free tools for practical China travel decisions. No login required.',
	],
	'faq'               => [
		'title' => 'FAQ',
		'copy'  => 'Short answers to common first-trip questions.',
		'items' => [
			[ 'title' => 'Do I need a visa to visit China?', 'copy' => 'Check the latest entry rules for your passport before booking.', 'link' => '/survival-kit/', 'link_label' => 'Review entry preparation' ],
			[ 'title' => 'How can I pay in China?', 'copy' => 'Set up mobile payment before arrival and keep a backup card or cash.', 'link' => '/survival-kit/', 'link_label' => 'Open the payment setup guide' ],
			[ 'title' => 'Which apps are essential in China?', 'copy' => 'Maps, translation, payments, ride hailing, and train tools are the basics.', 'link' => '/survival-kit/', 'link_label' => 'Browse essential setup guides' ],
			[ 'title' => 'Can I use Google, WhatsApp, and other services in China?', 'copy' => 'Some services may not work normally. Plan your connection setup in advance.', 'link' => '/survival-kit/', 'link_label' => 'Prepare internet access' ],
			[ 'title' => 'Is China safe for solo travelers?', 'copy' => 'Most trips are straightforward with normal urban travel awareness and planning.', 'link' => '/city-guides/', 'link_label' => 'Browse practical city guides' ],
			[ 'title' => 'How do I get around between cities?', 'copy' => 'High-speed rail is often the easiest option between major cities.', 'link' => '/planner/', 'link_label' => 'Continue to trip planning' ],
		],
	],
];

$page                = $core_pages[ $slug ] ?? null;
$guide_landing_slugs = [ 'survival-kit', 'city-guides', 'attraction-guides' ];
?>

<main id="main" class="stc-main">
	<?php if ( $page ) : ?>
		<section class="stc-page-hero stc-page-hero--visual stc-page-hero--<?php echo esc_attr( $slug ); ?>">
			<p><?php esc_html_e( 'SoloToChina', 'solo-to-china' ); ?></p>
			<h1><?php echo esc_html( $page['title'] ); ?></h1>
			<span><?php echo esc_html( $page['copy'] ); ?></span>
			<div class="stc-page-actions">
				<button type="button" data-stc-share-page data-share-title="<?php echo esc_attr( $page['title'] ); ?>" data-share-url="<?php echo esc_url( get_permalink() ); ?>">
					<?php esc_html_e( 'Share page', 'solo-to-china' ); ?>
				</button>
			</div>
		</section>

		<div class="stc-page-primary">
		<?php if ( 'survival-kit' === $slug ) : ?>
			<section class="stc-page-section">
				<div class="stc-link-grid stc-link-grid--five">
					<?php foreach ( $page['items'] as $item ) : ?>
						<article class="stc-info-card">
							<?php stc_render_survival_icon( $item['icon'] ); ?>
							<h2><?php echo esc_html( $item['title'] ); ?></h2>
							<p><?php echo esc_html( $item['copy'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php elseif ( in_array( $slug, [ 'city-guides', 'attraction-guides' ], true ) ) : ?>
			<?php
			$guide_grid_id    = 'city-guides' === $slug ? 'stc-city-guide-grid' : 'stc-attraction-guide-grid';
			$guide_grid_label = 'city-guides' === $slug ? 'Cities' : 'Attractions';
			$remaining_guides = max( 0, count( $page['items'] ) - 4 );
			?>
			<section class="stc-page-section">
				<div class="stc-guide-grid-shell" data-stc-guide-grid-shell data-stc-guide-label="<?php echo esc_attr( $guide_grid_label ); ?>">
				<div id="<?php echo esc_attr( $guide_grid_id ); ?>" class="stc-card-grid <?php echo esc_attr( 'city-guides' === $slug ? 'stc-card-grid--cities' : 'stc-card-grid--attractions' ); ?>" data-stc-guide-grid>
					<?php foreach ( $page['items'] as $item ) : ?>
						<article class="stc-image-card stc-image-card--<?php echo esc_attr( $item['class'] ); ?>">
							<span class="stc-image-card__media" aria-hidden="true"></span>
							<?php if ( ! empty( $item['tag'] ) ) : ?>
								<span class="stc-image-card__tag"><?php echo esc_html( $item['tag'] ); ?></span>
							<?php endif; ?>
							<a class="stc-image-card__link" href="<?php echo esc_url( get_permalink() ); ?>">
								<span class="stc-image-card__content">
									<strong><?php echo esc_html( $item['title'] ); ?></strong>
									<span><?php echo esc_html( $item['copy'] ); ?></span>
								</span>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
					<div class="stc-guide-grid-reveal">
						<button type="button" data-stc-guide-reveal aria-controls="<?php echo esc_attr( $guide_grid_id ); ?>" aria-expanded="false">
							<span data-stc-guide-reveal-label><?php echo esc_html( sprintf( '+%d More %s', $remaining_guides, $guide_grid_label ) ); ?></span>
							<svg class="stc-guide-grid-reveal__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 9 5 5 5-5"/></svg>
						</button>
					</div>
				</div>
			</section>
		<?php elseif ( 'planner' === $slug ) : ?>
			<section class="stc-planner stc-planner--page" aria-labelledby="stc-planner-page-title">
				<div class="stc-planner__intro">
					<span class="stc-planner__icon" aria-hidden="true">
						<svg viewBox="0 0 48 48" focusable="false"><rect x="6" y="9" width="32" height="31" rx="3"/><path d="M14 5v8M30 5v8M6 18h32M13 25h5M22 25h5M13 32h5"/><circle cx="36" cy="35" r="9"/><path d="m32.5 35 2.5 2.5 4.5-5"/></svg>
					</span>
					<div>
						<h2 id="stc-planner-page-title"><?php esc_html_e( 'Plan your trip', 'solo-to-china' ); ?></h2>
						<p><?php esc_html_e( 'Use practical guides first, then compare travel products with a clear route and budget.', 'solo-to-china' ); ?></p>
					</div>
				</div>
				<div class="stc-planner__partner">
					<span><?php esc_html_e( 'Start planning on', 'solo-to-china' ); ?></span>
					<strong>Trip.com</strong>
					<a class="stc-button stc-button--secondary" href="https://www.trip.com/" target="_blank" rel="sponsored noopener"><?php esc_html_e( 'Start planning on Trip.com', 'solo-to-china' ); ?></a>
					<p><?php esc_html_e( 'Opens in a new tab. We may earn a commission at no extra cost to you.', 'solo-to-china' ); ?></p>
				</div>
				<span class="stc-planner__art" aria-hidden="true"></span>
			</section>
		<?php elseif ( 'tools' === $slug ) : ?>
			<section class="stc-page-section">
				<div class="stc-feature-panel stc-feature-panel--gold">
					<div>
						<h2><?php esc_html_e( 'Ticket Tool / Reminder', 'solo-to-china' ); ?></h2>
						<p><?php esc_html_e( 'Check attraction booking timing and plan a reminder before your visit.', 'solo-to-china' ); ?></p>
					</div>
					<?php
					if ( shortcode_exists( 'solo_to_china_ticket_tool' ) ) {
						echo do_shortcode( '[solo_to_china_ticket_tool]' );
					} else {
						echo '<p>' . esc_html__( 'Activate the SoloToChina Tools plugin to use this tool.', 'solo-to-china' ) . '</p>';
					}
					?>
				</div>
			</section>
		<?php elseif ( 'faq' === $slug ) : ?>
			<section class="stc-faq stc-faq--page" aria-label="Frequently asked questions">
				<div class="stc-faq__grid">
					<?php foreach ( $page['items'] as $item ) : ?>
						<details>
							<summary><?php echo esc_html( $item['title'] ); ?></summary>
							<div class="stc-faq__answer">
								<p><?php echo esc_html( $item['copy'] ); ?></p>
								<a class="stc-faq__answer-link" href="<?php echo esc_url( home_url( $item['link'] ) ); ?>"><?php echo esc_html( $item['link_label'] ); ?></a>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
		</div>

		<?php if ( in_array( $slug, $guide_landing_slugs, true ) ) : ?>
			<section class="stc-saved-guides" aria-labelledby="stc-saved-guides-title">
				<div>
					<div class="stc-saved-guides__header">
						<h2 id="stc-saved-guides-title"><?php esc_html_e( 'Saved on this device', 'solo-to-china' ); ?></h2>
						<div class="stc-saved-guides__actions">
							<button type="button" data-stc-export-guides><?php esc_html_e( 'Export', 'solo-to-china' ); ?></button>
							<label>
								<span><?php esc_html_e( 'Import', 'solo-to-china' ); ?></span>
								<input type="file" accept="application/json,.json" data-stc-import-guides>
							</label>
							<button type="button" data-stc-clear-guides><?php esc_html_e( 'Clear all', 'solo-to-china' ); ?></button>
						</div>
					</div>
					<p class="stc-local-note"><?php esc_html_e( 'Stored only on this device. Export or clear it anytime.', 'solo-to-china' ); ?></p>
					<div data-stc-saved-guides></div>
				</div>
			</section>

			<?php stc_render_core_page_latest_guides( $slug ); ?>
		<?php endif; ?>
	<?php else : ?>
		<section class="stc-content">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<header class="stc-content__header">
					<h1><?php the_title(); ?></h1>
				</header>
				<div class="stc-entry-content">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		</section>
	<?php endif; ?>
</main>

<?php
get_footer();
