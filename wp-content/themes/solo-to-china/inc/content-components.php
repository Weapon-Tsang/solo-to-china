<?php
/**
 * Native Gutenberg patterns for the Content Component Contract.
 *
 * These are insertable semantic components, not fixed article templates.
 *
 * @package SoloToChina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the reusable core-block component patterns.
 *
 * @return array<string, array<string, string>>
 */
function stc_content_component_patterns() {
	return [
		'quick-answer'  => [
			'title'       => __( 'Content Component: Quick Answer', 'solo-to-china' ),
			'description' => __( 'An answer-first summary for the beginning of a practical guide.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--quick-answer","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--quick-answer"><!-- wp:heading --><h2 class="wp-block-heading">Quick answer</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Give the direct practical answer first, then explain important limits or exceptions.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
		],
		'key-takeaways' => [
			'title'       => __( 'Content Component: Key Takeaways', 'solo-to-china' ),
			'description' => __( 'A compact list of the decisions that matter most.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--key-takeaways","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--key-takeaways"><!-- wp:heading --><h2 class="wp-block-heading">Key takeaways</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>Put the highest-impact decision first.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Keep each takeaway short and actionable.</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:group -->',
		],
		'quick-facts'   => [
			'title'       => __( 'Content Component: Quick Facts', 'solo-to-china' ),
			'description' => __( 'Practical label and value pairs without dashboard styling.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--quick-facts","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--quick-facts"><!-- wp:heading --><h2 class="wp-block-heading">At a glance</h2><!-- /wp:heading --><!-- wp:group {"className":"stc-content-block__facts","layout":{"type":"grid","minimumColumnWidth":"12rem"}} --><div class="wp-block-group stc-content-block__facts"><!-- wp:group {"className":"stc-content-block__fact","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block__fact"><!-- wp:paragraph --><p><strong>Best time</strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Add a concise planning value.</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:group {"className":"stc-content-block__fact","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block__fact"><!-- wp:paragraph --><p><strong>Time needed</strong></p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Add a realistic duration.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group --></div><!-- /wp:group -->',
		],
		'tip'           => [
			'title'       => __( 'Content Component: Tip', 'solo-to-china' ),
			'description' => __( 'Low-priority supporting advice.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--tip","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--tip"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Solo traveler tip</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Add friendly advice that makes the plan easier without interrupting the main answer.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
		],
		'warning'       => [
			'title'       => __( 'Content Component: Warning', 'solo-to-china' ),
			'description' => __( 'A restrained prerequisite or travel risk.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--warning","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--warning"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Before you continue</h3><!-- /wp:heading --><!-- wp:paragraph --><p>State the risk or requirement in explicit text and explain the safest next action.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
		],
		'steps'         => [
			'title'       => __( 'Content Component: Steps', 'solo-to-china' ),
			'description' => __( 'A clear ordered process for setup, booking, or routes.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--steps","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--steps"><!-- wp:heading --><h2 class="wp-block-heading">Step by step</h2><!-- /wp:heading --><!-- wp:list {"ordered":true} --><ol class="wp-block-list"><!-- wp:list-item --><li>Complete the first required action.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Verify the result before moving on.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Keep a practical fallback.</li><!-- /wp:list-item --></ol><!-- /wp:list --></div><!-- /wp:group -->',
		],
		'checklist'     => [
			'title'       => __( 'Content Component: Checklist', 'solo-to-china' ),
			'description' => __( 'A fast-scanning preparation list.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--checklist","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--checklist"><!-- wp:heading --><h2 class="wp-block-heading">Checklist</h2><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>Confirm the essential document or booking.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Save an offline copy or fallback.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Check the final timing before departure.</li><!-- /wp:list-item --></ul><!-- /wp:list --></div><!-- /wp:group -->',
		],
		'comparison'    => [
			'title'       => __( 'Content Component: Comparison', 'solo-to-china' ),
			'description' => __( 'A semantic comparison table with component-level mobile scrolling.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--comparison","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--comparison"><!-- wp:heading --><h2 class="wp-block-heading">Compare options</h2><!-- /wp:heading --><!-- wp:table {"className":"stc-content-block__table"} --><figure class="wp-block-table stc-content-block__table"><table><caption>Choose the option that fits your trip</caption><thead><tr><th scope="col">Option</th><th scope="col">Best for</th><th scope="col">Tradeoff</th></tr></thead><tbody><tr><th scope="row">Option A</th><td>First-time visitors</td><td>Add a concise tradeoff</td></tr><tr><th scope="row">Option B</th><td>Flexible travelers</td><td>Add a concise tradeoff</td></tr></tbody></table></figure><!-- /wp:table --></div><!-- /wp:group -->',
		],
		'faq'           => [
			'title'       => __( 'Content Component: FAQ', 'solo-to-china' ),
			'description' => __( 'Native question and answer disclosures.', 'solo-to-china' ),
			'content'     => '<!-- wp:group {"className":"stc-content-block stc-content-block--faq","layout":{"type":"constrained"}} --><div class="wp-block-group stc-content-block stc-content-block--faq"><!-- wp:heading --><h2 class="wp-block-heading">Frequently asked questions</h2><!-- /wp:heading --><!-- wp:details --><details class="wp-block-details"><summary>Write a specific traveler question</summary><!-- wp:paragraph --><p>Answer directly in visible, indexable text.</p><!-- /wp:paragraph --></details><!-- /wp:details --><!-- wp:details --><details class="wp-block-details"><summary>Add a second practical question</summary><!-- wp:paragraph --><p>Keep the answer concise and complete.</p><!-- /wp:paragraph --></details><!-- /wp:details --></div><!-- /wp:group -->',
		],
	];
}

/**
 * Register insertable components in the existing SoloToChina pattern category.
 */
function stc_register_content_component_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	foreach ( stc_content_component_patterns() as $slug => $pattern ) {
		register_block_pattern(
			'solo-to-china/content-components-' . $slug,
			[
				'title'       => $pattern['title'],
				'description' => $pattern['description'],
				'categories'  => [ 'solo-to-china' ],
				'content'     => $pattern['content'],
			]
		);
	}
}
add_action( 'init', 'stc_register_content_component_patterns', 12 );

/**
 * Keep Contract content images lazy and asynchronously decoded on the frontend.
 *
 * Core Image block markup stays Gutenberg-valid in post_content; presentation
 * attributes are added only while WordPress renders the public block.
 *
 * @param string               $block_content Rendered Image block HTML.
 * @param array<string, mixed> $block Parsed block data.
 * @return string
 */
function stc_render_content_image_attributes( $block_content, $block ) {
	if ( false === strpos( $block_content, 'stc-content-image' ) ) {
		return $block_content;
	}

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag( 'img' ) ) {
			$processor->set_attribute( 'loading', 'lazy' );
			$processor->set_attribute( 'decoding', 'async' );
			return $processor->get_updated_html();
		}
	}

	return preg_replace( '/<img\b/i', '<img loading="lazy" decoding="async"', $block_content, 1 );
}
add_filter( 'render_block_core/image', 'stc_render_content_image_attributes', 10, 2 );
