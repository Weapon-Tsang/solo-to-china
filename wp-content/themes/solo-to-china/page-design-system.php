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
		</dl>
	</section>

	<nav class="stc-component-gallery__jump" aria-label="<?php esc_attr_e( 'Component categories', 'solo-to-china' ); ?>">
		<?php foreach ( array_keys( $categories ) as $category ) : ?>
			<a href="#gallery-<?php echo esc_attr( sanitize_title( $category ) ); ?>"><?php echo esc_html( ucwords( $category ) ); ?></a>
		<?php endforeach; ?>
		<a href="#gallery-live-examples"><?php esc_html_e( 'Live examples', 'solo-to-china' ); ?></a>
	</nav>

	<section class="stc-component-gallery__registry" aria-labelledby="stc-component-registry-title">
		<div class="stc-component-gallery__section-heading">
			<p><?php esc_html_e( 'Single source of truth', 'solo-to-china' ); ?></p>
			<h2 id="stc-component-registry-title"><?php esc_html_e( 'Published capabilities', 'solo-to-china' ); ?></h2>
		</div>

		<?php foreach ( $categories as $category => $category_components ) : ?>
			<section id="gallery-<?php echo esc_attr( sanitize_title( $category ) ); ?>" class="stc-component-gallery__category">
				<h3><?php echo esc_html( ucwords( $category ) ); ?></h3>
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
				<p><?php esc_html_e( 'Native share first; canonical channel and copy fallback second.', 'solo-to-china' ); ?></p>
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
