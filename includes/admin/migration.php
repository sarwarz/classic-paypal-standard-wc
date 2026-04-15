<?php
/**
 * PayPal Standard Migration functionality
 *
 * @package Classic_PayPal_Standard_WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if WooCommerce's native PayPal settings exist and the migration has not been completed
 * 
 * @return boolean True if native PayPal settings exist and migration is needed
 */
function cpsw_has_native_paypal_settings() {
    // Check if migration has already been completed
    if ( 'yes' === get_option( 'cpsw_migration_completed', 'no' ) ) {
        return false;
    }
    
    // Check if user has permanently dismissed the notice
    if ( 'yes' === get_option( 'cpsw_migration_notice_dismissed_permanently', 'no' ) ) {
        return false;
    }
    
    // Check if notice was temporarily dismissed and if the dismissal period has expired
    $dismissal_timestamp = get_option( 'cpsw_migration_notice_dismissed_until', 0 );
    if ( $dismissal_timestamp && time() < $dismissal_timestamp ) {
        return false;
    }
    
    // Get the native PayPal settings
    $native_settings = get_option( 'woocommerce_paypal_settings', array() );
    
    // Default WooCommerce PayPal settings - exact match from the user's provided serialized array
    $default_settings = array(
        'enabled' => 'no',
        'title' => 'PayPal',
        'description' => 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.',
        'email' => 'test@test.com',
        'advanced' => '',
        'testmode' => 'no',
        'debug' => 'no',
        'ipn_notification' => 'yes',
        'receiver_email' => 'test@test.com',
        'identity_token' => '',
        'invoice_prefix' => 'WC-',
        'send_shipping' => 'yes',
        'address_override' => 'no',
        'paymentaction' => 'sale',
        'image_url' => '',
        'api_details' => '',
        'api_username' => '',
        'api_password' => '',
        'api_signature' => '',
        'sandbox_api_username' => '',
        'sandbox_api_password' => '',
        'sandbox_api_signature' => ''
    );
    
    // If settings match default settings exactly, don't show the migration notice
    if (!empty($native_settings)) {
		
		// Check if all keys in the default settings are present and equal in the native settings
		$settings_match = true;
		
		
		foreach ($default_settings as $key => $value) {
			if (!isset($native_settings[$key]) || $native_settings[$key] !== $value) {
				$settings_match = false;
				break;
			}
		}
		
		// Also check if any extra keys exist in native settings that aren't in the defaults
		if ($settings_match) {
			foreach ($native_settings as $key => $value) {
				if (!isset($default_settings[$key])) {
					$settings_match = false;
					break;
				}
			}
		}
		
		// If settings match default settings and _should_load is 'no', don't show migration notice
		if ($settings_match) {
			return false;
		}
		
    }
    
    // Check if native PayPal settings exist with at least email or sandbox_email
    if ( ! empty( $native_settings ) && 
        ( ! empty( $native_settings['email'] ) || ! empty( $native_settings['sandbox_email'] ) ) ) {
        
        return true;
    }
    
    return false;
}















/**
 * Migrate settings from WooCommerce's native PayPal to our restored version
 */
