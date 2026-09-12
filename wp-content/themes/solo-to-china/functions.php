<?php
/**
 * SoloToChina theme setup.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STC_THEME_VERSION', '0.33.0' );
define( 'STC_SITE_PAGE_MIGRATION_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/component-registry.php';
require_once get_template_directory() . '/inc/content-contract.php';
require_once get_template_directory() . '/inc/content-components.php';
require_once get_template_directory() . '/inc/content-renderers.php';
require_once get_template_directory() . '/inc/commercial-events.php';
require_once get_template_directory() . '/inc/cms-articles.php';
require_once get_template_directory() . '/inc/entity-links.php';
require_once get_template_directory() . '/inc/site-collections.php';
require_once get_template_directory() . '/inc/experience-assets.php';
require_once get_template_directory() . '/inc/experience-components.php';

function stc_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'stc-guide-card-2x', 960, 0, false );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor-style.css' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

	register_nav_menus(
		[
			'primary' => __( 'Primary Navigation', 'solo-to-china' ),
		]
	);
}
add_action( 'after_setup_theme', 'stc_theme_setup' );

/**
 * Add deterministic server-rendered IDs to content H2 elements.
 *
 * Explicit Gutenberg/CMS anchors are preserved. Missing or duplicate heading
 * IDs receive readable stable slugs, so navigation remains useful without JS.
 *
 * @param string $content Rendered post content.
 * @return string
 */
function stc_add_stable_content_heading_ids( $content ) {
	if ( ! is_singular() || ! in_the_loop() || ! is_main_query() || false === stripos( $content, '<h2' ) ) {
		return $content;
	}

	$used_ids = array();
    preg_match_all('/<(?!h2\b)[a-z][^>]*\sid=[\"\']([^\"\']+)[\"\']/i', $content, $reserved);
    foreach ($reserved[1] as $id) { $used_ids[html_entity_decode($id,ENT_QUOTES,'UTF-8')]=true; }
    $index    = 0;

	return preg_replace_callback(
		'/<h2\b([^>]*)>(.*?)<\/h2>/is',
		function ( $matches ) use ( &$used_ids, &$index ) {
			$index++;
			$attributes  = $matches[1];
			$inner_html  = $matches[2];
			$explicit_id = '';

			if ( preg_match( '/\sid=(["\'])(.*?)\1/i', $attributes, $id_match ) ) {
				$explicit_id = sanitize_title( $id_match[2] );
			}

			$base_id = $explicit_id ? $explicit_id : sanitize_title( wp_strip_all_tags( $inner_html ) );
			$base_id = $base_id ? $base_id : 'section-' . $index;
			$heading_id = $base_id;
			$suffix     = 2;

			while ( isset( $used_ids[ $heading_id ] ) ) {
				$heading_id = $base_id . '-' . $suffix;
				$suffix++;
			}

			$used_ids[ $heading_id ] = true;

			if ( $explicit_id ) {
				$attributes = preg_replace( '/\sid=(["\']).*?\1/i', ' id="' . esc_attr( $heading_id ) . '"', $attributes, 1 );
			} else {
				$attributes = ' id="' . esc_attr( $heading_id ) . '"' . $attributes;
			}

			return '<h2' . $attributes . '>' . $inner_html . '</h2>';
		},
		$content
	);
}
add_filter( 'the_content', 'stc_add_stable_content_heading_ids', 12 );

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

	if ( preg_match( '/stc_(affiliate|hotel|ticket_cta|booking|esim|transport|commercial)|stc-affiliate/', stc_page_asset_content() ) ) {
	wp_enqueue_script(
		'stc-commercial-events',
		get_template_directory_uri() . '/assets/js/commercial-events.js',
		array(),
		STC_THEME_VERSION,
		true
	);
	wp_localize_script(
		'stc-commercial-events',
		'stcCommercialEvents',
		array( 'endpoint' => esc_url_raw( rest_url( 'stc/v1/commercial-events' ) ) )
	);
	}
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

/**
 * Return the single configurable Trip.Planner destination.
 *
 * Keep this separate from ticket, hotel, train, and other commercial URLs.
 *
 * @return string
 */
