<?php
/**
 * Disposable WordPress Playground fixtures for content-system integration tests.
 *
 * This file is mounted into Playground only. It is not included in release packages.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return representative articles composed only from supported Gutenberg blocks.
 *
 * @return array<int, array<string, string>>
 */
function stc_playground_fixture_definitions() {
	return array(
		array(
			'title'          => 'China Mobile Payment Setup',
			'slug'           => 'china-mobile-payment-setup',
			'category'       => 'survival-kit',
			'category_label' => 'Survival Kit',
			'guide_type'     => 'survival-kit',
			'excerpt'        => 'A calm pre-arrival checklist for setting up mobile payment and keeping a reliable backup.',
			'content'        => <<<'HTML'
<!-- wp:paragraph {"className":"stc-guide-intro"} -->
<p class="stc-guide-intro">Complete the account and card checks before departure, then keep a physical fallback for the first day.</p>
<!-- /wp:paragraph -->
<!-- wp:group {"className":"stc-content-block stc-content-block--quick-answer","layout":{"type":"constrained"}} -->
<div class="wp-block-group stc-content-block stc-content-block--quick-answer"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Quick answer</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Set up one primary payment app, verify your identity early, and carry a second card plus a small amount of cash.</p><!-- /wp:paragraph --></div>
<!-- /wp:group -->
<!-- wp:group {"className":"stc-content-block stc-content-block--key-takeaways","layout":{"type":"constrained"}} -->
<div class="wp-block-group stc-content-block stc-content-block--key-takeaways"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Key takeaways</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li>Use the same name order shown on your passport.</li><li>Finish bank verification before departure.</li><li>Keep a payment fallback that does not depend on one phone.</li></ul><!-- /wp:list --></div>
<!-- /wp:group -->
<!-- wp:group {"className":"stc-content-block stc-content-block--steps","layout":{"type":"constrained"}} -->
<div class="wp-block-group stc-content-block stc-content-block--steps"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Step-by-step setup</h2><!-- /wp:heading --><!-- wp:list {"ordered":true} --><ol class="wp-block-list"><li>Install the current official app.</li><li>Create the account with a reachable phone number.</li><li>Complete passport identity verification.</li><li>Add a supported card and test the account status.</li></ol><!-- /wp:list --></div>
<!-- /wp:group -->
<!-- wp:group {"className":"stc-content-block stc-content-block--warning","layout":{"type":"constrained"}} -->
<div class="wp-block-group stc-content-block stc-content-block--warning"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">What can go wrong</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Bank verification, name mismatches, network access, and transaction limits are the common friction points. Never depend on an untested setup for airport transport.</p><!-- /wp:paragraph --></div>
<!-- /wp:group -->
<!-- wp:group {"className":"stc-content-block stc-content-block--comparison","layout":{"type":"constrained"}} -->
<div class="wp-block-group stc-content-block stc-content-block--comparison"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Backup comparison</h2><!-- /wp:heading --><!-- wp:table --><figure class="wp-block-table"><table><thead><tr><th>Option</th><th>Best use</th><th>Limit</th></tr></thead><tbody><tr><td>Second card</td><td>Hotels and larger merchants</td><td>Acceptance varies</td></tr><tr><td>Cash</td><td>Small emergency purchases</td><td>Change may be limited</td></tr><tr><td>Second app</td><td>Account-service backup</td><td>Still phone dependent</td></tr></tbody></table></figure><!-- /wp:table --></div>
<!-- /wp:group -->
<!-- wp:shortcode -->[stc_affiliate_cta category="Connectivity" provider="Trip.com" title="Compare a travel eSIM before departure" description="Review coverage and data terms alongside your payment backup plan." price_text="Check current provider terms before purchase." cta_label="Compare eSIM options" target_url="https://www.trip.com/" disclosure="Affiliate link. SoloToChina may earn a commission at no extra cost to you." anchor="payment-connectivity-option"]<!-- /wp:shortcode -->
<!-- wp:group {"className":"stc-content-block stc-content-block--faq","layout":{"type":"constrained"}} -->
<div class="wp-block-group stc-content-block stc-content-block--faq"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">FAQ</h2><!-- /wp:heading --><!-- wp:details --><details class="wp-block-details"><summary>Should I set this up before arriving?</summary><!-- wp:paragraph --><p>Yes. Early setup leaves time to resolve identity or bank checks while your usual support channels are easy to reach.</p><!-- /wp:paragraph --></details><!-- /wp:details --><!-- wp:details --><details class="wp-block-details"><summary>Should I still carry cash?</summary><!-- wp:paragraph --><p>A modest emergency amount is sensible even when the mobile setup is working.</p><!-- /wp:paragraph --></details><!-- /wp:details --></div>
<!-- /wp:group -->
HTML,
		),
		array(
			'title'          => 'Beijing First-Time City Guide',
			'slug'           => 'beijing-first-time-city-guide',
			'category'       => 'city-guides',
			'category_label' => 'City Guides',
			'guide_type'     => 'city-guide',
			'excerpt'        => 'Where to stay, how to move around, and how to build a realistic first Beijing itinerary.',
			'content'        => <<<'HTML'
<!-- wp:paragraph {"className":"stc-guide-intro"} --><p class="stc-guide-intro">Beijing is easier when you plan by area instead of collecting distant sights.</p><!-- /wp:paragraph -->
<!-- wp:group {"className":"stc-content-block stc-content-block--quick-facts","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--quick-facts"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">At a glance</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><strong>Best base:</strong> Dongcheng for a first visit.</li><li><strong>Useful stay:</strong> Four to five nights.</li><li><strong>Main transport:</strong> Metro plus walking.</li></ul><!-- /wp:list --></div><!-- /wp:group -->
<!-- wp:heading --><h2 class="wp-block-heading">Best areas to stay</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Choose Dongcheng for central access or a transport hub when day trips are the priority.</p><!-- /wp:paragraph -->
<!-- wp:group {"className":"stc-content-block stc-content-block--checklist","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--checklist"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Before each day</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li>Group sights by district.</li><li>Check reservation requirements.</li><li>Leave time for security and station walks.</li></ul><!-- /wp:list --></div><!-- /wp:group -->
<!-- wp:group {"className":"stc-content-block stc-content-block--steps","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--steps"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">A realistic first itinerary</h2><!-- /wp:heading --><!-- wp:list {"ordered":true} --><ol class="wp-block-list"><li>Start with the historic core.</li><li>Reserve a separate day for the Great Wall.</li><li>Use one flexible half day for weather or rest.</li></ol><!-- /wp:list --></div><!-- /wp:group -->
<!-- wp:shortcode -->[stc_planner_cta title="Build a calmer Beijing plan" description="Group each day by area and keep one flexible half day." cta_label="Open the trip planner" target_url="https://www.trip.com/" provider="Trip.com" disclosure="Partner link. SoloToChina may earn a commission at no extra cost to you." anchor="beijing-planner"]<!-- /wp:shortcode -->
<!-- wp:group {"className":"stc-content-block stc-content-block--faq","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--faq"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">FAQ</h2><!-- /wp:heading --><!-- wp:details --><details class="wp-block-details"><summary>Is the metro enough for a first visit?</summary><!-- wp:paragraph --><p>It covers most central areas, but station walks and transfers should be included in your timing.</p><!-- /wp:paragraph --></details><!-- /wp:details --></div><!-- /wp:group -->
HTML,
		),
		array(
			'title'          => 'Forbidden City: First-Time Visitor Guide',
			'slug'           => 'forbidden-city-first-time-visitor-guide',
			'category'       => 'attraction-guides',
			'category_label' => 'Attraction Guides',
			'guide_type'     => 'attraction-guide',
			'excerpt'        => 'A practical route through the Forbidden City, with booking timing, passport checks, transport, and a calmer first visit.',
			'content'        => <<<'HTML'
<!-- wp:paragraph {"className":"stc-guide-intro"} --><p class="stc-guide-intro">The Forbidden City rewards a little preparation. Prioritize clear entry logistics and enough breathing room for a first visit.</p><!-- /wp:paragraph -->
<!-- wp:group {"className":"stc-content-block stc-content-block--quick-facts","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--quick-facts"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">At a glance</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><strong>Best time:</strong> First entry window.</li><li><strong>Time needed:</strong> Three to four hours.</li><li><strong>Bring:</strong> The original reservation passport.</li></ul><!-- /wp:list --></div><!-- /wp:group -->
<!-- wp:heading --><h2 class="wp-block-heading">How to visit</h2><!-- /wp:heading -->
<!-- wp:group {"className":"stc-content-block stc-content-block--steps","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--steps"><!-- wp:list {"ordered":true} --><ol class="wp-block-list"><li>Confirm the current opening calendar.</li><li>Arrive before the reservation window.</li><li>Follow the central halls, then choose one side gallery.</li><li>Exit north and walk away from the busiest pickup area.</li></ol><!-- /wp:list --></div><!-- /wp:group -->
<!-- wp:group {"className":"stc-content-block stc-content-block--warning","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--warning"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Passport and booking warning</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Match every visitor name and passport number exactly, and carry the original passport used for the reservation.</p><!-- /wp:paragraph --></div><!-- /wp:group -->
<!-- wp:shortcode -->[stc_ticket_reminder attraction_slug="forbidden-city" title="Check the ticket timing before your Beijing days are fixed"]<!-- /wp:shortcode -->
<!-- wp:group {"className":"stc-content-block stc-content-block--faq","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--faq"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">FAQ</h2><!-- /wp:heading --><!-- wp:details --><details class="wp-block-details"><summary>Can I buy at the entrance?</summary><!-- wp:paragraph --><p>Do not rely on same-day availability. Check the official release timing before finalizing the itinerary.</p><!-- /wp:paragraph --></details><!-- /wp:details --></div><!-- /wp:group -->
HTML,
		),
	);
}

/**
 * Install or refresh the disposable fixtures.
 *
 * @return void
 */
function stc_playground_install_fixtures() {
	foreach ( stc_playground_fixture_definitions() as $fixture ) {
		$term = get_term_by( 'slug', $fixture['category'], 'category' );
		if ( ! $term ) {
			$created = wp_insert_term( $fixture['category_label'], 'category', array( 'slug' => $fixture['category'] ) );
			if ( ! is_wp_error( $created ) ) {
				$term = get_term( $created['term_id'], 'category' );
			}
		}

		$existing = get_page_by_path( $fixture['slug'], OBJECT, 'post' );
		$post_id  = wp_insert_post(
			array(
				'ID'           => $existing ? $existing->ID : 0,
				'post_title'   => $fixture['title'],
				'post_name'    => $fixture['slug'],
				'post_excerpt' => $fixture['excerpt'],
				'post_content' => $fixture['content'],
				'post_status'  => 'publish',
				'post_type'    => 'post',
			)
		);

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( $term && ! is_wp_error( $term ) ) {
				wp_set_post_categories( $post_id, array( (int) $term->term_id ) );
			}
			update_post_meta( $post_id, '_stc_guide_type', $fixture['guide_type'] );
			update_post_meta( $post_id, '_stc_content_contract_version', STC_CONTENT_CONTRACT_VERSION );
		}
	}

	flush_rewrite_rules();
}
