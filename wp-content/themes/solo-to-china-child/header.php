<?php
/**
 * Child Theme header with current-page navigation state.
 *
 * @package SoloToChinaChild
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="stc-skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'solo-to-china-child' ); ?></a>
<header class="stc-header">
	<a class="stc-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'SoloToChina Home', 'solo-to-china-child' ); ?>">
		<span class="stc-brand__mark" aria-hidden="true">STC</span>
		<span class="stc-brand__name">SoloToChina</span>
	</a>
	<button class="stc-menu-toggle" type="button" aria-expanded="false" aria-controls="stc-primary-nav" data-open-label="<?php esc_attr_e( 'Open menu', 'solo-to-china-child' ); ?>" data-close-label="<?php esc_attr_e( 'Close menu', 'solo-to-china-child' ); ?>">
		<span class="stc-menu-toggle__line"></span>
		<span class="stc-menu-toggle__line"></span>
		<span class="stc-menu-toggle__line"></span>
		<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'solo-to-china-child' ); ?></span>
	</button>
	<?php stc_child_render_primary_navigation(); ?>
</header>
