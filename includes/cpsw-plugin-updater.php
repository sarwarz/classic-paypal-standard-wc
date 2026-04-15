<?php
/**
 * GitHub updates via Plugin Update Checker (YahnisElsts/plugin-update-checker).
 *
 * @package Classic_PayPal_Standard_WC
 */

defined( 'ABSPATH' ) || exit;

$puc_file = CPSW_PLUGIN_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';

if ( ! is_readable( $puc_file ) ) {
	return;
}

require_once $puc_file;

if ( ! class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory', false ) ) {
	return;
}

// Must match your public GitHub repo (see Plugin URI in main file).
$cpsw_github_repo = apply_filters( 'cpsw_plugin_update_github_url', 'https://github.com/sarwarz/classic-paypal-standard-wc/' );

$cpsw_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	$cpsw_github_repo,
	CPSW_PLUGIN_FILE,
	dirname( plugin_basename( CPSW_PLUGIN_FILE ) )
);

// Optional: stable branch if you do not use GitHub Releases/tags yet.
// $cpsw_update_checker->setBranch( 'main' );

// Optional: private repo token (set via wp-config or filter, never commit secrets).
$cpsw_github_token = apply_filters( 'cpsw_plugin_update_github_token', ( defined( 'CPSW_GITHUB_UPDATE_TOKEN' ) ? CPSW_GITHUB_UPDATE_TOKEN : '' ) );
if ( is_string( $cpsw_github_token ) && $cpsw_github_token !== '' ) {
	$cpsw_update_checker->setAuthentication( $cpsw_github_token );
}