function stc_get_trip_planner_url() {
	return 'https://www.trip.com/t/bCPFQ85ZHW2';
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
		'about'             => 'About SoloToChina',
		'contact'           => 'Contact',
		'privacy-policy'    => 'Privacy Policy',
		'terms-of-use'      => 'Terms of Use',
		'affiliate-disclosure' => 'Affiliate Disclosure',
		'disclaimer'        => 'Disclaimer',
	];
}

/**
 * Return display metadata for Theme-owned support and legal pages.
 *
 * @return array<string, array<string, string>>
 */
function stc_static_page_metadata() {
	return [
		'about'                => [ 'title' => 'About SoloToChina', 'copy' => 'Practical China travel guidance for first-time, solo, and independent travelers.' ],
		'contact'              => [ 'title' => 'Contact', 'copy' => 'Questions, corrections, partnerships, and privacy requests.' ],
		'privacy-policy'       => [ 'title' => 'Privacy Policy', 'copy' => 'How SoloToChina handles information across this website and its external links.' ],
		'terms-of-use'         => [ 'title' => 'Terms of Use', 'copy' => 'Important terms for using SoloToChina travel information and third-party links.' ],
		'affiliate-disclosure' => [ 'title' => 'Affiliate Disclosure', 'copy' => 'How selected commercial links support SoloToChina without increasing the reader\'s price.' ],
		'disclaimer'           => [ 'title' => 'Disclaimer', 'copy' => 'Travel information changes; verify important decisions with current official sources.' ],
	];
}

/**
 * Return initial content for Theme-owned support and legal pages.
 *
 * This content is written only when a page is first created. Administrators
 * remain free to edit it in WordPress; migrations never overwrite it.
 *
 * @return array<string, string>
 */
