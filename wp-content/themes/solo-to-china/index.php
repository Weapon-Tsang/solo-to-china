<?php
/**
 * Default template for SoloToChina.
 *
 * @package SoloToChina
 */

get_header();
?>

<main id="main" class="stc-main">
	<section class="stc-content stc-content--archive">
		<?php if ( have_posts() ) : ?>
			<header class="stc-content__header">
				<h1><?php echo esc_html( get_the_archive_title() ?: get_bloginfo( 'name' ) ); ?></h1>
				<div class="stc-archive-search"><?php stc_render_search_form( 'stc-search-form--inline' ); ?></div>
			</header>

			<div class="stc-post-list">
				<?php
				$image_prioritized = false;
				while ( have_posts() ) :
					the_post();
					$priority_image = ! $image_prioritized && has_post_thumbnail( get_the_ID() );
					?>
					<?php
					stc_render_guide_card( null, $priority_image );
					$image_prioritized = $image_prioritized || $priority_image;
					?>
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