function cpsw_migrate_native_paypal_settings() {
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        return;
    }
    
    // Get the native PayPal settings
    $native_settings = get_option( 'woocommerce_paypal_settings', array() );
    
    // Get our plugin's settings
    $restored_settings = get_option( 'woocommerce_cpsw_paypal_standard_settings', array() );
    
    // List of settings to migrate (mapping between native and restored keys)
    $settings_to_migrate = array(
        // Common fields (same keys)
        'enabled'          => 'enabled',
        'title'            => 'title',
        'description'      => 'description',
        'identity_token'   => 'identity_token',
        'invoice_prefix'   => 'invoice_prefix',
        'image_url'        => 'image_url',
        'debug'            => 'debug_enabled',
        'testmode'         => 'testmode',
        
        // Special mappings
        'address_override' => 'address_override',
        'send_shipping'    => 'send_shipping',
        'ipn_notification' => 'ipn_notification',

        // API Credentials
        'api_username'     => 'api_username',
        'api_password'     => 'api_password',
        'api_signature'    => 'api_signature',
        'sandbox_api_username' => 'sandbox_api_username',
        'sandbox_api_password' => 'sandbox_api_password',
        'sandbox_api_signature' => 'sandbox_api_signature',
        'paymentaction'    => 'paymentaction',
        'advanced'         => 'advanced',
        '_should_load'     => '_should_load',
    );
    
    // Migrate the settings
    foreach ( $settings_to_migrate as $native_key => $restored_key ) {
        if ( isset( $native_settings[ $native_key ] ) ) {
            $restored_settings[ $restored_key ] = $native_settings[ $native_key ];
        }
    }
    
    // Handle email settings based on sandbox mode
    $is_sandbox = isset($native_settings['testmode']) && 'yes' === $native_settings['testmode'];
    
    if ($is_sandbox) {
        // In sandbox mode, use sandbox_email as primary email
        if (!empty($native_settings['sandbox_email'])) {
            $restored_settings['sandbox_email'] = $native_settings['sandbox_email'];
            // Also use sandbox_email for receiver_email in sandbox mode if it exists
            if (empty($native_settings['receiver_email'])) {
                $restored_settings['receiver_email'] = $native_settings['sandbox_email'];
            } else {
                $restored_settings['receiver_email'] = $native_settings['receiver_email'];
            }
        } elseif (!empty($native_settings['email'])) {
            // Fallback to regular email if sandbox_email is not set
            $restored_settings['sandbox_email'] = $native_settings['email'];
            // Also use email for receiver_email if it exists and receiver_email doesn't
            if (empty($native_settings['receiver_email'])) {
                $restored_settings['receiver_email'] = $native_settings['email'];
            } else {
                $restored_settings['receiver_email'] = $native_settings['receiver_email'];
            }
        }
    } else {
        // In live mode, use regular email
        if (!empty($native_settings['email'])) {
            $restored_settings['email'] = $native_settings['email'];
            // Also use email for receiver_email if it exists and receiver_email doesn't
            if (empty($native_settings['receiver_email'])) {
                $restored_settings['receiver_email'] = $native_settings['email'];
            } else {
                $restored_settings['receiver_email'] = $native_settings['receiver_email'];
            }
        }
        // Still copy sandbox_email if it exists
        if (!empty($native_settings['sandbox_email'])) {
            $restored_settings['sandbox_email'] = $native_settings['sandbox_email'];
        }
    }
    
    // Set the plugin as enabled if native gateway was enabled
    if (isset($native_settings['enabled']) && 'yes' === $native_settings['enabled']) {
        $restored_settings['enabled'] = 'yes';
        
        // Disable the native PayPal gateway
        $native_settings['enabled'] = 'no';
        update_option( 'woocommerce_paypal_settings', $native_settings );
    }
    
    // Save the restored settings
    update_option( 'woocommerce_cpsw_paypal_standard_settings', $restored_settings );
    
    // Set default values for text fields if they are blank - these are not set by default in the native paypal standard settings.
    if (empty($restored_settings['checkout_button_text'])) {
        $restored_settings['checkout_button_text'] = __('Proceed to PayPal', 'classic-paypal-standard-wc');
    }
    if (empty($restored_settings['order_received_text'])) {
        $restored_settings['order_received_text'] = __('Thank you. Your order has been received', 'classic-paypal-standard-wc');
    }
    if (empty($restored_settings['pending_order_received_text'])) {
        $restored_settings['pending_order_received_text'] = __('Thank you for your order. It is currently being processed. We are waiting for PayPal to authenticate the payment.', 'classic-paypal-standard-wc');
    }
    
    // Update settings again with default values if needed
    update_option( 'woocommerce_cpsw_paypal_standard_settings', $restored_settings );
    
    // Mark migration as completed
    update_option( 'cpsw_migration_completed', 'yes' );
    
    // Set a transient to show a success message
    set_transient( 'cpsw_migration_success', true, 60 );
    
    cpsw_debug_log('Migration from native PayPal Standard to Classic PayPal Standard completed successfully');
}

