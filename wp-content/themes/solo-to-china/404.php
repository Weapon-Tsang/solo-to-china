<?php
/**
 * Not found template.
 *
 * @package SoloToChina
 */

get_header();
?>

<main id="main" class="stc-main">
	<section class="stc-content">
		<header class="stc-content__header">
			<p><?php esc_html_e( 'SoloToChina', 'solo-to-china' ); ?></p>
			<h1><?php esc_html_e( 'Page not found', 'solo-to-china' ); ?></h1>
			<p><?php esc_html_e( 'The guide you are looking for may have moved. Start from the main travel sections below.', 'solo-to-china' ); ?></p>
		</header>

		<div class="stc-link-grid">
			<a class="stc-info-card" href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">
				<h2><?php esc_html_e( 'Survival Kit', 'solo-to-china' ); ?></h2>
				<p><?php esc_html_e( 'Payments, apps, internet, visa, and first-trip basics.', 'solo-to-china' ); ?></p>
			</a>
			<a class="stc-info-card" href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">
				<h2><?php esc_html_e( 'City Guides', 'solo-to-china' ); ?></h2>
				<p><?php esc_html_e( 'Choose where to go and what to plan first.', 'solo-to-china' ); ?></p>
			</a>
			<a class="stc-info-card" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">
				<h2><?php esc_html_e( 'FAQ', 'solo-to-china' ); ?></h2>
				<p><?php esc_html_e( 'Short answers for common China travel questions.', 'solo-to-china' ); ?></p>
			</a>
		</div>
	</section>
</main>

<?php
get_footer();
