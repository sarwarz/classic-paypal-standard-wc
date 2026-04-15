<?php
/**
 * Helper functions for Classic PayPal Standard for WooCommerce
 *
 * @package Classic_PayPal_Standard_WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global debugging status
 */
$cpsw_debug_enabled = null;

/**
 * Display a notice if PHP version is too low.
 */
function cpsw_php_version_notice() {
    echo '<div class="error"><p>' . esc_html__( 'Classic PayPal Standard for WooCommerce requires PHP 5.6 or higher. Please update your PHP version to use this plugin.', 'classic-paypal-standard-wc' ) . '</p></div>';
}

/**
 * WooCommerce plugin dependency check.
 * 
 * @return bool
 */
function cpsw_woocommerce_dependency_check() {
    // Check if WooCommerce class exists
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'cpsw_woocommerce_dependency_notice' );
        return false;
    }
    
    // Check if payment gateways class exists
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        return false;
    }
    
    return true;
}

/**
 * Display a notice if WooCommerce is not active.
 */
function cpsw_woocommerce_dependency_notice() {
    $install_url = wp_nonce_url(
        add_query_arg(
            array(
                'action' => 'install-plugin',
                'plugin' => 'woocommerce',
            ),
            admin_url( 'update.php' )
        ),
        'install-plugin_woocommerce'
    );

    $activate_url = wp_nonce_url(
        add_query_arg(
            array(
                'action' => 'activate',
                'plugin' => 'woocommerce/woocommerce.php',
            ),
            admin_url( 'plugins.php' )
        ),
        'activate-plugin_woocommerce/woocommerce.php'
    );

    echo '<div class="error">';
    echo '<p><strong>' . esc_html__( 'Classic PayPal Standard for WooCommerce requires WooCommerce to be installed and active.', 'classic-paypal-standard-wc' ) . '</strong></p>';
    
    if ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) {
        echo '<p><a href="' . esc_url( $install_url ) . '" class="button-primary">' . esc_html__( 'Install WooCommerce', 'classic-paypal-standard-wc' ) . '</a></p>';
    } elseif ( is_plugin_inactive( 'woocommerce/woocommerce.php' ) ) {
        echo '<p><a href="' . esc_url( $activate_url ) . '" class="button-primary">' . esc_html__( 'Activate WooCommerce', 'classic-paypal-standard-wc' ) . '</a></p>';
    }
    
    echo '</div>';
}

/**
 * Declare compatibility with WooCommerce HPOS (High-Performance Order Storage)
 */
function cpsw_declare_hpos_compatibility() {
    // Check if the class exists without using ::class syntax
    if ( class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        // Declare compatibility with custom order tables
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', CPSW_PLUGIN_FILE, true );
    }
}

/**
 * Add the gateway to WooCommerce
 * 
 * @param array $gateways Payment gateways.
 * @return array
 */
function cpsw_add_paypal_gateway( $gateways ) {
    cpsw_debug_log('cpsw: Adding gateway to WooCommerce payment gateways filter');
    cpsw_debug_log('cpsw: Current gateways count: ' . count($gateways));
    
    // Include the gateway class if it's not already loaded
    if ( ! class_exists( 'cpsw_Gateway_PayPal_Standard' ) ) {
        // Load the settings class first
        if ( ! class_exists( 'cpsw_Gateway_PayPal_Standard_Settings' ) ) {
            require_once CPSW_PLUGIN_DIR . 'includes/paypal-standard-settings.php';
        }
        
        // Then load the main gateway class
        require_once CPSW_PLUGIN_DIR . 'includes/paypal-standard.php';
        cpsw_debug_log('cpsw: Gateway class loaded');
    } else {
        cpsw_debug_log('cpsw: Gateway class already exists');
    }
    
    // Add our gateway to the list
    $gateways[] = 'cpsw_Gateway_PayPal_Standard';
    
    cpsw_debug_log('cpsw: Added gateway to gateways array. New count: ' . count($gateways));
    return $gateways;
}

/**
 * Add settings link to plugin page
 */