/**
 * Display the migration notice
 */
function cpsw_display_migration_notice() {
    // Only show to admin users
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        return;
    }
    
    // Check if native PayPal settings exist
    if ( ! cpsw_has_native_paypal_settings() ) {
        return;
    }
    
    // Check if this is the first or second notice
    $notice_count = intval( get_option( 'cpsw_migration_notice_count', 0 ) );
    $notice_class = 'notice-warning';
    
    // Display the migration notice
    cpsw_render_migration_notice( $notice_count );
    
    // Increment the notice count for next time
    update_option( 'cpsw_migration_notice_count', $notice_count + 1 );
}

/**
 * Render the migration notice with the given notice count
 *
 * @param int $notice_count The number of times the notice has been shown
 */
function cpsw_render_migration_notice( $notice_count ) {
    $notice_class = 'notice-warning';
    ?>
    <div class="notice <?php echo esc_attr( $notice_class ); ?> is-dismissible cpsw-migration-notice" style="padding:15px; border-left-color:#FFB900;" data-notice-id="cpsw-migration-notice">
        <h2 style="margin-top:0;"><?php esc_html_e( 'Important: Switch to Classic PayPal Standard', 'classic-paypal-standard-wc' ); ?></h2>
        <p><?php esc_html_e( 'WooCommerce is phasing out its built-in PayPal Standard payment gateway and may remove it entirely in a future update.', 'classic-paypal-standard-wc' ); ?></p>
        <p><?php esc_html_e( 'To ensure your site continues running without interruption, Classic PayPal Standard for WooCommerce now includes its own standalone PayPal Standard integration - no longer relying on WooCommerce\'s native code.', 'classic-paypal-standard-wc' ); ?></p>
        <p><?php esc_html_e( 'This means your PayPal Standard setup will continue to work reliably, regardless of future changes in WooCommerce.', 'classic-paypal-standard-wc' ); ?></p>
        <p><strong><?php esc_html_e( 'We highly recommend switching now to avoid any disruption. With your consent, your existing PayPal Standard settings will be copied over automatically to this plugin and the native PayPal Standard Code will be disabled.', 'classic-paypal-standard-wc' ); ?></strong></p>
        <p><?php esc_html_e( 'After clicking the button below everything should continue to work as normal with no other changes needed.', 'classic-paypal-standard-wc' ); ?></p>
        <p>
            <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'cpsw_migrate_paypal' ), 'cpsw_migrate_paypal', 'cpsw_nonce' ) ); ?>" class="button button-primary">
                <?php esc_html_e( 'Switch to Classic PayPal Standard', 'classic-paypal-standard-wc' ); ?>
            </a>
        </p>
    </div>
    <?php
    
    // Add JavaScript to handle notice dismissal
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $(document).on('click', '.cpsw-migration-notice .notice-dismiss', function() {
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'cpsw_dismiss_migration_notice',
                    nonce: '<?php echo wp_create_nonce( 'cpsw_dismiss_migration_notice' ); ?>',
                    notice_count: <?php echo esc_js( $notice_count ); ?>
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * Handle AJAX request to dismiss migration notice temporarily
 */
function cpsw_handle_dismiss_migration_notice() {
    // Verify nonce
    if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['nonce'] ), 'cpsw_dismiss_migration_notice' ) ) {
        wp_die( '', '', array( 'response' => 403 ) );
    }
    
    // Get the notice count from the AJAX request
    $notice_count = isset( $_REQUEST['notice_count'] ) ? absint( wp_unslash( $_REQUEST['notice_count'] ) ) : 0;
    
    // If this is the second dismissal (notice_count is already 1 or higher), then permanently dismiss
    if ( $notice_count >= 1 ) {
        // Mark notice as permanently dismissed
        update_option( 'cpsw_migration_notice_dismissed_permanently', 'yes' );
    } else {
        // Set dismissal for 1 week
        $one_week = 7 * DAY_IN_SECONDS;
        update_option( 'cpsw_migration_notice_dismissed_until', time() + $one_week );
    }
    
    wp_die();
}