function stc_static_page_content() {
	return [
		'about' => '<h2>Who SoloToChina is for</h2><p>SoloToChina helps independent travelers plan a first or solo trip to China with practical, decision-oriented guides.</p><ul><li>First-time China visitors</li><li>Solo travelers</li><li>Independent travelers</li></ul><h2>What we cover</h2><p>We publish practical guidance on payments, essential apps, connectivity, visa and entry preparation, City Guides, Attraction Guides, and realistic travel planning.</p><h2>How the site works</h2><p>SoloToChina combines practical editorial content with lightweight planning tools and selected third-party booking links. Travel articles and their component order are managed as editorial content; the site interface makes those decisions easy to use.</p><h2>How we make money</h2><p>SoloToChina may earn affiliate commissions from partners such as Trip.com. This does not increase the reader\'s price. Commercial relationships should not determine editorial conclusions.</p>',
		'contact' => '<h2>How to reach us</h2><p>For general questions, content corrections, partnership or affiliate enquiries, and privacy requests, contact SoloToChina using the details below.</p><dl><dt>Email</dt><dd><a href="mailto:alex@solotochina.com">alex@solotochina.com</a></dd><dt>WhatsApp</dt><dd><a href="https://wa.me/19098361987">+1 909-836-1987</a></dd><dt>Postal address</dt><dd>7953 Beckwith Rd<br>Morton Grove, IL 60053<br>US</dd></dl><p>Please include the page URL when reporting a content correction or privacy concern.</p>',
		'privacy-policy' => '<p><strong>Last updated:</strong> September 8, 2026</p><h2>Information we handle</h2><p>We may receive information you voluntarily provide, such as your name, email address, message, or other details included when you contact us. Our hosting and security systems may also process technical and server-log information such as IP address, browser type, requested pages, timestamps, and error or security events.</p><h2>Cookies and browser technologies</h2><p>SoloToChina may use cookies or similar browser technologies where needed for site operation, security, preferences, and enabled third-party features. Available technology can change as the site evolves; this policy does not claim that a particular analytics or advertising provider is in use unless it is actually configured.</p><h2>Third-party and affiliate links</h2><p>Our pages may link to third-party services, including external booking providers such as Trip.com. Those services operate under their own privacy policies. Some links are affiliate links, which may allow the provider to attribute an eligible purchase to SoloToChina.</p><h2>Retention and security</h2><p>We retain information only as long as reasonably needed for the purpose for which it was collected, legal obligations, dispute resolution, and site security. We use reasonable safeguards, but no online service can guarantee absolute security.</p><h2>International users</h2><p>SoloToChina serves readers in multiple countries. Information you send may be processed in the United States or other locations where our service providers operate, which may have different data-protection rules from your home country.</p><h2>Your privacy requests</h2><p>To ask about, correct, or request deletion of information you have provided directly to us, email <a href="mailto:alex@solotochina.com">alex@solotochina.com</a>. We may need to verify the request and may retain information where legally permitted or required.</p><h2>Policy changes</h2><p>We may revise this policy when the site or applicable requirements change. The fixed date above shows when this page was last substantively updated.</p><h2>Contact</h2><p>SoloToChina, 7953 Beckwith Rd, Morton Grove, IL 60053, US. Email: <a href="mailto:alex@solotochina.com">alex@solotochina.com</a>.</p>',
		'terms-of-use' => '<p><strong>Last updated:</strong> September 8, 2026</p><h2>Informational travel content</h2><p>SoloToChina provides general travel information. Information about visa and entry rules, attraction ticket rules, transport requirements, opening hours, prices, schedules, and availability can change without notice. Verify important travel decisions with current official sources.</p><h2>Third-party services and booking</h2><p>Links to booking providers and other third parties are provided for convenience. Their products, availability, prices, terms, support, and performance are controlled by those providers. Your transaction is with the provider you choose.</p><h2>Affiliate links</h2><p>Some links are affiliate links. SoloToChina may earn a commission from an eligible purchase at no extra cost to you. Affiliate relationships do not guarantee a product or outcome.</p><h2>Intellectual property</h2><p>Unless otherwise stated, SoloToChina site design, original text, graphics, and branding are protected by applicable intellectual-property laws. You may link to our pages and quote short portions with attribution, but may not republish substantial content without permission.</p><h2>Acceptable use</h2><p>Do not misuse the site, interfere with its operation, attempt unauthorized access, introduce malicious code, or use automated systems in a way that unreasonably burdens the service.</p><h2>Limitations</h2><p>Use of the site is at your own risk. To the extent permitted by law, SoloToChina is not responsible for losses arising from reliance on changing travel information, third-party services, or events outside our control.</p><h2>Changes and contact</h2><p>We may update these terms when the site changes. Questions may be sent to <a href="mailto:alex@solotochina.com">alex@solotochina.com</a>.</p>',
		'affiliate-disclosure' => '<p><strong>Last updated:</strong> September 8, 2026</p><h2>How affiliate links work</h2><p>SoloToChina may earn commissions from eligible purchases made through affiliate links. This does not increase the reader\'s price.</p><h2>Current commercial destinations</h2><p>Trip.com is currently one commercial or affiliate destination used on SoloToChina. Not every mention of Trip.com means paid placement, and other external links may be included without compensation.</p><h2>Editorial independence</h2><p>Editorial usefulness should remain independent of whether a commercial link exists. We aim to distinguish commercial actions with clear disclosure and to keep practical travel decisions at the center of the page.</p><h2>Questions</h2><p>For questions about a commercial relationship, email <a href="mailto:alex@solotochina.com">alex@solotochina.com</a>.</p>',
		'disclaimer' => '<p><strong>Last updated:</strong> September 8, 2026</p><h2>Travel information changes</h2><p>Visa and immigration rules, attraction ticket rules, opening hours, prices, transport schedules, internet or app accessibility, and third-party availability can change quickly. Examples and booking-window estimates are planning aids, not live inventory.</p><h2>Verify important decisions</h2><p>Check current official government, attraction, carrier, and provider sources before making important travel, entry, transport, or purchasing decisions.</p><h2>Scope of our information</h2><p>SoloToChina provides general travel information. It does not provide legal or immigration advice, and using the site does not create a professional or advisory relationship.</p><h2>External services</h2><p>Third-party websites and services are responsible for their own information, availability, pricing, security, and terms. SoloToChina cannot guarantee their content or performance.</p><h2>Contact</h2><p>To report outdated or incorrect information, email <a href="mailto:alex@solotochina.com">alex@solotochina.com</a> and include the relevant page URL.</p>',
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
		'travel-guides'     => [
			'name'        => 'Travel Guides',
			'description' => 'General travel guides using the default SoloToChina article shell.',
		],
	];
}

