<?php
/**
 * Plugin Name: Classic PayPal Standard for WooCommerce
 * Description: PayPal Standard payment gateway for WooCommerce (classic hosted checkout).
 * Version: 1.0.0
 * Author: sarwarzahan
 * Author URI: https://sarwarzahan.com
 * Plugin URI: https://github.com/sarwarz/classic-paypal-standard-wc
 * Text Domain: classic-paypal-standard-wc
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 5.6
 * WC requires at least: 6.0
 * WC tested up to: 10
 * 
 * Requires Plugins: woocommerce
 * 
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants
define( 'CPSW_VERSION', '1.0.0' );
define( 'CPSW_PLUGIN_FILE', __FILE__ );
define( 'CPSW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CPSW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// PHP version check
if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {
    add_action( 'admin_notices', 'cpsw_php_version_notice' );
    return;
}

// GitHub updates: YahnisElsts/plugin-update-checker (bundled in /includes/plugin-update-checker).
require_once CPSW_PLUGIN_DIR . 'includes/cpsw-plugin-updater.php';

// Migrate legacy option keys / order payment method before anything reads new keys.
require_once CPSW_PLUGIN_DIR . 'includes/cpsw-prefix-migration.php';

// Include helper functions
require_once CPSW_PLUGIN_DIR . 'includes/functions.php';

// Include migration functionality
require_once CPSW_PLUGIN_DIR . 'includes/admin/migration.php';

// Include diagnostics
require_once CPSW_PLUGIN_DIR . 'includes/diagnostics.php';

// Include blocks support
require_once CPSW_PLUGIN_DIR . 'includes/blocks-support.php';

// Register all hooks
cpsw_register_hooks();

// Register activation hook - this cannot be moved to a function
register_activation_hook( __FILE__, 'cpsw_activation_hook' );

// Check if the enable_native_paypal option is set to 'yes'
$plugin = plugin_basename( __FILE__ );
$settings = get_option('woocommerce_cpsw_paypal_standard_settings', array());
$enable_native_paypal = isset($settings['enable_native_paypal']) && $settings['enable_native_paypal'] === 'yes';
$migration_complete = 'yes' === get_option( 'cpsw_migration_completed', 'no' );

// Run if native PayPal is enabled OR migration is not complete
if ($enable_native_paypal || !$migration_complete) {
  add_action( 'plugins_loaded', function() {
    // Ensures WooCommerce core PayPal Standard can load when migration or debug options require it.
    $paypal = class_exists( 'WC_Gateway_Paypal' ) ? new WC_Gateway_Paypal() : null;
    if ( $paypal ) {
      $paypal->update_option( '_should_load', 'yes' );
    }
    add_filter( 'woocommerce_should_load_paypal_standard', '__return_true', 9999 );
  } );
} else {
  // Ensure native PayPal is disabled after migration
  add_action( 'plugins_loaded', function() {
    if ( class_exists( 'WC_Gateway_Paypal' ) ) {
      $paypal = new WC_Gateway_Paypal();
      $paypal->update_option( '_should_load', 'no' );
    }
  }, 5 );
}