/**
 * Handle permanent dismissal of migration notice
 */
function cpsw_handle_permanent_dismiss_migration_notice() {
    // Check if this is our dismiss action
    if ( isset( $_GET['action'] ) && 'cpsw_dismiss_migration_notice_permanently' === $_GET['action'] ) {
        // Verify nonce
        if ( ! isset( $_GET['cpsw_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['cpsw_nonce'] ), 'cpsw_dismiss_migration_notice_permanently' ) ) {
            wp_die( esc_html__( 'Security check failed', 'classic-paypal-standard-wc' ) );
        }
        
        // Mark notice as permanently dismissed
        update_option( 'cpsw_migration_notice_dismissed_permanently', 'yes' );
        
        // Redirect back to the current page without action parameters
        wp_safe_redirect( remove_query_arg( array( 'action', 'cpsw_nonce' ) ) );
        exit;
    }
}

/**
 * Display migration success notice
 */
function cpsw_display_migration_success_notice() {
    // Check if the success transient is set
    if ( ! get_transient( 'cpsw_migration_success' ) ) {
        return;
    }
    
    // Display the success notice
    ?>
    <div class="notice notice-success is-dismissible">
        <p><strong><?php esc_html_e( 'PayPal Standard settings successfully migrated!', 'classic-paypal-standard-wc' ); ?></strong></p>
        <p><?php esc_html_e( 'Your PayPal Standard settings have been migrated from WooCommerce\'s native integration to Classic PayPal Standard for WooCommerce.', 'classic-paypal-standard-wc' ); ?></p>
        <p><?php esc_html_e( 'WooCommerce\'s native PayPal Standard gateway has been disabled, and this plugin has been enabled with your existing settings.', 'classic-paypal-standard-wc' ); ?></p>
        <p><b><?php esc_html_e( 'If you experience any issues, please enable the "Enable Native PayPal Standard" option in the settings debugging tab and let us know.', 'classic-paypal-standard-wc' ); ?></b></p>
    </div>
    <?php
    
    // Don't delete the transient here, let cpsw_admin_notice() function use it too
}

/**
 * Handle the migration action
 */
function cpsw_handle_migration_action() {
    // Check if this is our migration action
    if ( isset( $_GET['action'] ) && 'cpsw_migrate_paypal' === $_GET['action'] ) {
        // Verify nonce
        if ( ! isset( $_GET['cpsw_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['cpsw_nonce'] ), 'cpsw_migrate_paypal' ) ) {
            wp_die( esc_html__( 'Security check failed', 'classic-paypal-standard-wc' ) );
        }
        
        // Run the migration
        cpsw_migrate_native_paypal_settings();
        
        // Redirect back to the current page without action parameters
        wp_safe_redirect( remove_query_arg( array( 'action', 'cpsw_nonce' ) ) );
        exit;
    }
}

// Register hooks
add_action( 'admin_notices', 'cpsw_display_migration_notice' );
add_action( 'admin_notices', 'cpsw_display_migration_success_notice' );
add_action( 'admin_init', 'cpsw_handle_migration_action' );
add_action( 'admin_init', 'cpsw_handle_permanent_dismiss_migration_notice' );
add_action( 'wp_ajax_cpsw_dismiss_migration_notice', 'cpsw_handle_dismiss_migration_notice' );