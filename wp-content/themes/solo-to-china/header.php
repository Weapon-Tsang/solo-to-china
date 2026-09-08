<?php
/**
 * Theme header.
 *
 * @package SoloToChina
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
<a class="stc-skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'solo-to-china' ); ?></a>
<header class="stc-header">
	<a class="stc-brand<?php echo is_front_page() ? ' stc-brand--image' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="SoloToChina Home">
		<?php if ( is_front_page() ) : ?>
			<img class="stc-brand__logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/solotochina-logo-white.png' ); ?>" width="533" height="515" alt="" decoding="async" fetchpriority="high">
		<?php else : ?>
			<span class="stc-brand__mark">STC</span>
			<span class="stc-brand__name">SoloToChina</span>
		<?php endif; ?>
	</a>
	<button class="stc-menu-toggle" type="button" aria-expanded="false" aria-controls="stc-primary-nav">
		<span class="stc-menu-toggle__line"></span>
		<span class="stc-menu-toggle__line"></span>
		<span class="stc-menu-toggle__line"></span>
		<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'solo-to-china' ); ?></span>
	</button>
	<?php stc_render_primary_navigation(); ?>
</header>
