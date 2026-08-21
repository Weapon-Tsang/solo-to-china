<?php
/**
 * Single guide/article template.
 *
 * @package SoloToChina
 */

get_header();
?>

<main id="main" class="stc-main">
	<section class="stc-content">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'stc-single' ); ?>>
				<header class="stc-content__header">
					<p><?php echo esc_html( get_the_date() ); ?></p>
					<h1><?php the_title(); ?></h1>
				</header>

				<div class="stc-entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</section>
</main>

<?php
get_footer();
