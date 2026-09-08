<?php
/**
 * Non-destructive fallback for a reserved static-page slug owned by a draft.
 *
 * @package SoloToChina
 */

$slug     = sanitize_title( (string) get_query_var( 'stc_static_page_fallback' ) );
$pages    = stc_static_page_metadata();
$contents = stc_static_page_content();
$page     = isset( $pages[ $slug ] ) ? $pages[ $slug ] : null;
$content  = isset( $contents[ $slug ] ) ? $contents[ $slug ] : '';

get_header();
?>

<main id="main" class="stc-main">
	<?php if ( $page && $content ) : ?>
		<section class="stc-page-hero stc-page-hero--visual stc-page-hero--<?php echo esc_attr( $slug ); ?>">
			<nav class="stc-static-page__breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'solo-to-china' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'solo-to-china' ); ?></a><span aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html( $page['title'] ); ?></span></nav>
			<h1><?php echo esc_html( $page['title'] ); ?></h1>
			<span><?php echo esc_html( $page['copy'] ); ?></span>
		</section>

		<div class="stc-page-primary">
			<article class="stc-static-page stc-static-page--<?php echo esc_attr( $slug ); ?>">
				<div class="stc-static-page__content">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			</article>
		</div>
	<?php endif; ?>
</main>

<?php
get_footer();