function cpsw_add_settings_link( $links ) {
    // Check if migration is needed but not completed
    $migration_complete = 'yes' === get_option( 'cpsw_migration_completed', 'no' );
    $has_native_settings = function_exists( 'cpsw_has_native_paypal_settings' ) && cpsw_has_native_paypal_settings();
    
    // Settings link label
    $settings_label = __( 'Settings', 'classic-paypal-standard-wc' );
    
    if (!$has_native_settings || $migration_complete) {
        // If migration is complete or not needed, link to our settings
        $settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=cpsw_paypal_standard' );
        $settings_link = '<a href="' . $settings_url . '">' . $settings_label . '</a>';
        
        if (function_exists('cpsw_debug_log')) {
            cpsw_debug_log('cpsw: Adding settings link to our PayPal settings page');
        }
    } else {
        // If migration is needed but not completed, link to native PayPal settings
        $settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=paypal' );
        $settings_link = '<a href="' . $settings_url . '">' . $settings_label . '</a>';
        
        if (function_exists('cpsw_debug_log')) {
            cpsw_debug_log('cpsw: Adding settings link to native PayPal settings page');
        }
    }
    
    array_unshift( $links, $settings_link );
    
    return $links;
}

/**
 * Initialize the plugin
 */
function woo_paypal_standard_init() {
    // Load text domain
    load_plugin_textdomain( 'classic-paypal-standard-wc', false, dirname( plugin_basename( CPSW_PLUGIN_FILE ) ) . '/languages' );
    
    // Check if WooCommerce is active
    if ( ! cpsw_woocommerce_dependency_check() ) {
        cpsw_debug_log('cpsw: WooCommerce dependency check failed');
        return;
    }
    
    cpsw_debug_log('cpsw: Plugin initialized, adding payment_gateways filter');
    
    // Add our gateway to WooCommerce
    add_filter( 'woocommerce_payment_gateways', 'cpsw_add_paypal_gateway' );
}

/**
 * Add an admin notice to configure the plugin after activation
 */
function cpsw_admin_notice() {
    // If migration notice is being shown, don't show the activation notice
    if ( function_exists( 'cpsw_has_native_paypal_settings' ) && cpsw_has_native_paypal_settings() && 'yes' !== get_option( 'cpsw_migration_completed', 'no' ) ) {
        return;
    }
    
    // Show notice either after activation or after successful migration
    if ( get_transient( 'cpsw_activation_notice' ) || get_transient( 'cpsw_migration_success' ) ) {
        // Get the settings URL
        $settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=cpsw_paypal_standard' );
        
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p>' . sprintf( 
            /* translators: %s: settings URL */
            __( 'Thank you for installing Classic PayPal Standard for WooCommerce. Please %s to start accepting payments.', 'classic-paypal-standard-wc' ),
            '<a href="' . esc_url( $settings_url ) . '">' . __( 'configure your settings', 'classic-paypal-standard-wc' ) . '</a>'
        ) . '</p>';
        echo '</div>';
        
        // Delete the transients so the notice only shows once
        delete_transient( 'cpsw_activation_notice' );
        delete_transient( 'cpsw_migration_success' );
    }
}

/**
 * Set a transient on plugin activation
 */
function cpsw_activation_hook() {
    // Set a transient to show the activation notice
    set_transient( 'cpsw_activation_notice', true, 5 * DAY_IN_SECONDS );
}

/**
 * Add debug helper function
 *
 * @param string $message Message to log
 */
function cpsw_debug_log($message) {
    global $cpsw_debug_enabled;
    
    // Initialize debug status if not set
    if ($cpsw_debug_enabled === null) {
        // Check settings
        $settings = get_option('woocommerce_cpsw_paypal_standard_settings', array());
        
        // Enable debugging if debug_enabled is set to 'yes' - keep sandbox mode removed
        $cpsw_debug_enabled = isset($settings['debug_enabled']) ? ($settings['debug_enabled'] === 'yes') : false;
    }
    
    // Only log if debugging is explicitly enabled in settings OR WP_DEBUG is enabled
    if ($cpsw_debug_enabled || (defined('WP_DEBUG') && WP_DEBUG)) {
        // Use WC logger if available
        if (function_exists('wc_get_logger')) {
            $logger = wc_get_logger();
            $logger->debug($message, array('source' => 'cpsw_paypal_standard'));
        } else {
            // Fallback to error_log only when WC logger isn't available
            error_log( 'cpsw PayPal: ' . $message );
        }
    }
}

/**
 * Hide our settings tab when migration is needed but hasn't been completed
 *
 * @param array $sections WooCommerce checkout sections
 * @return array Filtered sections
 */
