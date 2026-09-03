<?php
/**
 * Standalone regression checks for commercial renderers and public event input.
 */

define( 'ABSPATH', __DIR__ );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'MINUTE_IN_SECONDS', 60 );

function esc_url_raw( $value ) { return filter_var( $value, FILTER_VALIDATE_URL ) ? $value : ''; }
function wp_parse_url( $value, $component = -1 ) { return -1 === $component ? parse_url( $value ) : parse_url( $value, $component ); }
function shortcode_atts( $defaults, $values ) { return array_merge( $defaults, array_intersect_key( $values, $defaults ) ); }
function wp_strip_all_tags( $value ) { return strip_tags( $value ); }
function sanitize_text_field( $value ) { return trim( preg_replace( '/[\r\n\t]+/', ' ', strip_tags( $value ) ) ); }
function sanitize_title( $value ) { return trim( strtolower( preg_replace( '/[^a-z0-9]+/i', '-', $value ) ), '-' ); }
function wp_unique_id( $prefix = '' ) { static $id = 0; return $prefix . ++$id; }
function wp_unslash( $value ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return esc_attr( $value ); }
function __( $value ) { return $value; }
function add_action() {}
function add_shortcode() {}
function register_rest_route() {}
function get_the_ID() { return 123; }
function home_url() { return 'http://example.test/'; }
function wp_salt() { return 'test-only-salt'; }
function wp_json_encode( $value ) { return json_encode( $value ); }
$stc_test_transients = array();
function get_transient( $key ) { global $stc_test_transients; return isset( $stc_test_transients[ $key ] ) ? $stc_test_transients[ $key ] : false; }
function set_transient( $key, $value ) { global $stc_test_transients; $stc_test_transients[ $key ] = $value; return true; }
function wp_remote_post() { return new WP_Error( 'unexpected_forward' ); }
function wp_remote_retrieve_response_code() { return 500; }

class WP_Error {
	public $code;
	public function __construct( $code ) { $this->code = $code; }
}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
class WP_REST_Response {
	public $data;
	public $status;
	public function __construct( $data, $status ) { $this->data = $data; $this->status = $status; }
}
class STC_Test_Request {
	private $origin;
	private $body;
	private $json;
	public function __construct( $origin, $json, $body = null ) { $this->origin = $origin; $this->json = $json; $this->body = null === $body ? json_encode( $json ) : $body; }
	public function get_header( $name ) { return 'origin' === $name ? $this->origin : ''; }
	public function get_body() { return $this->body; }
	public function get_json_params() { return $this->json; }
}

require dirname( __DIR__ ) . '/wp-content/themes/solo-to-china/inc/content-renderers.php';
require dirname( __DIR__ ) . '/wp-content/themes/solo-to-china/inc/commercial-events.php';

