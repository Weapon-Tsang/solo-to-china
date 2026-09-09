<?php
/**
 * Development-only Component Gallery template.
 *
 * The Theme does not create this page in production. Playground fixtures create
 * it explicitly so frontend, CMS, design, and QA teams can inspect real output.
 *
 * Template Name: Component Gallery
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$components = stc_get_cms_component_definitions();
$categories = array();
foreach ( $components as $component ) {
	$categories[ $component['category'] ][] = $component;
}
?>

<main id="main" class="stc-component-gallery">
	<section class="stc-component-gallery__intro" aria-labelledby="stc-component-gallery-title">
		<div>
			<p class="stc-component-gallery__eyebrow"><?php esc_html_e( 'Internal design system', 'solo-to-china' ); ?></p>
			<h1 id="stc-component-gallery-title"><?php esc_html_e( 'Frontend Component Gallery', 'solo-to-china' ); ?></h1>
			<p><?php esc_html_e( 'Live examples of every capability currently published to the CMS. Component type and variant names are stable API values; visual tokens remain frontend-owned.', 'solo-to-china' ); ?></p>
		</div>
		<dl class="stc-component-gallery__stats">
			<div><dt><?php esc_html_e( 'Registry', 'solo-to-china' ); ?></dt><dd><?php echo esc_html( STC_COMPONENT_REGISTRY_VERSION ); ?></dd></div>
			<div><dt><?php esc_html_e( 'CMS capabilities', 'solo-to-china' ); ?></dt><dd><?php echo esc_html( count( $components ) ); ?></dd></div>
			<div><dt><?php esc_html_e( 'Contract', 'solo-to-china' ); ?></dt><dd><?php echo esc_html( STC_CONTENT_CONTRACT_VERSION ); ?></dd></div>
			<div><dt><?php esc_html_e( 'Foundation sets', 'solo-to-china' ); ?></dt><dd>5</dd></div>
		</dl>
	</section>

	<nav class="stc-component-gallery__jump" aria-label="<?php esc_attr_e( 'Component categories', 'solo-to-china' ); ?>">
		<a href="#gallery-foundations"><?php esc_html_e( 'Foundations', 'solo-to-china' ); ?></a>
		<?php foreach ( array_keys( $categories ) as $category ) : ?>
			<a href="#gallery-<?php echo esc_attr( sanitize_title( $category ) ); ?>"><span><?php echo esc_html( ucwords( $category ) ); ?></span><small><?php echo esc_html( count( $categories[ $category ] ) ); ?></small></a>
		<?php endforeach; ?>
		<a href="#gallery-live-examples"><?php esc_html_e( 'Live examples', 'solo-to-china' ); ?></a>
	</nav>

	<section id="gallery-foundations" class="stc-component-gallery__foundations" aria-labelledby="stc-gallery-foundations-title">
		<div class="stc-component-gallery__section-heading">
			<p><?php esc_html_e( 'Reusable UI foundation', 'solo-to-china' ); ?></p>
			<h2 id="stc-gallery-foundations-title"><?php esc_html_e( 'Tokens and primitives', 'solo-to-china' ); ?></h2>
			<p><?php esc_html_e( 'Shared building blocks for consistent interfaces across the public site, tools, and editorial content.', 'solo-to-china' ); ?></p>
		</div>

		<div class="stc-component-gallery__foundation-grid">
			<article class="stc-component-gallery__foundation-card stc-component-gallery__foundation-card--wide">
				<div class="stc-component-gallery__sample-heading"><span>01</span><div><h3><?php esc_html_e( 'Color roles', 'solo-to-china' ); ?></h3><p><?php esc_html_e( 'Semantic tokens stay stable while exact values remain frontend-owned.', 'solo-to-china' ); ?></p></div></div>
				<div class="stc-component-gallery__swatches">
					<?php foreach ( array( 'ink' => 'Ink', 'jade' => 'Jade', 'brand' => 'Brand', 'gold' => 'Gold', 'canvas' => 'Canvas', 'surface' => 'Surface' ) as $token => $label ) : ?>
						<div><span class="stc-component-gallery__swatch stc-component-gallery__swatch--<?php echo esc_attr( $token ); ?>" aria-hidden="true"></span><strong><?php echo esc_html( $label ); ?></strong><code>--stc-color-<?php echo esc_html( $token ); ?></code></div>
					<?php endforeach; ?>
				</div>
			</article>

			<article class="stc-component-gallery__foundation-card">
				<div class="stc-component-gallery__sample-heading"><span>02</span><div><h3><?php esc_html_e( 'Type scale', 'solo-to-china' ); ?></h3><p><?php esc_html_e( 'Editorial character with a practical interface voice.', 'solo-to-china' ); ?></p></div></div>
				<div class="stc-component-gallery__type-samples"><strong><?php esc_html_e( 'Travel deeper', 'solo-to-china' ); ?></strong><h4><?php esc_html_e( 'Plan with confidence', 'solo-to-china' ); ?></h4><p><?php esc_html_e( 'Clear guidance stays readable at every screen size.', 'solo-to-china' ); ?></p><small><?php esc_html_e( 'LABEL / SUPPORTING META', 'solo-to-china' ); ?></small></div>
			</article>

			<article class="stc-component-gallery__foundation-card">
				<div class="stc-component-gallery__sample-heading"><span>03</span><div><h3><?php esc_html_e( 'Actions', 'solo-to-china' ); ?></h3><p><?php esc_html_e( 'Primary, secondary, outline, and quiet hierarchy.', 'solo-to-china' ); ?></p></div></div>
				<div class="stc-cluster"><a class="stc-button stc-button--primary" href="#gallery-foundations"><?php esc_html_e( 'Primary', 'solo-to-china' ); ?></a><a class="stc-button stc-button--secondary" href="#gallery-foundations"><?php esc_html_e( 'Secondary', 'solo-to-china' ); ?></a><a class="stc-button stc-button--outline" href="#gallery-foundations"><?php esc_html_e( 'Outline', 'solo-to-china' ); ?></a><a class="stc-button stc-button--quiet" href="#gallery-foundations"><?php esc_html_e( 'Quiet action', 'solo-to-china' ); ?></a></div>
			</article>

			<article class="stc-component-gallery__foundation-card">
				<div class="stc-component-gallery__sample-heading"><span>04</span><div><h3><?php esc_html_e( 'Status badges', 'solo-to-china' ); ?></h3><p><?php esc_html_e( 'Meaning is carried by both text and color.', 'solo-to-china' ); ?></p></div></div>
				<div class="stc-cluster"><span class="stc-badge">Default</span><span class="stc-badge stc-badge--success">Verified</span><span class="stc-badge stc-badge--attention">Check first</span><span class="stc-badge stc-badge--critical">Important</span></div>
			</article>

			<article class="stc-component-gallery__foundation-card">
				<div class="stc-component-gallery__sample-heading"><span>05</span><div><h3><?php esc_html_e( 'Fields and panels', 'solo-to-china' ); ?></h3><p><?php esc_html_e( 'Consistent labels, help text, focus, shape, and elevation.', 'solo-to-china' ); ?></p></div></div>
				<div class="stc-stack stc-stack--tight"><label class="stc-field"><span><?php esc_html_e( 'Destination', 'solo-to-china' ); ?></span><input type="text" value="Beijing" readonly><small><?php esc_html_e( 'City or landmark', 'solo-to-china' ); ?></small></label><div class="stc-panel stc-panel--accent"><strong><?php esc_html_e( 'Ready to use', 'solo-to-china' ); ?></strong><p><?php esc_html_e( 'The same primitives work in themes, tools, and editor previews.', 'solo-to-china' ); ?></p></div></div>
			</article>
		</div>
	</section>

	<section class="stc-component-gallery__registry" aria-labelledby="stc-component-registry-title">
		<div class="stc-component-gallery__section-heading">
			<p><?php esc_html_e( 'Single source of truth', 'solo-to-china' ); ?></p>
			<h2 id="stc-component-registry-title"><?php esc_html_e( 'Published capabilities', 'solo-to-china' ); ?></h2>
		</div>

		<?php foreach ( $categories as $category => $category_components ) : ?>
			<section id="gallery-<?php echo esc_attr( sanitize_title( $category ) ); ?>" class="stc-component-gallery__category stc-component-gallery__category--<?php echo esc_attr( sanitize_title( $category ) ); ?>">
				<h3><span><?php echo esc_html( ucwords( $category ) ); ?></span><small><?php echo esc_html( sprintf( _n( '%d component', '%d components', count( $category_components ), 'solo-to-china' ), count( $category_components ) ) ); ?></small></h3>
				<div class="stc-component-gallery__registry-grid">
					<?php foreach ( $category_components as $component ) : ?>
						<?php
						$required = isset( $component['schema']['required'] ) ? $component['schema']['required'] : array();
						$fields   = isset( $component['schema']['properties'] ) ? array_keys( $component['schema']['properties'] ) : array();
						$optional = array_values( array_diff( $fields, $required ) );
						?>
						<article class="stc-component-gallery__registry-card" data-component-id="<?php echo esc_attr( $component['id'] ); ?>">
							<div class="stc-component-gallery__registry-meta">
								<code><?php echo esc_html( $component['id'] ); ?></code>
								<span><?php echo esc_html( $component['status'] ); ?></span>
							</div>
							<h4><?php echo esc_html( $component['name'] ); ?></h4>
							<p><?php echo esc_html( $component['purpose'] ); ?></p>
							<dl>
								<div><dt><?php esc_html_e( 'Interface', 'solo-to-china' ); ?></dt><dd><?php echo esc_html( $component['cms_interface'] ); ?></dd></div>
								<div><dt><?php esc_html_e( 'Variants', 'solo-to-china' ); ?></dt><dd><?php echo esc_html( implode( ', ', $component['variants'] ) ); ?></dd></div>
								<div><dt><?php esc_html_e( 'Required', 'solo-to-china' ); ?></dt><dd><?php echo esc_html( $required ? implode( ', ', $required ) : 'none' ); ?></dd></div>
								<div><dt><?php esc_html_e( 'Optional', 'solo-to-china' ); ?></dt><dd><?php echo esc_html( $optional ? implode( ', ', $optional ) : 'none' ); ?></dd></div>
							</dl>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endforeach; ?>
	</section>

	<section class="stc-component-gallery__presentation" aria-labelledby="stc-gallery-presentation-title">
		<div class="stc-component-gallery__section-heading">
			<p><?php esc_html_e( 'Presentation metadata', 'solo-to-china' ); ?></p>
			<h2 id="stc-gallery-presentation-title"><?php esc_html_e( 'Article Hero variants', 'solo-to-china' ); ?></h2>
		</div>
		<div class="stc-component-gallery__hero-grid" data-stc-gallery-component="article_hero">
			<?php foreach ( array( 'default', 'attraction', 'city', 'survival' ) as $variant ) : ?>
				<article class="stc-component-gallery__hero-sample stc-component-gallery__hero-sample--<?php echo esc_attr( $variant ); ?>">
					<span><?php echo esc_html( $variant ); ?></span>
					<strong><?php esc_html_e( 'A calmer first visit', 'solo-to-china' ); ?></strong>
				</article>
			<?php endforeach; ?>
		</div>
		<div class="stc-component-gallery__utility-grid">
			<article data-stc-gallery-component="share_this_page">
				<h3><?php esc_html_e( 'Share This Page', 'solo-to-china' ); ?></h3>
				<p><?php esc_html_e( 'Desktop branded popover; mobile native share with a canonical-link fallback sheet.', 'solo-to-china' ); ?></p>
				<?php stc_render_share_this_page(); ?>
			</article>
			<article data-stc-gallery-component="table_of_contents">
				<h3><?php esc_html_e( 'Table of Contents', 'solo-to-china' ); ?></h3>
				<p><?php esc_html_e( 'Explicit utility rendered from stable H2 anchors.', 'solo-to-china' ); ?></p>
				<?php stc_render_guide_toc(); ?>
			</article>
		</div>
	</section>

	<section id="gallery-live-examples" class="stc-component-gallery__examples" aria-labelledby="stc-gallery-live-title">
		<div class="stc-component-gallery__section-heading">
			<p><?php esc_html_e( 'Real renderer output', 'solo-to-china' ); ?></p>
			<h2 id="stc-gallery-live-title"><?php esc_html_e( 'CMS-authored block examples', 'solo-to-china' ); ?></h2>
			<p><?php esc_html_e( 'The content below is stored as ordinary Gutenberg blocks and approved shortcodes in the disposable Playground page.', 'solo-to-china' ); ?></p>
		</div>
		<div class="stc-entry-content stc-entry-content--guide">
			<?php
			while ( have_posts() ) {
				the_post();
				the_content();
			}
			?>
		</div>
	</section>
</main>

<?php
get_footer();
