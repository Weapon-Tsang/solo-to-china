<?php
/**
 * SoloToChina theme setup.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_THEME_VERSION', '0.20.0' );

function stc_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'stc-guide-card-2x', 960, 0, false );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

	register_nav_menus(
		[
			'primary' => __( 'Primary Navigation', 'solo-to-china' ),
		]
	);
}
add_action( 'after_setup_theme', 'stc_theme_setup' );

function stc_enqueue_assets() {
	wp_enqueue_style(
		'stc-main',
		get_template_directory_uri() . '/assets/css/main.css',
		[],
		STC_THEME_VERSION
	);

	wp_enqueue_script(
		'stc-main',
		get_template_directory_uri() . '/assets/js/main.js',
		[],
		STC_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'stc_enqueue_assets' );

function stc_primary_navigation_items() {
	return [
		[ 'label' => 'Home', 'url' => home_url( '/' ) ],
		[ 'label' => 'Survival Kit', 'url' => home_url( '/survival-kit/' ) ],
		[ 'label' => 'City Guides', 'url' => home_url( '/city-guides/' ) ],
		[ 'label' => 'Attraction Guides', 'url' => home_url( '/attraction-guides/' ) ],
		[ 'label' => 'Planner', 'url' => home_url( '/planner/' ) ],
		[ 'label' => 'Tools', 'url' => home_url( '/tools/' ) ],
		[ 'label' => 'FAQ', 'url' => home_url( '/faq/' ) ],
	];
}

function stc_render_primary_navigation() {
	echo '<nav id="stc-primary-nav" class="stc-nav" aria-label="' . esc_attr__( 'Primary navigation', 'solo-to-china' ) . '">';
	foreach ( stc_primary_navigation_items() as $item ) {
		echo '<a class="stc-nav__link" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
	}
	echo '</nav>';
}

function stc_core_pages() {
	return [
		'survival-kit'      => 'Survival Kit',
		'city-guides'       => 'City Guides',
		'attraction-guides' => 'Attraction Guides',
		'planner'           => 'Planner',
		'tools'             => 'Tools',
		'faq'               => 'FAQ',
	];
}

function stc_core_guide_categories() {
	return [
		'survival-kit'      => [
			'name'        => 'Survival Kit',
			'description' => 'Practical setup and troubleshooting guides for first-time China travel.',
		],
		'city-guides'       => [
			'name'        => 'City Guides',
			'description' => 'City strategy guides for where to stay, how to move, and what to do.',
		],
		'attraction-guides' => [
			'name'        => 'Attraction Guides',
			'description' => 'Scenic spot and attraction guides for timing, transport, tickets, and route planning.',
		],
	];
}

function stc_ensure_core_pages() {
	foreach ( stc_core_pages() as $slug => $title ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}

		wp_insert_post(
			[
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '<!-- SoloToChina core landing page. The theme renders this page from its slug. -->',
			]
		);
	}
}
add_action( 'after_switch_theme', 'stc_ensure_core_pages' );

function stc_ensure_core_categories() {
	foreach ( stc_core_guide_categories() as $slug => $category ) {
		if ( term_exists( $slug, 'category' ) ) {
			continue;
		}

		wp_insert_term(
			$category['name'],
			'category',
			[
				'description' => $category['description'],
				'slug'        => $slug,
			]
		);
	}
}
add_action( 'after_switch_theme', 'stc_ensure_core_categories' );

function stc_is_attraction_guide_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	return has_category( 'attraction-guides', $post_id ) || has_tag( 'attraction-guide', $post_id );
}

function stc_is_city_guide_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	return has_category( 'city-guides', $post_id ) || has_tag( 'city-guide', $post_id );
}

function stc_is_survival_kit_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	return has_category( 'survival-kit', $post_id ) || has_tag( 'survival-kit', $post_id );
}

function stc_get_guide_type_slug( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( stc_is_survival_kit_post( $post_id ) ) {
		return 'survival-kit';
	}

	if ( stc_is_attraction_guide_post( $post_id ) ) {
		return 'attraction-guide';
	}

	if ( stc_is_city_guide_post( $post_id ) ) {
		return 'city-guide';
	}

	return 'travel-guide';
}

function stc_get_guide_type_label( $post_id = null ) {
	$slug = stc_get_guide_type_slug( $post_id );

	$labels = [
		'survival-kit'     => __( 'Survival Kit', 'solo-to-china' ),
		'attraction-guide' => __( 'Attraction Guide', 'solo-to-china' ),
		'city-guide'       => __( 'City Guide', 'solo-to-china' ),
		'travel-guide'     => __( 'Travel Guide', 'solo-to-china' ),
	];

	return $labels[ $slug ] ?? $labels['travel-guide'];
}

function stc_render_guide_card_media( $image_file, $alt = '' ) {
	$image_url = get_template_directory_uri() . '/assets/images/' . ltrim( $image_file, '/' );

	echo '<span class="stc-image-card__media">';
	echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt ) . '" width="960" height="1200" loading="lazy" decoding="async">';
	echo '</span>';
}

function stc_render_faq_chevron() {
	echo '<svg class="stc-faq__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false">';
	echo '<path d="m7 9 5 5 5-5" />';
	echo '</svg>';
}

function stc_render_guide_card( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	$type_slug  = stc_get_guide_type_slug( $post_id );
	$type_label = stc_get_guide_type_label( $post_id );
	$classes    = get_post_class( [ 'stc-post-card', 'stc-post-card--' . $type_slug ], $post_id );
	$excerpt    = get_the_excerpt( $post_id );
	$date_attr  = get_the_date( DATE_W3C, $post_id );
	$date_label = get_the_date( '', $post_id );

	echo '<article class="' . esc_attr( implode( ' ', $classes ) ) . '">';
	echo '<a class="stc-post-card__link" href="' . esc_url( get_permalink( $post_id ) ) . '">';
	if ( has_post_thumbnail( $post_id ) ) {
		echo '<span class="stc-post-card__media">';
		echo wp_get_attachment_image(
			get_post_thumbnail_id( $post_id ),
			'stc-guide-card-2x',
			false,
			[
				'class'    => 'stc-post-card__image',
				'loading'  => 'lazy',
				'decoding' => 'async',
				'sizes'    => '(max-width: 720px) calc(100vw - 40px), (max-width: 1100px) calc(50vw - 48px), 360px',
			]
		);
		echo '</span>';
	}
	echo '<div class="stc-post-card__meta">';
	echo '<span class="stc-post-card__type">' . esc_html( $type_label ) . '</span>';
	echo '<time datetime="' . esc_attr( $date_attr ) . '">' . esc_html( $date_label ) . '</time>';
	echo '</div>';
	echo '<h2>' . esc_html( get_the_title( $post_id ) ) . '</h2>';
	if ( $excerpt ) {
		echo '<p>' . esc_html( wp_trim_words( $excerpt, 28 ) ) . '</p>';
	}
	echo '<span class="stc-post-card__cta">' . esc_html__( 'Read guide', 'solo-to-china' ) . '</span>';
	echo '</a>';
	echo '</article>';
}

function stc_render_guide_toc( $modifier_class = '' ) {
	$classes = trim( 'stc-guide-toc ' . sanitize_html_class( $modifier_class ) );

	echo '<nav class="' . esc_attr( $classes ) . '" aria-label="' . esc_attr__( 'On this page', 'solo-to-china' ) . '" data-stc-guide-toc>';
	echo '<h2>' . esc_html__( 'On this page', 'solo-to-china' ) . '</h2>';
	echo '<ol data-stc-guide-toc-list></ol>';
	echo '</nav>';
}

function stc_render_article_save_button( $guide_type ) {
	$post_id = get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	$title   = get_the_title( $post_id );
	$excerpt = get_the_excerpt( $post_id );
	$copy    = $excerpt ? wp_trim_words( $excerpt, 24 ) : __( 'Saved for practical trip planning.', 'solo-to-china' );
	$id      = sanitize_title( $guide_type . '-' . $title );

	echo '<button class="stc-save-guide stc-article-save" type="button" aria-pressed="false" data-stc-save-guide data-guide-id="' . esc_attr( $id ) . '" data-guide-type="' . esc_attr( $guide_type ) . '" data-guide-title="' . esc_attr( $title ) . '" data-guide-copy="' . esc_attr( $copy ) . '">' . esc_html__( 'Save guide', 'solo-to-china' ) . '</button>';
}

function stc_core_page_latest_guides_config( $slug ) {
	$config = [
		'survival-kit'      => [
			'category' => 'survival-kit',
			'label'    => __( 'Latest Survival Kit guides', 'solo-to-china' ),
			'empty'    => __( 'Published Survival Kit guides will appear here.', 'solo-to-china' ),
		],
		'city-guides'       => [
			'category' => 'city-guides',
			'label'    => __( 'Latest City Guides', 'solo-to-china' ),
			'empty'    => __( 'Published City Guide articles will appear here.', 'solo-to-china' ),
		],
		'attraction-guides' => [
			'category' => 'attraction-guides',
			'label'    => __( 'Latest Attraction Guides', 'solo-to-china' ),
			'empty'    => __( 'Published Attraction Guide articles will appear here.', 'solo-to-china' ),
		],
	];

	return $config[ $slug ] ?? null;
}

function stc_render_core_page_latest_guides( $slug ) {
	$config = stc_core_page_latest_guides_config( $slug );

	if ( ! $config ) {
		return;
	}

	$query = new WP_Query(
		[
			'category_name'       => $config['category'],
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'post_type'           => 'post',
			'posts_per_page'      => 6,
		]
	);

	$category     = get_category_by_slug( $config['category'] );
	$archive_link = $category ? get_category_link( $category ) : home_url( '/category/' . $config['category'] . '/' );

	echo '<section class="stc-page-section stc-latest-guides" aria-labelledby="stc-latest-guides-title">';
	echo '<div class="stc-latest-guides__header">';
	echo '<div>';
	echo '<p>' . esc_html__( 'Fresh practical guides', 'solo-to-china' ) . '</p>';
	echo '<h2 id="stc-latest-guides-title">' . esc_html( $config['label'] ) . '</h2>';
	echo '</div>';
	echo '<a href="' . esc_url( $archive_link ) . '">' . esc_html__( 'Browse all', 'solo-to-china' ) . '</a>';
	echo '</div>';

	if ( $query->have_posts() ) {
		echo '<div class="stc-post-list">';
		while ( $query->have_posts() ) {
			$query->the_post();
			stc_render_guide_card( get_the_ID() );
		}
		echo '</div>';
	} else {
		echo '<p class="stc-latest-guides__empty">' . esc_html( $config['empty'] ) . '</p>';
	}

	wp_reset_postdata();

	echo '</section>';
}

function stc_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'solo-to-china',
			[ 'label' => __( 'SoloToChina', 'solo-to-china' ) ]
		);
	}

	$attraction_guide_content = '<!-- wp:paragraph {"className":"stc-guide-intro"} --><p>Start with the practical answer: who should visit, how much time to allow, and what travelers should decide before they go.</p><!-- /wp:paragraph -->'
		. '<!-- wp:group {"className":"stc-guide-quick-facts"} --><div class="wp-block-group stc-guide-quick-facts">'
		. '<!-- wp:heading {"level":2} --><h2>At a glance</h2><!-- /wp:heading -->'
		. '<!-- wp:group {"className":"stc-guide-facts-grid"} --><div class="wp-block-group stc-guide-facts-grid">'
		. '<!-- wp:group {"className":"stc-guide-fact"} --><div class="wp-block-group stc-guide-fact"><!-- wp:paragraph --><p><strong>Best time</strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Example: April-May or September-October for calmer weather and lighter crowds.</p><!-- /wp:paragraph --></div><!-- /wp:group -->'
		. '<!-- wp:group {"className":"stc-guide-fact"} --><div class="wp-block-group stc-guide-fact"><!-- wp:paragraph --><p><strong>Time needed</strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Example: 3-4 hours, half day, or full day depending on route and queues.</p><!-- /wp:paragraph --></div><!-- /wp:group -->'
		. '<!-- wp:group {"className":"stc-guide-fact"} --><div class="wp-block-group stc-guide-fact"><!-- wp:paragraph --><p><strong>Reservation window</strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Example: check tickets 7 days ahead, earlier during Chinese public holidays.</p><!-- /wp:paragraph --></div><!-- /wp:group -->'
		. '<!-- wp:group {"className":"stc-guide-fact"} --><div class="wp-block-group stc-guide-fact"><!-- wp:paragraph --><p><strong>Passport note</strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Explain whether foreign visitors need passport details, real-name booking, or ID checks at entry.</p><!-- /wp:paragraph --></div><!-- /wp:group -->'
		. '<!-- wp:group {"className":"stc-guide-fact"} --><div class="wp-block-group stc-guide-fact"><!-- wp:paragraph --><p><strong>Best base area</strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Name the easiest nearby district, metro area, or city base for first-time visitors.</p><!-- /wp:paragraph --></div><!-- /wp:group -->'
		. '</div><!-- /wp:group --></div><!-- /wp:group -->'
		. '<!-- wp:group {"className":"stc-guide-warning"} --><div class="wp-block-group stc-guide-warning">'
		. '<!-- wp:heading {"level":2} --><h2>Before you book</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Call out the one or two decisions travelers should make before paying: ticket type, entry time, passport requirement, transport home, or weather risk.</p><!-- /wp:paragraph -->'
		. '</div><!-- /wp:group -->'
		. '<!-- wp:heading {"level":2} --><h2>Best time to visit</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Cover seasons, weather, crowd levels, photography windows, and when solo or first-time visitors should avoid peak pressure.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>How to get there</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Explain metro, taxi, high-speed rail, airport, walking, and last-mile details in plain English.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Tickets and prices</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>List price ranges, common ticket types, what is included, and passport or real-name entry notes.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Opening and booking timing</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Explain opening hours, closed days, how many days ahead to check tickets, and when to use the reminder tool.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Where to stay</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Recommend the best nearby or connected areas for first-time visitors, with tradeoffs for price, transit, and late arrivals.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Common mistakes</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Call out traps, confusing entrances, timing mistakes, overpacked routes, taxi issues, and holiday crowd risks.</p><!-- /wp:paragraph -->'
		. '<!-- wp:group {"className":"stc-guide-route"} --><div class="wp-block-group stc-guide-route">'
		. '<!-- wp:heading {"level":2} --><h2>Suggested route</h2><!-- /wp:heading -->'
		. '<!-- wp:list {"ordered":true} --><ol><!-- wp:list-item --><li>Start with the easiest gate, metro exit, or visitor center for foreign travelers.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Visit the must-see section before peak crowds or harsh weather.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Add one slower stop for food, shade, views, or a clean restroom break.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Exit through the transport-friendly side and avoid a late taxi bottleneck.</li><!-- /wp:list-item --></ol><!-- /wp:list -->'
		. '</div><!-- /wp:group -->'
		. '<!-- wp:heading {"level":2} --><h2>FAQ</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Answer the most likely first-time visitor questions in short, direct blocks.</p><!-- /wp:paragraph -->';

	register_block_pattern(
		'solo-to-china/attraction-guide-v1',
		[
			'title'       => __( 'Attraction Guide v1', 'solo-to-china' ),
			'description' => __( 'A practical SoloToChina article structure for scenic spot and attraction guides.', 'solo-to-china' ),
			'categories'  => [ 'solo-to-china' ],
			'content'     => $attraction_guide_content,
		]
	);

	$city_guide_content = '<!-- wp:paragraph {"className":"stc-guide-intro"} --><p>Start with the city answer: who this city is best for, how many days to stay, and what first-time visitors should plan before arrival.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Best areas to stay</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Compare the best neighborhoods or districts for first-time visitors, solo travelers, transport access, price, nightlife, and early departures.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Getting around</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Explain metro, taxi, ride-hailing, airport or railway station transfers, walkability, and language friction in plain English.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>First-time itinerary</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Give a simple 1-3 day route for a calm first visit, with realistic travel time and room for meals or rest.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Food and neighborhoods</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Introduce food areas, useful local dishes, market or street-food expectations, and how to avoid tourist-only traps.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Day trips and nearby attractions</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>List nearby scenic spots, ancient towns, museums, mountains, or transit-friendly extensions that pair naturally with this city.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Common city mistakes</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Call out rushed routes, wrong station choices, late-night arrival issues, holiday crowding, weather surprises, and payment or app friction.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>FAQ</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Answer the most likely first-time city questions in short, practical blocks.</p><!-- /wp:paragraph -->';

	register_block_pattern(
		'solo-to-china/city-guide-v1',
		[
			'title'       => __( 'City Guide v1', 'solo-to-china' ),
			'description' => __( 'A practical SoloToChina article structure for city guides.', 'solo-to-china' ),
			'categories'  => [ 'solo-to-china' ],
			'content'     => $city_guide_content,
		]
	);

	$survival_kit_content = '<!-- wp:paragraph {"className":"stc-guide-intro"} --><p>Start with the practical answer: what the traveler should do, when to do it, and what to prepare before arriving in China.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Quick answer</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Give the short, confidence-building answer first, including who this applies to and the safest default choice.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>What to set up before arrival</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>List the items to prepare before departure, such as payment setup, Essential Apps, eSIM and internet, Visa and entry documents, VPN and access needs.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Step-by-step setup</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Break the setup into simple steps a first-time visitor can follow without Chinese language confidence.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>What can go wrong</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Explain the common failure points, confusing app screens, payment issues, passport checks, blocked services, or arrival-day friction.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>Backup plan</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Give a fallback route if the main setup fails, including cash/card options, hotel help, airport counters, offline screenshots, and alternate apps.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2>FAQ</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Answer the most likely first-time visitor questions in short, practical blocks.</p><!-- /wp:paragraph -->';

	register_block_pattern(
		'solo-to-china/survival-kit-v1',
		[
			'title'       => __( 'Survival Kit v1', 'solo-to-china' ),
			'description' => __( 'A practical SoloToChina article structure for first-time China travel survival topics.', 'solo-to-china' ),
			'categories'  => [ 'solo-to-china' ],
			'content'     => $survival_kit_content,
		]
	);
}
add_action( 'init', 'stc_register_block_patterns' );

function stc_render_survival_icon( $icon ) {
	$paths = [
		'payment' => '<path d="M12 3v18"/><path d="M17 7.5c-.9-1-2.2-1.5-4-1.5-2.4 0-4 1.1-4 2.8 0 4.1 8 1.7 8 6 0 1.8-1.7 3.2-4.2 3.2-1.8 0-3.3-.6-4.3-1.7"/>',
		'apps'    => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>',
		'esim'    => '<path d="M5 8a7 7 0 0 1 14 0"/><path d="M8 11a4 4 0 0 1 8 0"/><path d="M12 15h.01"/><path d="M9 18h6"/>',
		'visa'    => '<rect x="6" y="3" width="12" height="18" rx="2"/><path d="M9 7h6"/><path d="M9 11h6"/><path d="M9 15h3"/>',
		'vpn'     => '<path d="M4 9a12 12 0 0 1 16 0"/><path d="M7 12a7.5 7.5 0 0 1 10 0"/><path d="M10 15a3 3 0 0 1 4 0"/><path d="M12 19h.01"/>',
	];

	$path = $paths[ $icon ] ?? $paths['apps'];

	echo '<span class="stc-survival-card__icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false">' . $path . '</svg></span>';
}