function stc_ensure_core_pages() {
	$static_content  = stc_static_page_content();
	$privacy_page_id = 0;
	$complete        = true;

	foreach ( stc_core_pages() as $slug => $title ) {
		$existing_page = get_page_by_path( $slug );
		if ( $existing_page ) {
			if ( 'privacy-policy' === $slug ) {
				$privacy_page_id = (int) $existing_page->ID;
			}
			continue;
		}

		$page_id = wp_insert_post(
			[
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => isset( $static_content[ $slug ] ) ? $static_content[ $slug ] : '<!-- SoloToChina core landing page. The theme renders this page from its slug. -->',
			]
		);
		if ( ! $page_id || is_wp_error( $page_id ) ) {
			$complete = false;
			continue;
		}

		if ( 'privacy-policy' === $slug ) {
			$privacy_page_id = (int) $page_id;
		}
	}

	if ( ! (int) get_option( 'wp_page_for_privacy_policy' ) && $privacy_page_id ) {
		update_option( 'wp_page_for_privacy_policy', $privacy_page_id );
	}

	return $complete;
}
add_action( 'after_switch_theme', 'stc_ensure_core_pages' );

/**
 * Run page creation once after a Theme upgrade, including when already active.
 *
 * @return void
 */
function stc_maybe_run_site_page_migration() {
	if ( STC_SITE_PAGE_MIGRATION_VERSION === get_option( 'stc_site_page_migration_version' ) ) {
		return;
	}

	if ( stc_ensure_core_pages() ) {
		update_option( 'stc_site_page_migration_version', STC_SITE_PAGE_MIGRATION_VERSION );
	}
}
add_action( 'admin_init', 'stc_maybe_run_site_page_migration' );

/**
 * Register the query variable used by the non-destructive static-page fallback.
 *
 * A fresh WordPress install may already contain an unpublished Privacy Policy
 * draft. The migration must not overwrite or publish administrator content, so
 * the public URL falls back to the Theme copy until that page is published.
 *
 * @param array<int, string> $query_vars Public query variables.
 * @return array<int, string>
 */
function stc_register_static_page_fallback_query_var( $query_vars ) {
	$query_vars[] = 'stc_static_page_fallback';
	return $query_vars;
}
add_filter( 'query_vars', 'stc_register_static_page_fallback_query_var' );

/**
 * Route an unpublished reserved static-page slug to a read-only Theme fallback.
 *
 * @param array<string, mixed> $query_vars Parsed request variables.
 * @return array<string, mixed>
 */
function stc_route_unpublished_static_page( $query_vars ) {
	$requested_slug = isset( $query_vars['pagename'] ) ? sanitize_title( trim( $query_vars['pagename'], '/' ) ) : '';
	$static_pages   = stc_static_page_metadata();

	if ( ! $requested_slug || ! isset( $static_pages[ $requested_slug ] ) ) {
		return $query_vars;
	}

	$published_pages = get_posts(
		[
			'name'           => $requested_slug,
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		]
	);

	if ( $published_pages ) {
		return $query_vars;
	}

	unset( $query_vars['pagename'], $query_vars['page_id'] );
	$query_vars['stc_static_page_fallback'] = $requested_slug;

	return $query_vars;
}
add_filter( 'request', 'stc_route_unpublished_static_page' );

/**
 * Select the static fallback template when an existing draft owns the slug.
 *
 * @param string $template Resolved WordPress template path.
 * @return string
 */
function stc_static_page_fallback_template( $template ) {
	if ( ! get_query_var( 'stc_static_page_fallback' ) ) {
		return $template;
	}

	$fallback = get_template_directory() . '/page-static-fallback.php';
	if ( is_readable( $fallback ) ) {
		status_header( 200 );
		return $fallback;
	}

	return $template;
}
add_filter( 'template_include', 'stc_static_page_fallback_template', 99 );

