<?php
/**
 * Theme footer.
 *
 * @package SoloToChina
 */
?>
<footer class="stc-footer">
	<div>
		<a class="stc-brand stc-brand--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="stc-brand__mark">STC</span>
			<span class="stc-brand__name">SoloToChina</span>
		</a>
		<p>Practical China travel for independent travelers.</p>
	</div>
	<div>
		<h2>Explore</h2>
		<a href="<?php echo esc_url( home_url( '/survival-kit/' ) ); ?>">Survival Kit</a>
		<a href="<?php echo esc_url( home_url( '/city-guides/' ) ); ?>">City Guides</a>
		<a href="<?php echo esc_url( home_url( '/attraction-guides/' ) ); ?>">Attraction Guides</a>
		<a href="<?php echo esc_url( home_url( '/planner/' ) ); ?>">Planner</a>
	</div>
	<div>
		<h2>Tools</h2>
		<a href="<?php echo esc_url( home_url( '/tools/' ) ); ?>">Ticket Tool / Reminder</a>
		<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a>
	</div>
	<div>
		<h2>About</h2>
		<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About SoloToChina</a>
		<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a>
		<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
