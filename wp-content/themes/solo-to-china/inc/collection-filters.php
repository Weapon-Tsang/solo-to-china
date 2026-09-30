<?php
/** Public collection filters, backed by the WordPress post_tag taxonomy. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Only offer tags used by public articles in this category (including children). */
function stc_collection_tag_clauses( $clauses, $taxonomies, $args ) {
	if ( empty( $args['stc_collection_categories'] ) || ! in_array( 'post_tag', $taxonomies, true ) ) { return $clauses; }
	global $wpdb;
	$ids = array_map( 'absint', $args['stc_collection_categories'] );
	$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
	$clauses['where'] .= $wpdb->prepare(
		" AND EXISTS (SELECT 1 FROM {$wpdb->term_relationships} tag_rel
		 INNER JOIN {$wpdb->posts} public_post ON public_post.ID = tag_rel.object_id
		 INNER JOIN {$wpdb->term_relationships} category_rel ON category_rel.object_id = public_post.ID
		 INNER JOIN {$wpdb->term_taxonomy} category_tax ON category_tax.term_taxonomy_id = category_rel.term_taxonomy_id
		 WHERE tag_rel.term_taxonomy_id = tt.term_taxonomy_id
		 AND public_post.post_type = 'post' AND public_post.post_status = 'publish' AND public_post.post_password = ''
		 AND category_tax.taxonomy = 'category' AND category_tax.term_id IN ($placeholders))",
		$ids
	);
	return $clauses;
}
add_filter( 'terms_clauses', 'stc_collection_tag_clauses', 10, 3 );

/** Validate the complete selection; invalid filters must never broaden results. */
function stc_collection_filter_state( $slug ) {
	static $states = array();
	if ( isset( $states[ $slug ] ) ) { return $states[ $slug ]; }
	$category = get_category_by_slug( $slug );
	$tags = array();
	if ( $category ) {
		$children = get_term_children( $category->term_id, 'category' );
		$tags = get_terms( array(
			'taxonomy' => 'post_tag', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC',
			// Password changes must also be reflected immediately in the public options.
			'cache_results' => false,
			'stc_collection_categories' => array_merge( array( $category->term_id ), is_wp_error( $children ) ? array() : $children ),
		) );
	}
	$unavailable = is_wp_error( $tags );
	$tags = $unavailable ? array() : $tags;
	$available = array();
	foreach ( $tags as $tag ) { $available[ $tag->slug ] = $tag; }
	$raw = isset( $_GET['stc_tags'] ) ? wp_unslash( $_GET['stc_tags'] ) : array();
	$invalid = ! is_array( $raw ) || count( $raw ) > 20;
	$selected = array();
	if ( ! $invalid ) {
		foreach ( $raw as $value ) {
			if ( ! is_string( $value ) || ! isset( $available[ $value ] ) ) { $invalid = true; continue; }
			$selected[ $value ] = $available[ $value ];
		}
	}
	$page = isset( $_GET['stc_page'] ) && is_string( $_GET['stc_page'] ) && ctype_digit( $_GET['stc_page'] ) ? max( 1, min( 100000, (int) $_GET['stc_page'] ) ) : 1;
	$states[ $slug ] = array( 'tags' => $tags, 'selected' => $selected, 'invalid' => $invalid, 'unavailable' => $unavailable, 'active' => ! empty( $raw ) || $invalid, 'page' => $page );
	return $states[ $slug ];
}

function stc_collection_filter_url( $base, $slugs, $page = 1 ) {
	$args = array();
	if ( $slugs ) { $args['stc_tags'] = array_values( $slugs ); }
	if ( $page > 1 ) { $args['stc_page'] = $page; }
	return $args ? add_query_arg( $args, $base ) : $base;
}

/** Group existing WP tags using editorial and public city identities; retain WP's name order. */
function stc_collection_tag_groups( $tags ) {
	static $city_keys = null;
	$normalize = function ( $value ) { return str_replace( '-', '', sanitize_title( $value ) ); };
	if ( null === $city_keys ) {
		$city_keys = array();
		foreach ( stc_get_site_collection_items( 'city-guides' ) as $city ) {
			$city_keys[ $normalize( $city['name'] ) ] = true;
			$city_keys[ $normalize( $city['entity_key'] ?? $city['class'] ) ] = true;
		}
		// CMS city identities also cover cities outside the curated collection.
		foreach ( stc_entity_index() as $slot => $guide ) {
			if ( 0 === strpos( $slot, 'city-guide:' ) ) { $city_keys[ $normalize( substr( $slot, strlen( 'city-guide:' ) ) ) ] = true; }
		}
	}
	$groups = array(
		'cities' => array( 'label' => __( 'Cities', 'solo-to-china' ), 'tags' => array() ),
		'topics' => array( 'label' => __( 'Topics', 'solo-to-china' ), 'tags' => array() ),
	);
	foreach ( $tags as $tag ) {
		$group = isset( $city_keys[ $normalize( $tag->slug ) ] ) || isset( $city_keys[ $normalize( $tag->name ) ] ) ? 'cities' : 'topics';
		$groups[ $group ]['tags'][] = $tag;
	}
	return array_filter( $groups, function ( $group ) { return ! empty( $group['tags'] ); } );
}