/**
 * Give virtual static fallbacks the same document title as a published page.
 *
 * @param array<string, string> $parts Document title parts.
 * @return array<string, string>
 */
function stc_static_page_fallback_document_title( $parts ) {
	$slug   = sanitize_title( (string) get_query_var( 'stc_static_page_fallback' ) );
	$pages  = stc_static_page_metadata();

	if ( $slug && isset( $pages[ $slug ] ) ) {
		$parts['title'] = $pages[ $slug ]['title'];
	}

	return $parts;
}
add_filter( 'document_title_parts', 'stc_static_page_fallback_document_title' );

/**
 * Add page-like body classes for virtual static fallbacks.
 *
 * @param array<int, string> $classes Body classes.
 * @return array<int, string>
 */
function stc_static_page_fallback_body_class( $classes ) {
	$slug = sanitize_title( (string) get_query_var( 'stc_static_page_fallback' ) );

	if ( $slug ) {
		$classes[] = 'page';
		$classes[] = 'stc-static-page-fallback';
		$classes[] = 'stc-static-page-fallback--' . sanitize_html_class( $slug );
	}

	return $classes;
}
add_filter( 'body_class', 'stc_static_page_fallback_body_class' );

/**
 * Output the public canonical URL for a static fallback route.
 */
function stc_static_page_fallback_canonical() {
	$slug = sanitize_title( (string) get_query_var( 'stc_static_page_fallback' ) );

	if ( $slug ) {
		echo '<link rel="canonical" href="' . esc_url( home_url( '/' . $slug . '/' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'stc_static_page_fallback_canonical', 1 );

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

	$explicit_type = stc_get_explicit_guide_type( $post_id );

	return 'attraction-guide' === $explicit_type || ( ! $explicit_type && ( has_category( 'attraction-guides', $post_id ) || has_tag( 'attraction-guide', $post_id ) ) );
}

function stc_is_city_guide_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	$explicit_type = stc_get_explicit_guide_type( $post_id );

	return 'city-guide' === $explicit_type || ( ! $explicit_type && ( has_category( 'city-guides', $post_id ) || has_tag( 'city-guide', $post_id ) ) );
}

function stc_is_survival_kit_post( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return false;
	}

	$explicit_type = stc_get_explicit_guide_type( $post_id );

	return 'survival-kit' === $explicit_type || ( ! $explicit_type && ( has_category( 'survival-kit', $post_id ) || has_tag( 'survival-kit', $post_id ) ) );
}

function stc_get_guide_type_slug( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$explicit_type = stc_get_explicit_guide_type( $post_id );

	if ( $explicit_type ) {
		return $explicit_type;
	}

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
	echo '<span class="stc-image-card__media">';
	stc_render_theme_image( pathinfo( $image_file, PATHINFO_FILENAME ), $alt );
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
	$content = isset( $GLOBALS['stc_rendered_article_content'] ) ? $GLOBALS['stc_rendered_article_content'] : '';
	preg_match_all( '/<h2\b[^>]*\bid=["\']([^"\']+)["\'][^>]*>(.*?)<\/h2>/is', $content, $headings, PREG_SET_ORDER );
	if ( ! $headings ) { return; }
	$classes = trim( 'stc-guide-toc ' . sanitize_html_class( $modifier_class ) );

	echo '<nav class="' . esc_attr( $classes ) . '" aria-label="' . esc_attr__( 'On this page', 'solo-to-china' ) . '" data-stc-guide-toc>';
	echo '<h2>' . esc_html__( 'On this page', 'solo-to-china' ) . '</h2>';
	echo '<ol data-stc-guide-toc-list>';
    foreach ( $headings as $heading ) {
        echo '<li><a href="#' . esc_attr( $heading[1] ) . '">' . esc_html( wp_strip_all_tags( $heading[2] ) ) . '</a></li>';
    }
    echo '</ol>';
	echo '</nav>';
}