$failures = array();
function stc_test( $condition, $message ) {
	global $failures;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

stc_test( 'https://www.trip.com/path' === stc_validate_affiliate_url( 'https://www.trip.com/path' ), 'Legitimate Trip.com subdomain was rejected.' );
stc_test( '' === stc_validate_affiliate_url( 'https://trip.com.evil.example/path' ), 'Malicious lookalike hostname was accepted.' );
stc_test( '' === stc_validate_affiliate_url( 'javascript:alert(1)' ), 'JavaScript URL was accepted.' );
stc_test( '' === stc_validate_affiliate_url( 'https://user:secret@trip.com/path' ), 'Credential-bearing URL was accepted.' );

$booking = array(
	'affiliate_asset_id' => 'asset-1', 'provider' => 'Trip.com', 'asset_type' => 'DEEP_LINK', 'product_category' => 'ATTRACTION',
	'title' => 'Ticket options', 'description' => 'Review the listing.', 'cta_label' => 'View options', 'target_url' => 'https://www.trip.com/',
	'disclosure' => 'Affiliate link.', 'scope_type' => 'ENTITY', 'scope_key' => 'forbidden-city', 'slot_key' => 'slot-1',
	'placement' => 'contextual', 'strategy_version' => 'v1',
);
$booking_html = stc_render_affiliate_booking_card_component( $booking );
stc_test( false !== strpos( $booking_html, 'rel="sponsored nofollow noopener"' ), 'Safe sponsored relationship attributes are missing.' );
stc_test( false !== strpos( $booking_html, 'Affiliate link.' ), 'Visible disclosure is missing.' );
stc_test( false !== strpos( $booking_html, 'data-stc-commercial="true"' ), 'Event attribution data is missing.' );

$unknown = $booking;
$unknown['html'] = '<script>alert(1)</script>';
stc_test( '' === stc_render_affiliate_booking_card_component( $unknown ), 'Unknown/raw HTML field was not rejected.' );
$raw_title = $booking;
$raw_title['title'] = '<b>Unsafe</b>';
stc_test( '' === stc_render_affiliate_booking_card_component( $raw_title ), 'Raw HTML in a text field was not rejected.' );

$search = $booking;
$search['asset_type'] = 'SEARCH_BOX';
$search['product_category'] = 'HOTEL';
stc_test( '' !== stc_render_affiliate_search_card_component( $search ), 'Safe link-mode search card did not render.' );
$search['embed_config'] = array( 'theme' => 'light', 'src' => 'https://pages.trip.com/search', 'height' => 240, 'embed_type' => 'search_box', 'variant' => 'standard', 'width' => 640, 'language' => 'en' );
stc_test( '' === stc_render_affiliate_search_card_component( $search ), 'Search card accepted both link and embed modes.' );
unset( $search['target_url'] );
stc_test( false !== strpos( stc_render_affiliate_search_card_component( $search ), 'loading="lazy"' ), 'Safe structured search embed did not render lazily.' );
$search['embed_config']['src'] = 'https://trip.com.evil.example/embed';
stc_test( '' === stc_render_affiliate_search_card_component( $search ), 'Lookalike embed hostname was accepted.' );

$banner = $booking;
$banner['asset_type'] = 'STATIC_BANNER';
$banner['product_category'] = 'PLANNER';
$banner['placement'] = 'end_resource';
$banner['image_url'] = 'https://pages.trip.com/banner.png';
$banner['alt_text'] = 'Trip.com travel planning';
stc_test( false !== strpos( stc_render_affiliate_banner_component( $banner ), 'loading="lazy"' ), 'Safe static banner did not render lazily.' );
$banner['image_url'] = 'data:image/png;base64,unsafe';
stc_test( '' === stc_render_affiliate_banner_component( $banner ), 'Data URL banner image was accepted.' );
$banner['asset_type'] = 'DYNAMIC_BANNER';
$banner['image_url'] = '';
$banner['alt_text'] = '';
$banner['embed_config'] = array( 'embed_type' => 'dynamic_banner', 'src' => 'https://pages.trip.com/banner', 'width' => 728, 'height' => 180, 'language' => 'en', 'theme' => 'light', 'variant' => 'standard' );
$dynamic_banner_html = stc_render_affiliate_banner_component( $banner );
stc_test( false !== strpos( $dynamic_banner_html, 'sandbox="allow-forms allow-scripts allow-popups allow-popups-to-escape-sandbox"' ), 'Safe dynamic banner sandbox is missing.' );

$promotion = $booking;
$promotion['asset_type'] = 'PROMOTION';
$promotion['product_category'] = 'HOTEL';
$promotion['placement'] = 'end_resource';
$promotion['valid_until'] = '2000-01-01T00:00:00Z';
stc_test( '' === stc_render_affiliate_promotion_card_component( $promotion ), 'Expired promotion rendered.' );
$promotion['valid_until'] = '';
$promotion['valid_from'] = '2099-01-01T00:00:00Z';
stc_test( '' === stc_render_affiliate_promotion_card_component( $promotion ), 'Not-yet-valid promotion rendered.' );
$promotion['valid_from'] = '';
stc_test( '' !== stc_render_affiliate_promotion_card_component( $promotion ), 'Active promotion did not render.' );

$event = array(
	'event_type' => 'impression', 'affiliate_asset_id' => 'asset-1', 'provider' => 'Trip.com', 'category' => 'HOTEL',
	'slot_key' => 'slot-1', 'component_variant' => 'affiliate_booking_card:default', 'placement' => 'contextual',
	'timestamp' => gmdate( DATE_ATOM ), 'device' => 'desktop', 'locale' => 'en', 'strategy_version' => 'v1',
);
stc_test( is_array( stc_validate_commercial_event_payload( $event ) ), 'Valid minimal event payload was rejected.' );
$event['email'] = 'person@example.com';
stc_test( is_wp_error( stc_validate_commercial_event_payload( $event ) ), 'PII/unknown event field was accepted.' );
stc_test( ! array_intersect( array( 'name', 'email', 'cookie', 'ip', 'content' ), stc_commercial_event_allowed_fields() ), 'PII field exists in event allowlist.' );

unset( $event['email'] );
$_SERVER['REMOTE_ADDR'] = '192.0.2.1';
$relay = stc_rest_receive_commercial_event( new STC_Test_Request( 'http://example.test', $event ) );
stc_test( $relay instanceof WP_REST_Response && 202 === $relay->status && 'relay_not_configured' === $relay->data['reason'], 'Unconfigured relay did not fail safely.' );
$duplicate = stc_rest_receive_commercial_event( new STC_Test_Request( 'http://example.test', $event ) );
stc_test( $duplicate instanceof WP_REST_Response && ! empty( $duplicate->data['duplicate'] ), 'Duplicate public event was not suppressed.' );
$cross_origin = stc_rest_receive_commercial_event( new STC_Test_Request( 'https://evil.example', $event ) );
stc_test( is_wp_error( $cross_origin ) && 'stc_commercial_event_origin' === $cross_origin->code, 'Cross-origin event was not rejected.' );
$oversize = stc_rest_receive_commercial_event( new STC_Test_Request( 'http://example.test', $event, str_repeat( 'x', 4097 ) ) );
stc_test( is_wp_error( $oversize ) && 'stc_commercial_event_payload_too_large' === $oversize->code, 'Oversized event payload was not rejected.' );
$rate_key = 'stc_commercial_rate_' . substr( stc_commercial_event_request_identity(), 0, 32 );
$stc_test_transients[ $rate_key ] = 120;
$rate_limited = stc_rest_receive_commercial_event( new STC_Test_Request( 'http://example.test', $event ) );
stc_test( is_wp_error( $rate_limited ) && 'stc_commercial_event_rate_limit' === $rate_limited->code, 'Public event rate limit was not enforced.' );

if ( $failures ) {
	fwrite( STDERR, implode( PHP_EOL, $failures ) . PHP_EOL );
	exit( 1 );
}

echo "SoloToChina commercial component verification passed.\n";
