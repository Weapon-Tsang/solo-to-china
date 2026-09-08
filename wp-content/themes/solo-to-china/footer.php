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
			<div class="stc-footer__contact">
				<a href="mailto:alex@solotochina.com">alex@solotochina.com</a>
				<a href="https://wa.me/19098361987">WhatsApp: +1 909-836-1987</a>
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
			<a href="<?php echo esc_url( home_url( '/tools/find-this-place/' ) ); ?>">Find This Place</a>
			<a href="<?php echo esc_url( home_url( '/tools/taxi-card/' ) ); ?>">Taxi Card</a>
			<a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Ticket Booking Window</a>
		</nav>
		<nav class="stc-footer__column" aria-label="<?php esc_attr_e( 'Help', 'solo-to-china' ); ?>">
			<h2>Help</h2>
			<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a>
		</nav>
		<nav class="stc-footer__column" aria-label="<?php esc_attr_e( 'About', 'solo-to-china' ); ?>">
			<h2>About</h2>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About SoloToChina</a>
			<a href="<?php echo esc_url( home_url( '/affiliate-disclosure/' ) ); ?>">Affiliate Disclosure</a>
			<a href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>">Disclaimer</a>
		</nav>
	</div>
	<div class="stc-footer__bottom">
		<p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> SoloToChina. All rights reserved.</p>
		<nav class="stc-footer__legal" aria-label="<?php esc_attr_e( 'Legal', 'solo-to-china' ); ?>">
			<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a>
			<a href="<?php echo esc_url( home_url( '/terms-of-use/' ) ); ?>">Terms of Use</a>
			<a href="<?php echo esc_url( home_url( '/affiliate-disclosure/' ) ); ?>">Affiliate Disclosure</a>
		</nav>
		<p class="stc-footer__principles">Guest-first. Practical. Independent.</p>
		<span class="stc-footer__seal" aria-hidden="true">STC</span>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
