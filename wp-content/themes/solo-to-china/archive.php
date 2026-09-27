<?php
/**
 * Archive template.
 *
 * @package SoloToChina
 */

get_header();
?>

<main id="main" class="stc-main">
	<section class="stc-content stc-content--archive">
		<header class="stc-content__header">
			<h1><?php the_archive_title(); ?></h1>
			<div class="stc-archive-search"><?php stc_render_search_form( 'stc-search-form--inline' ); ?></div>
		</header>

		<?php if ( have_posts() ) : ?>
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
			<div class="stc-entry-content">
				<p><?php esc_html_e( 'No guides are published in this section yet.', 'solo-to-china' ); ?></p>
				<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'solo-to-china' ); ?></a></p>
			</div>
		<?php endif; ?>
		<?php if ( get_the_archive_description() ) : ?>
			<div class="stc-archive-description stc-entry-content"><?php the_archive_description(); ?></div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
