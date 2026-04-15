<?php
/**
 * Settings for PayPal Standard Gateway.
 *
 * @package WooPayPalStandard
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get settings for the specified section.
 *
 * @param string $section Section ID (general, advanced, debug)
 * @return array Settings fields
 */
if ( ! function_exists( 'cpsw_get_settings_for_section' ) ) {
    function cpsw_get_settings_for_section($section = 'general') {
        $all_settings = array(
            'general' => array(
                'general_section' => array(
                    'title'       => __( 'General Settings', 'classic-paypal-standard-wc' ),
                    'type'        => 'title',
                    'description' => __( 'Configure your PayPal Standard payment gateway settings.', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-section-title',
                ),
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', 'classic-paypal-standard-wc' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable PayPal Standard', 'classic-paypal-standard-wc' ),
                    'default' => 'no',
                ),
                'testmode' => array(
                    'title'       => __( 'Mode', 'classic-paypal-standard-wc' ),
                    'type'        => 'select',
                    'description' => __( 'Use Sandbox mode to test payments. Sandbox mode automatically enables logging.', 'classic-paypal-standard-wc' ),
                    'default'     => 'no',
                    'desc_tip'    => true,
                    'options'     => array(
                        'no'    => __( 'Live Mode', 'classic-paypal-standard-wc' ),
                        'yes' => __( 'Sandbox Mode (Testing)', 'classic-paypal-standard-wc' ),
                    ),
                    'class'       => 'wc-enhanced-select cpsw-testmode-selector',
                ),

                'email' => array(
                    'title'       => __( 'PayPal Email', 'classic-paypal-standard-wc' ),
                    'type'        => 'email',
                    'description' => __( 'Please enter your PayPal email address; this is needed in order to accept payments.', 'classic-paypal-standard-wc' ),
                    //'default'     => get_option( 'admin_email' ),
                    'desc_tip'    => true,
                    //'placeholder' => 'you@youremail.com',
                    'class'       => 'cpsw-live-field',
                ),
                'receiver_email' => array(
                    'title'       => __( 'Receiver Email', 'classic-paypal-standard-wc' ),
                    'type'        => 'email',
                    'description' => __( 'If your primary PayPal email is different from the one entered above, enter it here. This is used solely to validate IPN requests as a security measure. It is not sent to PayPal during checkout, so you can safely leave it blank if both emails are the same.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    //'placeholder' => __( 'PayPal primary email', 'classic-paypal-standard-wc' ),
                ),
                'sandbox_email' => array(
                    'title'       => __( 'Sandbox PayPal Email', 'classic-paypal-standard-wc' ),
                    'type'        => 'email',
                    'description' => sprintf( __( 'Enter the email of your PayPal sandbox account for testing. %s', 'classic-paypal-standard-wc' ), '<a href="https://developer.paypal.com/tools/sandbox/" target="_blank" rel="noopener noreferrer">' . __( 'Learn more about sandbox mode', 'classic-paypal-standard-wc' ) . '</a>' ),
                    'default'     => '',
                    'desc_tip'    => __( 'A Sandbox account is used for testing with fake money to make sure things are working correctly. Enter the email of your PayPal sandbox account for testing.', 'classic-paypal-standard-wc' ),
                    //'placeholder' => 'sandbox@example.com',
                    'class'       => 'cpsw-sandbox-field',
                ),
                'paymentaction' => array(
                    'title'       => __( 'Payment Action', 'classic-paypal-standard-wc' ),
                    'type'        => 'select',
                    'class'       => 'wc-enhanced-select',
                    'description' => __( 'Choose whether you wish to capture funds immediately or authorize payment only which you can capture at a later date.', 'classic-paypal-standard-wc' ),
                    'default'     => 'sale',
                    'desc_tip'    => true,
                    'options'     => array(
                        'sale'          => __( 'Capture', 'classic-paypal-standard-wc' ),
                        'authorization' => __( 'Authorize', 'classic-paypal-standard-wc' ),
                    ),
                ),
            ),
            'text' => array(
                'text_section' => array(
                    'title'       => __( 'Text Settings', 'classic-paypal-standard-wc' ),
                    'type'        => 'title',
                    'description' => __( 'Customize the text shown to customers during the checkout and payment process.', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-section-title',
                ),
                'title' => array(
                    'title'       => __( 'Title', 'classic-paypal-standard-wc' ),
                    'type'        => 'safe_text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'classic-paypal-standard-wc' ),
                    'placeholder' => __( 'PayPal', 'classic-paypal-standard-wc' ),
                    'default' 	  => __( 'PayPal', 'classic-paypal-standard-wc' ),
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => __( 'Description', 'classic-paypal-standard-wc' ),
                    'type'        => 'textarea',
                    'desc_tip'    => true,
                    'description' => __( 'This controls the description which the user sees during checkout.', 'classic-paypal-standard-wc' ),
                    'placeholder' => __( "Pay with PayPal - You can pay with your credit card if you don't have a PayPal account.", 'classic-paypal-standard-wc' ),
                    'default'     => __( "Pay with PayPal - You can pay with your credit card if you don't have a PayPal account.", 'classic-paypal-standard-wc' ),
                ),
                'checkout_button_text' => array(
                    'title'       => __( 'Checkout Button Text', 'classic-paypal-standard-wc' ),
                    'type'        => 'safe_text',
                    'description' => __( 'This controls the text on the PayPal checkout button.', 'classic-paypal-standard-wc' ),
                    'placeholder' => __( 'Proceed to PayPal', 'classic-paypal-standard-wc' ),
                    'default' => __( 'Proceed to PayPal', 'classic-paypal-standard-wc' ),
                    'desc_tip'    => true,
                ),
                'order_received_text' => array(
                    'title'       => __( 'Order Received Text', 'classic-paypal-standard-wc' ),
                    'type'        => 'textarea',
                    'description' => __( 'This text is displayed on the order received page for successful orders.', 'classic-paypal-standard-wc' ),
                    'placeholder' => __( 'Thank you. Your order has been received', 'classic-paypal-standard-wc' ),
                    'default' => __( 'Thank you. Your order has been received', 'classic-paypal-standard-wc' ),
                    'desc_tip'    => true,
                ),
                'pending_order_received_text' => array(
                    'title'       => __( 'Pending Order Received Text', 'classic-paypal-standard-wc' ),
                    'type'        => 'textarea',
                    'description' => __( 'This text is displayed on the order received page when payment is pending.', 'classic-paypal-standard-wc' ),
                    'placeholder' => __( 'Thank you for your order. It is currently being processed. We are waiting for PayPal to authenticate the payment.', 'classic-paypal-standard-wc' ),
                    'default' => __( 'Thank you for your order. It is currently being processed. We are waiting for PayPal to authenticate the payment.', 'classic-paypal-standard-wc' ),
                    'desc_tip'    => true,
                ),
            ),
            'advanced' => array(
                'advanced_section' => array(
                    'title'       => __( 'Advanced Settings', 'classic-paypal-standard-wc' ),
                    'type'        => 'title',
                    'description' => __( 'These are extra settings that are not required for the plugin to work. You can leave them blank if you are not sure what they are.', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-section-title',
                ),
                'invoice_prefix' => array(
                    'title'       => __( 'Invoice Prefix', 'classic-paypal-standard-wc' ),
                    'type'        => 'safe_text',
                    'description' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', 'classic-paypal-standard-wc' ),
                    'default'     => 'WC-',
                    'desc_tip'    => true,
                ),
                'paypal_generic_line_item' => array(
                    'title'       => __( 'Generic cart line at PayPal', 'classic-paypal-standard-wc' ),
                    'type'        => 'checkbox',
                    'label'       => __( 'Send only order number and amount (hide product names)', 'classic-paypal-standard-wc' ),
                    'default'     => 'no',
                    'description' => __( 'When enabled, PayPal receives a single cart line with the total instead of each product name. Use the optional label below to customize the text. Turn off if you need full line-item detail on the PayPal receipt or for reconciliation.', 'classic-paypal-standard-wc' ),
                ),
                'paypal_generic_line_text' => array(
                    'title'       => __( 'Generic line label (optional)', 'classic-paypal-standard-wc' ),
                    'type'        => 'textarea',
                    'description' => __( 'Shown on the PayPal cart line when generic mode is enabled. Leave blank for the default “Order …” wording. Placeholders: {order_number}, {order_id}, {site_name}. PayPal allows up to 127 characters.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'css'         => 'width: 25em; min-height: 4em;',
                    'placeholder' => __( 'e.g. Purchase {order_number} — {site_name}', 'classic-paypal-standard-wc' ),
                ),
                'identity_token' => array(
                    'title'       => __( 'PayPal Identity Token', 'classic-paypal-standard-wc' ),
                    'type'        => 'safe_text',
                    'description' => __( 'Enter your PayPal identity token to enable Payment Data Transfer (PDT). PDT allows PayPal to send order details directly to your website after payment, which can help reduce fraudulent orders.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                ),
                'address_override' => array(
                    'title'       => __( 'Address Override', 'classic-paypal-standard-wc' ),
                    'type'        => 'checkbox',
                    'label'       => __( 'Enable address override', 'classic-paypal-standard-wc' ),
                    'default'     => 'no',
                    'description' => __( 'Should the shipping address entered by the customer during checkout be used instead of the PayPal address? (We recommend keeping it disabled to avoid errors).', 'classic-paypal-standard-wc' ),
                    'desc_tip'    => __( 'address_override is a parameter sent to PayPal during checkout that controls whether the shipping address from your WooCommerce checkout overrides the customer\'s PayPal-stored address. This is useful for strict shipping control and fraud prevention but it comes at the cost of more errors since the customer cannot change their address at PayPal checkout. We recommend you keep this turned off.', 'classic-paypal-standard-wc' ),
                ),
                'image_url' => array(
                    'title'       => __( 'Checkout Image URL', 'classic-paypal-standard-wc' ),
                    'type'        => 'safe_text',
                    'description' => __( 'Enter a URL to an image you want to display on checkout. Leave blank to use the default image.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                ),
                'api_credentials_title' => array(
                    'title'       => __( 'API Credentials', 'classic-paypal-standard-wc' ),
                    'type'        => 'title',
                    'description' => sprintf( __( 'Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your %s.', 'classic-paypal-standard-wc' ), '<a href="https://developer.paypal.com/api/nvp-soap/PayPalAPIOverview/" target="_blank" rel="noopener noreferrer">' . __( 'PayPal API Credentials', 'classic-paypal-standard-wc' ) . '</a>' ),
                    'class'       => 'cpsw-section-title',
                ),
                'api_username' => array(
                    'title'       => __( 'Live API Username', 'classic-paypal-standard-wc' ),
                    'type'        => 'safe_text',
                    'description' => __( 'Get your API credentials from PayPal.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-live-field',
                ),
                'api_password' => array(
                    'title'       => __( 'Live API Password', 'classic-paypal-standard-wc' ),
                    'type'        => 'password',
                    'description' => __( 'Get your API credentials from PayPal.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-live-field',
                ),
                'api_signature' => array(
                    'title'       => __( 'Live API Signature', 'classic-paypal-standard-wc' ),
                    'type'        => 'password',
                    'description' => __( 'Get your API credentials from PayPal.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-live-field',
                ),
                'sandbox_api_username' => array(
                    'title'       => __( 'Sandbox API Username', 'classic-paypal-standard-wc' ),
                    'type'        => 'safe_text',
                    'description' => __( 'Get your API credentials from PayPal.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-sandbox-field',
                ),
                'sandbox_api_password' => array(
                    'title'       => __( 'Sandbox API Password', 'classic-paypal-standard-wc' ),
                    'type'        => 'password',
                    'description' => __( 'Get your API credentials from PayPal.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-sandbox-field',
                ),
                'sandbox_api_signature' => array(
                    'title'       => __( 'Sandbox API Signature', 'classic-paypal-standard-wc' ),
                    'type'        => 'password',
                    'description' => __( 'Get your API credentials from PayPal.', 'classic-paypal-standard-wc' ),
                    'default'     => '',
                    'desc_tip'    => true,
                    'placeholder' => __( 'Optional', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-sandbox-field',
                ),
            ),
            'debugging' => array(
                'debug_section' => array(
                    'title'       => __( 'Debugging Settings', 'classic-paypal-standard-wc' ),
                    'type'        => 'title',
                    'description' => __( 'These settings help with troubleshooting PayPal Standard issues.', 'classic-paypal-standard-wc' ),
                    'class'       => 'cpsw-section-title',
                ),
                'debug_enabled' => array(
                    'title'       => __( 'Enable Debugging', 'classic-paypal-standard-wc' ),
                    'type'        => 'checkbox',
                    'label'       => __( 'Enable debug logging', 'classic-paypal-standard-wc' ),
                    'default'     => 'no',
                    'description' => __( 'Log PayPal events such as IPN requests. This may help to diagnose connection issues with PayPal. The logs will be saved in WooCommerce > Status > Logs.', 'classic-paypal-standard-wc' ),
                ),
                'enable_native_paypal' => array(
                    'title'       => __( 'Enable Native PayPal Standard', 'classic-paypal-standard-wc' ),
                    'type'        => 'checkbox',
                    'label'       => __( 'Enable native PayPal Standard', 'classic-paypal-standard-wc' ),
                    'default'     => 'no',
                    'description' => __( 'DISCLAIMER: WooCommerce may remove this feature at any time. This option should not be used long-term as it relies on native code that might be removed in future WooCommerce updates. This option will display the native PayPal Standard gateway in the WooCommerce payment methods list, so you can configure it as you normally would. You may wish to also disable this gateway if you are having issues.', 'classic-paypal-standard-wc' ) . ' <a href="https://github.com/sarwarz/classic-paypal-standard-wc/issues" target="_blank" rel="noopener noreferrer">' . __( 'Please let us know', 'classic-paypal-standard-wc' ) . '</a> ' . __( 'if you experience any issues.', 'classic-paypal-standard-wc' ),
                ),
                'view_logs' => array(
                    'title'       => __( 'View Debug Logs', 'classic-paypal-standard-wc' ),
                    'type'        => 'title',
                    'description' => sprintf(
                        __( 'You can view PayPal Standard logs in the <a href="%s">WooCommerce Status > Logs</a> section.', 'classic-paypal-standard-wc' ),
                        esc_url( admin_url( 'admin.php?page=wc-status&tab=logs' ) )
                    ),
                ),
            ),
        );

        // Return the settings for the specified section
        return isset($all_settings[$section]) ? $all_settings[$section] : $all_settings['general'];
    }
}

// If section parameter is provided, return that section's settings
$section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : 'general';
$section = isset( $_GET['sub_section'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_section'] ) ) : $section; // For backward compatibility

return cpsw_get_settings_for_section($section); 