function stc_render_collection_filter( $state, $base, $count ) {
	$selected = $state['selected'];
	?>
	<div class="stc-tag-filter" data-stc-tag-filter>
		<div class="stc-tag-filter__bar">
			<details class="stc-tag-filter__picker" data-stc-tag-picker>
				<summary>
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16M4 17h16"/><circle cx="9" cy="7" r="2"/><circle cx="15" cy="17" r="2"/></svg>
					<?php esc_html_e( 'Filter by tag', 'solo-to-china' ); ?>
					<?php if ( $selected ) : ?><span class="stc-tag-filter__badge"><?php echo count( $selected ); ?></span><?php endif; ?>
					<svg class="stc-tag-filter__chevron" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="m6 8 4 4 4-4"/></svg>
				</summary>
				<form class="stc-tag-filter__panel" method="get" action="<?php echo esc_url( $base ); ?>" data-stc-tag-form>
					<?php if ( ! get_option( 'permalink_structure' ) ) : ?><input type="hidden" name="page_id" value="<?php echo absint( get_queried_object_id() ); ?>"><?php endif; ?>
					<div class="stc-tag-filter__panel-heading"><h2><?php esc_html_e( 'Filter guides', 'solo-to-china' ); ?></h2><button class="stc-tag-filter__close" type="button" aria-label="<?php esc_attr_e( 'Close tag filters', 'solo-to-china' ); ?>" hidden data-stc-tag-close><svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="m6 6 8 8M14 6l-8 8"/></svg></button></div>
					<p id="stc-tag-filter-help"><?php esc_html_e( 'Match all selected tags.', 'solo-to-china' ); ?></p>
					<?php if ( $state['tags'] ) : ?>
						<label class="stc-tag-filter__search" hidden data-stc-tag-search-wrap><span class="screen-reader-text"><?php esc_html_e( 'Find a tag', 'solo-to-china' ); ?></span><input type="search" placeholder="<?php esc_attr_e( 'Find a tag…', 'solo-to-china' ); ?>" autocomplete="off" data-stc-tag-search></label>
						<fieldset aria-describedby="stc-tag-filter-help"><legend class="screen-reader-text"><?php esc_html_e( 'Article tags', 'solo-to-china' ); ?></legend>
							<div class="stc-tag-filter__groups">
								<?php foreach ( stc_collection_tag_groups( $state['tags'] ) as $group ) : ?>
									<div class="stc-tag-filter__group" role="group" aria-label="<?php echo esc_attr( $group['label'] ); ?>" data-stc-tag-group>
										<p class="stc-tag-filter__group-title" aria-hidden="true"><?php echo esc_html( $group['label'] ); ?></p>
										<div class="stc-tag-filter__options">
											<?php foreach ( $group['tags'] as $tag ) : ?>
												<label class="stc-tag-filter__option" data-stc-tag-option><input type="checkbox" name="stc_tags[]" value="<?php echo esc_attr( $tag->slug ); ?>" <?php checked( isset( $selected[ $tag->slug ] ) ); ?>><span><?php echo esc_html( $tag->name ); ?></span></label>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</fieldset>
						<p class="stc-tag-filter__no-tags" role="status" hidden data-stc-tag-no-match><?php esc_html_e( 'No tags match. Try another name.', 'solo-to-china' ); ?></p>
						<div class="stc-tag-filter__actions"><span data-stc-tag-selection role="status"><?php echo esc_html( $selected ? sprintf( __( '%d selected', 'solo-to-china' ), count( $selected ) ) : __( 'All guides', 'solo-to-china' ) ); ?></span><button type="submit" data-stc-tag-apply><span data-stc-tag-apply-label><?php esc_html_e( 'Apply filters', 'solo-to-china' ); ?></span><span aria-hidden="true">&rarr;</span></button></div>
					<?php else : ?>
						<p class="stc-tag-filter__no-tags"><?php echo esc_html( $state['unavailable'] ? __( 'Tags are temporarily unavailable. Please reload to try again.', 'solo-to-china' ) : __( 'Tags will appear when they are added to published guides in WordPress.', 'solo-to-china' ) ); ?></p>
					<?php endif; ?>
				</form>
			</details>
			<?php if ( $state['active'] ) : ?><div class="stc-tag-filter__selection"><?php endif; ?>
			<?php foreach ( $selected as $slug => $tag ) : ?>
				<a class="stc-tag-filter__chip" href="<?php echo esc_url( stc_collection_filter_url( $base, array_diff( array_keys( $selected ), array( $slug ) ) ) ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove tag: %s', 'solo-to-china' ), $tag->name ) ); ?>"><?php echo esc_html( $tag->name ); ?><svg viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="m6 6 8 8M14 6l-8 8"/></svg></a>
			<?php endforeach; ?>
			<?php if ( $state['active'] ) : ?><a class="stc-tag-filter__clear" href="<?php echo esc_url( $base ); ?>"><?php esc_html_e( 'Clear all', 'solo-to-china' ); ?></a><?php endif; ?>
			<?php if ( $state['active'] ) : ?></div><?php endif; ?>
			<p class="stc-tag-filter__count"><?php echo esc_html( sprintf( _n( '%s guide', '%s guides', $count, 'solo-to-china' ), number_format_i18n( $count ) ) ); ?></p>
		</div>
	</div>
	<?php
}