/**
 * Render the reusable Share This Page utility.
 *
 * The CMS decides whether it is present; the Theme only renders and enhances it.
 *
 * @param array<string, mixed> $args Optional post, title, and description values.
 */
function stc_render_share_this_page( $args = array() ) {
	$post_id     = isset( $args['post_id'] ) ? (int) $args['post_id'] : (int) get_the_ID();
	$title       = isset( $args['title'] ) ? sanitize_text_field( $args['title'] ) : get_the_title( $post_id );
	$description = isset( $args['description'] ) ? sanitize_text_field( $args['description'] ) : get_the_excerpt( $post_id );
	$canonical   = $post_id ? wp_get_canonical_url( $post_id ) : '';
	$canonical   = $canonical ? $canonical : ( $post_id ? get_permalink( $post_id ) : home_url( '/' ) );
	$panel_id    = wp_unique_id( 'stc-share-panel-' );
	$heading_id  = $panel_id . '-title';

	if ( ( $post_id && ( 'publish' !== get_post_status( $post_id ) || post_password_required( $post_id ) ) ) || ! $title || ! wp_http_validate_url( $canonical ) ) {
		return;
	}

	$whatsapp_url = 'https://wa.me/?text=' . rawurlencode( $title . ' — ' . $canonical );
	$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $canonical );
	$reddit_url   = 'https://www.reddit.com/submit?url=' . rawurlencode( $canonical ) . '&title=' . rawurlencode( $title );
	$x_url        = 'https://twitter.com/intent/tweet?url=' . rawurlencode( $canonical ) . '&text=' . rawurlencode( $title );

	echo '<div class="stc-share" data-stc-share data-share-title="' . esc_attr( $title ) . '" data-share-description="' . esc_attr( wp_trim_words( $description, 28 ) ) . '" data-share-canonical="' . esc_url( $canonical ) . '">';
	echo '<button class="stc-share__trigger" type="button" aria-expanded="false" aria-controls="' . esc_attr( $panel_id ) . '" aria-label="' . esc_attr__( 'Share this page', 'solo-to-china' ) . '" data-stc-share-trigger>';
	echo '<span class="stc-share__trigger-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg></span>';
	echo '<span class="stc-share__trigger-copy"><strong>' . esc_html__( 'Share', 'solo-to-china' ) . '</strong></span>';
	echo '<svg class="stc-share__trigger-arrow" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="m6 8 4 4 4-4"/></svg></button>';
	echo '<div id="' . esc_attr( $panel_id ) . '" class="stc-share__panel" role="dialog" aria-labelledby="' . esc_attr( $heading_id ) . '" data-stc-share-panel hidden>';
	echo '<div class="stc-share__panel-heading"><strong id="' . esc_attr( $heading_id ) . '">' . esc_html__( 'Share this guide', 'solo-to-china' ) . '</strong>';
	echo '<button class="stc-share__close" type="button" aria-label="' . esc_attr__( 'Close sharing options', 'solo-to-china' ) . '" data-stc-share-close><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7 7 10 10M17 7 7 17"/></svg></button></div>';
	echo '<p class="stc-share__panel-copy">' . esc_html__( 'Choose a social platform or copy the link.', 'solo-to-china' ) . '</p>';
	echo '<div class="stc-share__channels">';
	echo '<button class="stc-share__channel stc-share__copy" type="button" data-stc-share-copy><span class="stc-share__channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M9.5 14.5 14.5 9"/><path d="M7.5 16.5 5 19a3.5 3.5 0 0 1-5-5l3-3a3.5 3.5 0 0 1 5 0" transform="translate(3)"/><path d="m13.5 7.5 2.5-2.5a3.5 3.5 0 0 1 5 5l-3 3a3.5 3.5 0 0 1-5 0" transform="translate(-3)"/></svg></span><span class="stc-share__channel-label" data-stc-share-copy-label>' . esc_html__( 'Copy link', 'solo-to-china' ) . '</span></button>';
	echo '<a class="stc-share__channel stc-share__channel--whatsapp" href="' . esc_url( $whatsapp_url ) . '" target="_blank" rel="noopener" data-stc-share-whatsapp><span class="stc-share__channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M12 2a9.5 9.5 0 0 0-8.2 14.3L2.5 21.5l5.3-1.3A9.5 9.5 0 1 0 12 2Zm5.4 13.4c-.2.7-1.2 1.3-2 1.5-.5.1-1.2.2-3.7-.8-3.1-1.3-5.1-4.5-5.3-4.7-.1-.2-1.2-1.6-1.2-3.1 0-1.5.8-2.2 1.1-2.5.3-.3.7-.4 1-.4h.7c.2 0 .5-.1.8.6l1 2.4c.1.2.1.5 0 .7l-.4.7-.6.6c-.2.2-.4.4-.2.8.2.4.8 1.3 1.8 2.1 1.2 1.1 2.2 1.4 2.6 1.6.3.2.6.1.8-.1l1.1-1.3c.2-.3.5-.3.8-.2l2.2 1c.4.2.6.3.7.5.1.2.1.8-.2 1.5Z"/></svg></span><span class="stc-share__channel-label">' . esc_html__( 'WhatsApp', 'solo-to-china' ) . '</span></a>';
	echo '<details class="stc-share__extras"><summary>More sharing options</summary><div>';
	echo '<a class="stc-share__channel stc-share__channel--facebook" href="' . esc_url( $facebook_url ) . '" target="_blank" rel="noopener" data-stc-share-facebook><span class="stc-share__channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M13.7 22v-8h2.8l.4-3h-3.2V9.1c0-.9.3-1.6 1.7-1.6H17V4.8c-.8-.1-1.7-.2-2.8-.2-2.9 0-4.9 1.8-4.9 5.1V11H6v3h3.3v8h4.4Z"/></svg></span><span class="stc-share__channel-label">' . esc_html__( 'Facebook', 'solo-to-china' ) . '</span></a>';
	echo '<a class="stc-share__channel stc-share__channel--reddit" href="' . esc_url( $reddit_url ) . '" target="_blank" rel="noopener" data-stc-share-reddit><span class="stc-share__channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="13" r="7"/><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><path d="M8.5 15c1.8 1.3 5.2 1.3 7 0M16.8 7.7l1-3.7 3 1M5.7 10a2 2 0 1 0-2.4 3.1M18.3 10a2 2 0 1 1 2.4 3.1"/></svg></span><span class="stc-share__channel-label">' . esc_html__( 'Reddit', 'solo-to-china' ) . '</span></a>';
	echo '<a class="stc-share__channel stc-share__channel--x" href="' . esc_url( $x_url ) . '" target="_blank" rel="noopener" data-stc-share-x><span class="stc-share__channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M4 3h5.2l3.9 5.3L17.7 3H20l-5.8 6.8L20.8 21h-5.2l-4.4-6-5.1 6H3.8l6.3-7.5L4 3Zm3.9 2 8.7 14h1.5L9.4 5H7.9Z"/></svg></span><span class="stc-share__channel-label">' . esc_html__( 'X', 'solo-to-china' ) . '</span></a>';
	echo '<button class="stc-share__channel stc-share__channel--more" type="button" data-stc-share-more hidden><span class="stc-share__channel-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg></span><span class="stc-share__channel-label">' . esc_html__( 'More apps', 'solo-to-china' ) . '</span></button>';
	echo '</div></details>';
	echo '</div>';
	echo '<input class="stc-share__url-field" type="text" value="' . esc_attr( $canonical ) . '" readonly aria-label="' . esc_attr__( 'Canonical page link', 'solo-to-china' ) . '" data-stc-share-url>';
	echo '<p class="stc-share__status" role="status" aria-live="polite" data-stc-share-status></p>';
	echo '</div><noscript><p class="stc-share__nojs">Share this page: <a href="' . esc_url( $canonical ) . '">' . esc_html( $canonical ) . '</a></p></noscript></div>';
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

/** Small pre-paint progressive-enhancement flag; content remains visible without JS. */
add_action( 'wp_head', function () { echo '<script>document.documentElement.classList.add("stc-js")</script>'; }, 0 );
