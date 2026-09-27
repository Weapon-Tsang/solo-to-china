<?php
/** Public guide search results. */
get_header();
$state       = $GLOBALS['stc_search_input_state'] ?? array();
$term        = (string) get_search_query( false );
$input_error = isset( $state['error'] ) ? $state['error'] : '';
$query_error = ! empty( $GLOBALS['stc_search_query_failed'] );
$has_term    = '' !== trim( $term );
?>
<main id="main" class="stc-main">
	<section class="stc-content stc-content--search">
		<header class="stc-content__header">
			<h1><?php echo esc_html( $has_term ? __( 'Search results', 'solo-to-china' ) : __( 'Search guides', 'solo-to-china' ) ); ?></h1>
			<?php if ( $has_term && ! $query_error ) : ?>
				<p class="stc-search-summary"><?php echo esc_html( sprintf( _n( '%1$d guide for “%2$s”', '%1$d guides for “%2$s”', (int) $wp_query->found_posts, 'solo-to-china' ), (int) $wp_query->found_posts, $term ) ); ?></p>
			<?php endif; ?>
			<?php stc_render_search_form( 'stc-search-form--results' ); ?>
		</header>
		<?php if ( $query_error ) : ?>
			<p class="stc-search-message" role="alert"><?php esc_html_e( 'Search is temporarily unavailable. Please try again.', 'solo-to-china' ); ?></p>
		<?php elseif ( $input_error ) : ?>
			<p class="stc-search-message" role="alert"><?php echo esc_html( 'long' === $input_error ? __( 'Use 120 characters or fewer and try again.', 'solo-to-china' ) : __( 'Enter a valid search term and try again.', 'solo-to-china' ) ); ?></p>
		<?php elseif ( ! $has_term ) : ?>
			<p class="stc-search-message"><?php esc_html_e( 'Enter a city, attraction, or travel topic to search published guides.', 'solo-to-china' ); ?></p>
		<?php elseif ( have_posts() ) : ?>
			<div class="stc-post-list">
				<?php
				$image_prioritized = false;
				while ( have_posts() ) :
					the_post();
					$priority_image = ! $image_prioritized && has_post_thumbnail( get_the_ID() );
					stc_render_guide_card( null, $priority_image );
					$image_prioritized = $image_prioritized || $priority_image;
				endwhile;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="stc-search-message">
				<p><?php echo esc_html( sprintf( __( 'No guides found for “%s”. Try another search.', 'solo-to-china' ), $term ) ); ?></p>
				<p><a href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>"><?php esc_html_e( 'City Guides', 'solo-to-china' ); ?></a> <span aria-hidden="true">·</span> <a href="<?php echo esc_url( home_url( '/attraction-guides/' ) ); ?>"><?php esc_html_e( 'Attraction Guides', 'solo-to-china' ); ?></a></p>
			</div>
		<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
