<?php
/**
 * Search results template.
 *
 * @package SoloToChina
 */

get_header();
?>

<main id="main" class="stc-main">
	<section class="stc-content">
		<header class="stc-content__header">
			<p><?php esc_html_e( 'Search', 'solo-to-china' ); ?></p>
			<h1>
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Results for "%s"', 'solo-to-china' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>
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
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</a>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="stc-entry-content">
				<p><?php esc_html_e( 'No matching guides found yet. Try another city, attraction, or travel topic.', 'solo-to-china' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
