<?php
/**
 * Search form template.
 *
 * @package SoloToChina
 */
?>
<form role="search" method="get" class="search-form <?php echo esc_attr( isset( $args['class'] ) ? $args['class'] : '' ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-stc-search-form>
	<label class="search-form__label">
		<span class="screen-reader-text"><?php esc_html_e( 'Search SoloToChina guides', 'solo-to-china' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr__( 'Search guides', 'solo-to-china' ); ?>" value="<?php echo esc_attr( get_search_query( false ) ); ?>" name="s" maxlength="120" autocomplete="off">
	</label>
	<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'solo-to-china' ); ?>"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="10.75" cy="10.75" r="6.75"/><path d="m16 16 5 5"/></svg></button>
</form>
