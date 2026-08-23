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
			[ 'title' => 'Beijing', 'copy' => 'History, culture, and classic first-trip routes.' ],
			[ 'title' => 'Shanghai', 'copy' => 'Modern China, neighborhoods, food, and day trips.' ],
			[ 'title' => 'Guangzhou', 'copy' => 'Food, trade culture, transit, and south China access.' ],
			[ 'title' => 'Chengdu', 'copy' => 'Pandas, teahouses, Sichuan food, and slow travel.' ],
			[ 'title' => 'Chongqing', 'copy' => 'River views, hotpot, hills, and night scenes.' ],
			[ 'title' => "Xi'an", 'copy' => 'Ancient capital routes and Terracotta Warriors planning.' ],
			[ 'title' => 'Hangzhou', 'copy' => 'West Lake, tea villages, and relaxed city breaks.' ],
			[ 'title' => 'Zhangjiajie', 'copy' => 'Mountain routes, tickets, weather, and transport.' ],
		],
	],
	'attraction-guides' => [
		'title' => 'Attraction Guides',
		'copy'  => 'Ticket timing, passport notes, best seasons, and practical visit planning.',
		'items' => [
			[ 'title' => 'Forbidden City', 'copy' => 'Booking required. Passport details matter.' ],
			[ 'title' => 'Great Wall', 'copy' => 'Choose the right section and transport plan.' ],
			[ 'title' => 'Terracotta Warriors', 'copy' => 'Plan museum time, city transfer, and ID checks.' ],
			[ 'title' => 'Zhangjiajie', 'copy' => 'Weather, cable cars, route order, and ticket windows.' ],
			[ 'title' => 'West Lake', 'copy' => 'Easy walks, boat options, and best viewing seasons.' ],
			[ 'title' => 'Shanghai Disney Resort', 'copy' => 'Peak dates, passport notes, and booking timing.' ],
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
			[ 'title' => 'Do I need a visa to visit China?', 'copy' => 'Check the latest entry rules for your passport before booking.' ],
			[ 'title' => 'How can I pay in China?', 'copy' => 'Set up mobile payment before arrival and keep a backup card or cash.' ],
			[ 'title' => 'Which apps are essential in China?', 'copy' => 'Maps, translation, payments, ride hailing, and train tools are the basics.' ],
			[ 'title' => 'Can I use Google, WhatsApp, and other services in China?', 'copy' => 'Some services may not work normally. Plan your connection setup in advance.' ],
			[ 'title' => 'Is China safe for solo travelers?', 'copy' => 'Most trips are straightforward with normal urban travel awareness and planning.' ],
			[ 'title' => 'How do I get around between cities?', 'copy' => 'High-speed rail is often the easiest option between major cities.' ],
		],
	],
];

$page = $core_pages[ $slug ] ?? null;

if ( ! function_exists( 'stc_render_save_guide_button' ) ) {
	function stc_render_save_guide_button( $item, $type ) {
		echo '<button class="stc-save-guide" type="button" data-stc-save-guide data-guide-id="' . esc_attr( sanitize_title( $type . '-' . $item['title'] ) ) . '" data-guide-type="' . esc_attr( $type ) . '" data-guide-title="' . esc_attr( $item['title'] ) . '" data-guide-copy="' . esc_attr( $item['copy'] ) . '">' . esc_html__( 'Save', 'solo-to-china' ) . '</button>';
	}
}
?>

<main id="main" class="stc-main">
	<?php if ( $page ) : ?>
		<section class="stc-page-hero">
			<p><?php esc_html_e( 'SoloToChina', 'solo-to-china' ); ?></p>
			<h1><?php echo esc_html( $page['title'] ); ?></h1>
			<span><?php echo esc_html( $page['copy'] ); ?></span>
			<div class="stc-page-actions">
				<button type="button" data-stc-share-page data-share-title="<?php echo esc_attr( $page['title'] ); ?>" data-share-url="<?php echo esc_url( get_permalink() ); ?>">
					<?php esc_html_e( 'Share page', 'solo-to-china' ); ?>
				</button>
			</div>
		</section>

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

		<?php if ( 'survival-kit' === $slug ) : ?>
			<section class="stc-page-section">
				<div class="stc-link-grid stc-link-grid--five">
					<?php foreach ( $page['items'] as $item ) : ?>
						<article class="stc-info-card">
							<?php stc_render_survival_icon( $item['icon'] ); ?>
							<h2><?php echo esc_html( $item['title'] ); ?></h2>
							<p><?php echo esc_html( $item['copy'] ); ?></p>
							<?php stc_render_save_guide_button( $item, 'Survival Kit' ); ?>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php elseif ( in_array( $slug, [ 'city-guides', 'attraction-guides' ], true ) ) : ?>
			<section class="stc-page-section">
				<div class="stc-link-grid">
					<?php foreach ( $page['items'] as $item ) : ?>
						<article class="stc-info-card">
							<h2><?php echo esc_html( $item['title'] ); ?></h2>
							<p><?php echo esc_html( $item['copy'] ); ?></p>
							<?php stc_render_save_guide_button( $item, 'city-guides' === $slug ? 'City Guide' : 'Attraction Guide' ); ?>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php elseif ( 'planner' === $slug ) : ?>
			<section class="stc-page-section">
				<div class="stc-feature-panel">
					<div>
						<h2><?php esc_html_e( 'Plan, then book with confidence', 'solo-to-china' ); ?></h2>
						<p><?php esc_html_e( 'Use the guides first, then open Trip.com when you are ready to compare hotels, trains, flights, or activities.', 'solo-to-china' ); ?></p>
					</div>
					<a class="stc-button stc-button--secondary" href="https://www.trip.com/" target="_blank" rel="sponsored noopener"><?php esc_html_e( 'Open Trip.com', 'solo-to-china' ); ?></a>
				</div>
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
			<section class="stc-page-section">
				<div class="stc-faq__grid">
					<?php foreach ( $page['items'] as $item ) : ?>
						<details>
							<summary><?php echo esc_html( $item['title'] ); ?></summary>
							<p><?php echo esc_html( $item['copy'] ); ?></p>
						</details>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php stc_render_core_page_latest_guides( $slug ); ?>
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