function cpsw_filter_checkout_sections( $sections ) {
    // Check if migration is needed but not completed
    $migration_complete = 'yes' === get_option( 'cpsw_migration_completed', 'no' );
    $has_native_settings = function_exists( 'cpsw_has_native_paypal_settings' ) && cpsw_has_native_paypal_settings();
    
    // Remove our section if migration is needed but not completed
    if ($has_native_settings && !$migration_complete) {
        if (isset($sections['cpsw_paypal_standard'])) {
            unset($sections['cpsw_paypal_standard']);
        }
    }
    
    return $sections;
}

/**
 * Register all hooks and actions for the plugin
 */
function cpsw_register_hooks() {
    // Declare HPOS compatibility
    add_action( 'before_woocommerce_init', 'cpsw_declare_hpos_compatibility' );
    
    // Add settings link to plugin page
    add_filter( 'plugin_action_links_' . plugin_basename( CPSW_PLUGIN_FILE ), 'cpsw_add_settings_link' );
    
    // Initialize the plugin - use a higher priority to ensure WooCommerce is loaded first
    add_action( 'plugins_loaded', 'woo_paypal_standard_init', 20 );
    
    // Add admin notice
    add_action( 'admin_notices', 'cpsw_admin_notice' );
    
    // Filter payment gateways during migration
    add_filter( 'woocommerce_payment_gateways', 'cpsw_filter_payment_gateways', 30 );
    
    // Filter checkout sections to hide our settings tab if needed
    add_filter( 'woocommerce_get_sections_checkout', 'cpsw_filter_checkout_sections', 20 );
}

/**
 * Filter WooCommerce payment gateways during migration
 *
 * @param array $gateways Payment gateways
 * @return array Filtered payment gateways
 */
function cpsw_filter_payment_gateways( $gateways ) {
    cpsw_debug_log('cpsw: Filter payment gateways called - Total gateways: ' . count($gateways));
    
    // Check if migration is complete - we only hide our PayPal gateway if migration is needed
    $migration_complete = 'yes' === get_option( 'cpsw_migration_completed', 'no' );
    $has_native_settings = function_exists( 'cpsw_has_native_paypal_settings' ) && cpsw_has_native_paypal_settings();
    
    // Get the plugin settings
    $settings = get_option('woocommerce_cpsw_paypal_standard_settings', array());
    
    // Check if native PayPal is enabled via our debugging option
    $enable_native_paypal = isset($settings['enable_native_paypal']) && $settings['enable_native_paypal'] === 'yes';
    
    cpsw_debug_log('cpsw: Filter payment gateways - migration_complete: ' . ($migration_complete ? 'yes' : 'no'));
    cpsw_debug_log('cpsw: Filter payment gateways - has_native_settings: ' . ($has_native_settings ? 'yes' : 'no'));
    cpsw_debug_log('cpsw: Filter payment gateways - enable_native_paypal: ' . ($enable_native_paypal ? 'yes' : 'no'));
    
    // Hide our gateway if migration is needed but not completed
    if ($has_native_settings && !$migration_complete) {
        // Array of our PayPal gateway class names to hide
        $our_paypal_gateways = array(
            'cpsw_Gateway_PayPal_Standard'  // Our restored PayPal gateway
        );
        
        // Filter out our PayPal gateway
        $gateways = array_filter($gateways, function($gateway) use ($our_paypal_gateways) {
            // Return false to filter out our PayPal gateway, true to keep other gateways
            return !in_array($gateway, $our_paypal_gateways);
        });
        
        cpsw_debug_log('cpsw: Hiding our gateway until migration is completed');
        cpsw_debug_log('cpsw: Remaining gateways after filter: ' . count($gateways));
    } 
    // If migration is completed and native PayPal is not enabled, hide the native WooCommerce PayPal gateway
    else if ($migration_complete && !$enable_native_paypal) {
        // Native WooCommerce PayPal gateway class name
        $native_paypal_gateway = 'WC_Gateway_Paypal';
        
        // Filter out the native PayPal gateway
        $gateways = array_filter($gateways, function($gateway) use ($native_paypal_gateway) {
            // Return false to filter out native PayPal gateway, true to keep other gateways
            return $gateway !== $native_paypal_gateway;
        });
        
        cpsw_debug_log('cpsw: Hiding native WooCommerce PayPal gateway after migration');
        cpsw_debug_log('cpsw: Remaining gateways after filter: ' . count($gateways));
    } else if ($enable_native_paypal) {
        cpsw_debug_log('cpsw: Native PayPal gateway enabled via debug option');
    } else {
        cpsw_debug_log('cpsw: No gateway filtering applied');
    }
    
    return $gateways;
}
