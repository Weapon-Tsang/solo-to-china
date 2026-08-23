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
			$is_city_guide       = stc_is_city_guide_post();
			$is_survival_kit     = stc_is_survival_kit_post();
			$article_classes     = 'stc-single';

			if ( $is_attraction_guide ) {
				$article_classes .= ' stc-single--attraction-guide';
			} elseif ( $is_city_guide ) {
				$article_classes .= ' stc-single--city-guide';
			} elseif ( $is_survival_kit ) {
				$article_classes .= ' stc-single--survival-kit';
			}
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
						<?php stc_render_guide_toc( 'stc-guide-toc--mobile' ); ?>
						<div class="stc-entry-content stc-entry-content--guide">
							<?php the_content(); ?>
						</div>
						<aside class="stc-attraction-guide__sidebar" aria-label="Attraction guide planning topics">
							<?php stc_render_guide_toc( 'stc-guide-toc--desktop' ); ?>
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
				<?php elseif ( $is_city_guide ) : ?>
					<header class="stc-city-guide__hero">
						<p class="stc-city-guide__eyebrow">City Guide</p>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="stc-city-guide__deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<p class="stc-city-guide__meta"><?php echo esc_html( get_the_date() ); ?></p>
					</header>

					<div class="stc-city-guide__layout">
						<?php stc_render_guide_toc( 'stc-guide-toc--mobile' ); ?>
						<div class="stc-entry-content stc-entry-content--guide">
							<?php the_content(); ?>
						</div>
						<aside class="stc-city-guide__sidebar" aria-label="City guide planning topics">
							<?php stc_render_guide_toc( 'stc-guide-toc--desktop' ); ?>
							<section class="stc-city-guide__checklist">
								<h2>City planning checklist</h2>
								<ul>
									<li><span>Where to stay</span><small>Best base areas and tradeoffs</small></li>
									<li><span>Getting around</span><small>Metro, taxi, airport, rail</small></li>
									<li><span>Itinerary</span><small>Realistic first-time route</small></li>
									<li><span>Food</span><small>Local dishes and easy areas</small></li>
									<li><span>Neighborhoods</span><small>What each area is best for</small></li>
									<li><span>Common mistakes</span><small>Station, timing, crowd risks</small></li>
								</ul>
								<a class="stc-button stc-button--secondary" href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">Browse city guides</a>
							</section>
						</aside>
					</div>

					<footer class="stc-city-guide__footer">
						<a href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">Back to City Guides</a>
					</footer>
				<?php elseif ( $is_survival_kit ) : ?>
					<header class="stc-survival-kit__hero">
						<p class="stc-survival-kit__eyebrow">Survival Kit</p>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="stc-survival-kit__deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<p class="stc-survival-kit__meta"><?php echo esc_html( get_the_date() ); ?></p>
					</header>

					<div class="stc-survival-kit__layout">
						<?php stc_render_guide_toc( 'stc-guide-toc--mobile' ); ?>
						<div class="stc-entry-content stc-entry-content--guide">
							<?php the_content(); ?>
						</div>
						<aside class="stc-survival-kit__sidebar" aria-label="Survival kit setup topics">
							<?php stc_render_guide_toc( 'stc-guide-toc--desktop' ); ?>
							<section class="stc-survival-kit__checklist">
								<h2>Setup checklist</h2>
								<ul>
									<li><span>Before arrival</span><small>Prepare before the flight</small></li>
									<li><span>Setup steps</span><small>Simple order of actions</small></li>
									<li><span>Required apps</span><small>Install and verify access</small></li>
									<li><span>Documents</span><small>Passport, visa, screenshots</small></li>
									<li><span>Connectivity</span><small>eSIM, VPN, airport fallback</small></li>
									<li><span>Backup plan</span><small>What to do if setup fails</small></li>
								</ul>
								<a class="stc-button stc-button--secondary" href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">Browse Survival Kit</a>
							</section>
						</aside>
					</div>

					<footer class="stc-survival-kit__footer">
						<a href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">Back to Survival Kit</a>
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
