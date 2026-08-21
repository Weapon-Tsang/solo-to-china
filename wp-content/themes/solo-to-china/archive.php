<?php
/**
 * Archive template.
 *
 * @package SoloToChina
 */

get_header();
?>

<main id="main" class="stc-main">
	<section class="stc-content">
		<header class="stc-content__header">
			<p><?php esc_html_e( 'SoloToChina guides', 'solo-to-china' ); ?></p>
			<h1><?php the_archive_title(); ?></h1>
			<?php if ( get_the_archive_description() ) : ?>
				<div class="stc-entry-content"><?php the_archive_description(); ?></div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="stc-post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'stc-post-card' ); ?>>
						<a href="<?php echo esc_url( get_permalink() ); ?>">
							<h2><?php the_title(); ?></h2>
							<?php if ( has_excerpt() ) : ?>
								<p><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>
						</a>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="stc-entry-content">
				<p><?php esc_html_e( 'No guides are published in this section yet.', 'solo-to-china' ); ?></p>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
