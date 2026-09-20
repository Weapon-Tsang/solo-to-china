<?php
/** Ephemeral local-only WordPress REST credential for cross-repository preview. */
if ( ! defined( 'ABSPATH' ) || 'local' !== wp_get_environment_type() ) {
	throw new RuntimeException( 'Local WordPress environment is required.' );
}
$credential_dir = '/tmp/solo-to-china-preview-credentials';
if ( ! is_dir( $credential_dir ) || ! is_writable( $credential_dir ) ) {
	throw new RuntimeException( 'The isolated preview credential mount is unavailable.' );
}
wp_set_password( 'LocalOnly-WP-V3-2026!', 1 );
$created = WP_Application_Passwords::create_new_application_password( 1, array( 'name' => 'SoloToChina local CMS preview' ) );
if ( is_wp_error( $created ) || empty( $created[0] ) ) {
	throw new RuntimeException( 'Could not create an isolated local application password.' );
}
file_put_contents( $credential_dir . '/wp-application-password.txt', $created[0], LOCK_EX );
echo "Isolated local WordPress REST credential created.\n";
