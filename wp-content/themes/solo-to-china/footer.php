<?php
/**
 * Theme footer.
 *
 * @package SoloToChina
 */
?>
<footer class="stc-footer">
	<div class="stc-footer__inner">
		<div class="stc-footer__brand">
			<a class="stc-brand stc-brand--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="stc-brand__mark">STC</span>
				<span class="stc-brand__name">SoloToChina</span>
			</a>
			<p>Practical China travel for independent travelers, especially solo explorers.</p>
			<div class="stc-footer__socials" aria-hidden="true">
				<span class="stc-footer__social"><svg viewBox="0 0 24 24" focusable="false"><rect x="4" y="4" width="16" height="16" rx="5"></rect><circle cx="12" cy="12" r="3.5"></circle><circle cx="17" cy="7" r="1"></circle></svg></span>
				<span class="stc-footer__social"><svg viewBox="0 0 24 24" focusable="false"><rect x="3" y="6" width="18" height="12" rx="4"></rect><path d="M10 9l5 3-5 3z"></path></svg></span>
				<span class="stc-footer__social"><svg viewBox="0 0 24 24" focusable="false"><path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9c0-.7.3-1 1-1z"></path></svg></span>
				<span class="stc-footer__social"><svg viewBox="0 0 24 24" focusable="false"><path d="M13 4v10.5a4 4 0 1 1-4-4"></path><path d="M13 7c1.4 2 3.2 3.2 5 3.5"></path></svg></span>
				<span class="stc-footer__social"><svg viewBox="0 0 24 24" focusable="false"><path d="M4 6h16v12H4z"></path><path d="M4 7l8 6 8-6"></path></svg></span>
			</div>
		</div>
		<nav class="stc-footer__column" aria-label="<?php esc_attr_e( 'Explore', 'solo-to-china' ); ?>">
			<h2>Explore</h2>
			<a href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">Survival Kit</a>
			<a href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">City Guides</a>
			<a href="<?php echo esc_url( home_url( '/attraction-guides/' ) ); ?>">Attraction Guides</a>
			<a href="<?php echo esc_url( home_url( '/planner/' ) ); ?>">Planner</a>
		</nav>
		<nav class="stc-footer__column" aria-label="<?php esc_attr_e( 'Tools', 'solo-to-china' ); ?>">
			<h2>Tools</h2>
			<a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Ticket Tool / Reminder</a>
		</nav>
		<nav class="stc-footer__column" aria-label="<?php esc_attr_e( 'Help', 'solo-to-china' ); ?>">
			<h2>Help</h2>
			<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a>
		</nav>
		<nav class="stc-footer__column" aria-label="<?php esc_attr_e( 'About', 'solo-to-china' ); ?>">
			<h2>About</h2>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About SoloToChina</a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a>
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a>
		</nav>
	</div>
	<div class="stc-footer__bottom">
		<p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> SoloToChina. All rights reserved.</p>
		<p>Guest-first. Practical. Independent.</p>
		<span class="stc-footer__seal" aria-hidden="true">STC</span>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
