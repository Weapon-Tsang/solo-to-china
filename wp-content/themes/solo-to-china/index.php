<?php
/**
 * Default template for SoloToChina.
 *
 * @package SoloToChina
 */

get_header();
?>

<main id="main" class="stc-main">
	<section class="stc-content">
		<?php if ( have_posts() ) : ?>
			<header class="stc-content__header">
				<h1><?php echo esc_html( get_the_archive_title() ?: get_bloginfo( 'name' ) ); ?></h1>
			</header>

			<div class="stc-post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php stc_render_guide_card(); ?>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<header class="stc-content__header">
				<h1><?php esc_html_e( 'Nothing found', 'solo-to-china' ); ?></h1>
				<p><?php esc_html_e( 'New SoloToChina guides are being prepared.', 'solo-to-china' ); ?></p>
			</header>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
