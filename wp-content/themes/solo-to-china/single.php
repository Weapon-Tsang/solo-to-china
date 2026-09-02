<?php
/**
 * Generic single article shell.
 *
 * Content type supplies taxonomy and presentation context only. The CMS controls
 * content blocks, their order, Share, and TOC visibility through post data.
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

			$guide_type  = stc_get_guide_type_slug();
			$guide_label = stc_get_guide_type_label();
			$hero_variant = stc_get_hero_variant();
			$show_share  = stc_page_presentation_enabled( 'share' );
			$show_toc    = stc_page_presentation_enabled( 'toc' );
			?>
			<article <?php post_class( array( 'stc-single', 'stc-single--' . $guide_type ) ); ?>>
				<header class="stc-article-hero stc-article-hero--<?php echo esc_attr( $hero_variant ); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="stc-article-hero__media">
							<?php
							echo wp_get_attachment_image(
								get_post_thumbnail_id(),
								'full',
								false,
								array(
									'class'    => 'stc-article-hero__image',
									'decoding' => 'async',
									'fetchpriority' => 'high',
									'sizes'    => '(max-width: 840px) 100vw, 1180px',
								)
							);
							?>
						</div>
					<?php endif; ?>

					<div class="stc-article-hero__content">
						<p class="stc-article-hero__eyebrow"><?php echo esc_html( $guide_label ); ?></p>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="stc-article-hero__deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<p class="stc-article-hero__meta">
							<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</p>
						<?php if ( $show_share ) : ?>
							<div class="stc-article-hero__utilities">
								<?php stc_render_share_this_page(); ?>
							</div>
						<?php endif; ?>
					</div>
				</header>

				<div class="stc-article-layout<?php echo $show_toc ? ' stc-article-layout--with-toc' : ''; ?>">
					<?php if ( $show_toc ) : ?>
						<?php stc_render_guide_toc( 'stc-guide-toc--mobile' ); ?>
					<?php endif; ?>

					<div class="stc-entry-content stc-entry-content--guide">
						<?php the_content(); ?>
					</div>

					<?php if ( $show_toc ) : ?>
						<aside class="stc-article-sidebar" aria-label="<?php esc_attr_e( 'Article navigation', 'solo-to-china' ); ?>">
							<?php stc_render_guide_toc( 'stc-guide-toc--desktop' ); ?>
						</aside>
					<?php endif; ?>
				</div>
			</article>
		<?php endwhile; ?>
	</section>
</main>

<?php
get_footer();
