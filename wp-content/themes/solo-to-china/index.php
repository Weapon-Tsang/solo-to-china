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
			<header class="stc-content__header">
				<h1><?php esc_html_e( 'Nothing found', 'solo-to-china' ); ?></h1>
				<p><?php esc_html_e( 'New SoloToChina guides are being prepared.', 'solo-to-china' ); ?></p>
			</header>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
