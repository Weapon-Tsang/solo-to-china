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
			$is_attraction_guide = stc_is_attraction_guide_post();
			$article_classes     = $is_attraction_guide ? 'stc-single stc-single--attraction-guide' : 'stc-single';
			?>
			<article <?php post_class( $article_classes ); ?>>
				<?php if ( $is_attraction_guide ) : ?>
					<header class="stc-attraction-guide__hero">
						<p class="stc-attraction-guide__eyebrow">Attraction Guide</p>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="stc-attraction-guide__deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<p class="stc-attraction-guide__meta"><?php echo esc_html( get_the_date() ); ?></p>
					</header>

					<div class="stc-attraction-guide__layout">
						<div class="stc-entry-content stc-entry-content--guide">
							<?php the_content(); ?>
						</div>
						<aside class="stc-attraction-guide__sidebar" aria-label="Attraction guide planning topics">
							<section class="stc-attraction-guide__checklist">
								<h2>Planning checklist</h2>
								<ul>
									<li><span>Best time</span><small>Season, weather, crowds</small></li>
									<li><span>Transport</span><small>Metro, taxi, rail, last mile</small></li>
									<li><span>Ticket price</span><small>Price ranges and entry notes</small></li>
									<li><span>Booking window</span><small>When to check or reserve</small></li>
									<li><span>Where to stay</span><small>Best base areas nearby</small></li>
									<li><span>Common mistakes</span><small>Timing, entrances, holidays</small></li>
								</ul>
								<a class="stc-button stc-button--secondary" href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Use ticket reminder</a>
							</section>
						</aside>
					</div>

					<footer class="stc-attraction-guide__footer">
						<a href="<?php echo esc_url( home_url( '/attraction-guides/' ) ); ?>">Back to Attraction Guides</a>
					</footer>
				<?php else : ?>
					<header class="stc-content__header">
						<p><?php echo esc_html( get_the_date() ); ?></p>
						<h1><?php the_title(); ?></h1>
					</header>

					<div class="stc-entry-content">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
	</section>
</main>

<?php
get_footer();