/** Server-side AND filtering applies to the complete collection, before pagination. */
function stc_render_filtered_collection( $slug ) {
	$state = stc_collection_filter_state( $slug );
	$base = get_permalink( get_queried_object_id() );
	$query_args = array(
		'category_name' => $slug, 'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false,
		'ignore_sticky_posts' => true, 'posts_per_page' => 6, 'paged' => $state['page'],
		'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ), 'no_found_rows' => false,
	);
	if ( $state['selected'] ) { $query_args['tag__and'] = array_map( function ( $tag ) { return $tag->term_id; }, array_values( $state['selected'] ) ); }
	if ( $state['invalid'] ) { $query_args['post__in'] = array( 0 ); }
	$query = new WP_Query( $query_args );
	$count = (int) $query->found_posts;
	// WP skips found_rows on an empty out-of-range page; recover the total and first page.
	if ( ! $query->post_count && $state['page'] > 1 ) {
		$query_args['paged'] = 1;
		$query = new WP_Query( $query_args );
		$state['page'] = 1;
		$count = (int) $query->found_posts;
	}
	echo '<section class="stc-collection-articles stc-collection-articles--filterable" aria-labelledby="stc-collection-title">';
	stc_render_collection_filter( $state, $base, $count );
	if ( $query->have_posts() ) {
		echo '<div class="stc-post-list">';
		$image_prioritized = false;
		while ( $query->have_posts() ) {
			$query->the_post();
			$priority_image = ! $image_prioritized && has_post_thumbnail( get_the_ID() );
			stc_render_guide_card( get_the_ID(), $priority_image );
			$image_prioritized = $image_prioritized || $priority_image;
		}
		echo '</div>';
	} else {
		echo '<div class="stc-tag-filter__empty"><h2>' . esc_html__( 'No guides found', 'solo-to-china' ) . '</h2><p>';
		echo esc_html( $state['invalid'] ? __( 'One or more tags are no longer available. Clear the filters and choose again.', 'solo-to-china' ) : ( $state['active'] ? __( 'No guides have all of these tags. Remove a tag or clear the filters to explore more.', 'solo-to-china' ) : __( 'Published guides will appear here.', 'solo-to-china' ) ) );
		echo '</p></div>';
	}
	if ( $query->max_num_pages > 1 ) {
		// Include the selection in the pagination base so WP preserves every tag.
		$placeholder = 999999;
		$links = paginate_links( array(
			'base' => str_replace( (string) $placeholder, '%#%', esc_url( stc_collection_filter_url( $base, array_keys( $state['selected'] ), $placeholder ) ) ),
			'format' => '', 'add_args' => false, 'current' => $state['page'], 'total' => $query->max_num_pages,
			'type' => 'list', 'prev_text' => __( 'Previous', 'solo-to-china' ), 'next_text' => __( 'Next', 'solo-to-china' ),
		) );
		echo '<nav class="stc-tag-filter__pagination" aria-label="' . esc_attr__( 'Guide pages', 'solo-to-china' ) . '">' . wp_kses_post( $links ) . '</nav>';
	}
	echo '</section>';
	wp_reset_postdata();
}

function stc_enqueue_collection_filters() {
	if ( ! is_page( array( 'city-guides', 'attraction-guides' ) ) ) { return; }
	foreach ( array( 'css' => 'style', 'js' => 'script' ) as $extension => $type ) {
		$path = '/assets/' . $extension . '/collection-filters.' . $extension;
		$version = STC_THEME_VERSION . '.' . filemtime( get_template_directory() . $path );
		if ( 'style' === $type ) { wp_enqueue_style( 'stc-collection-filters', get_template_directory_uri() . $path, array( 'stc-main' ), $version ); }
		else { wp_enqueue_script( 'stc-collection-filters', get_template_directory_uri() . $path, array(), $version, true ); }
	}
}
add_action( 'wp_enqueue_scripts', 'stc_enqueue_collection_filters', 40 );
