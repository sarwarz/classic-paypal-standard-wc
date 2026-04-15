<?php
/**
 * One-time migration from legacy option keys / gateway id (restore / rpsfw) to cpsw_*.
 *
 * @package Classic_PayPal_Standard_WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Copy legacy options so existing installs keep settings after prefix rename.
 */
function cpsw_migrate_legacy_stored_options() {
	static $ran = false;
	if ( $ran ) {
		return;
	}
	$ran = true;

	$old_settings = 'woocommerce_restore_paypal_standard_settings';
	$new_settings = 'woocommerce_cpsw_paypal_standard_settings';
	if ( get_option( $new_settings, null ) === null && get_option( $old_settings, null ) !== null ) {
		update_option( $new_settings, get_option( $old_settings ) );
	}

	$option_map = array(
		'rpsfw_migration_completed'                      => 'cpsw_migration_completed',
		'rpsfw_migration_notice_dismissed_permanently' => 'cpsw_migration_notice_dismissed_permanently',
		'rpsfw_migration_notice_dismissed_until'       => 'cpsw_migration_notice_dismissed_until',
		'rpsfw_migration_notice_count'                 => 'cpsw_migration_notice_count',
	);

	foreach ( $option_map as $old_key => $new_key ) {
		if ( get_option( $new_key, null ) === null && get_option( $old_key, null ) !== null ) {
			update_option( $new_key, get_option( $old_key ) );
		}
	}
}

cpsw_migrate_legacy_stored_options();

/**
 * Update order payment method meta from legacy gateway id (HPOS + posts).
 */
function cpsw_migrate_order_payment_method_ids() {
	if ( 'yes' === get_option( 'cpsw_order_payment_method_migrated', 'no' ) ) {
		return;
	}

	global $wpdb;

	if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
		$table = $wpdb->prefix . 'wc_orders';
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "UPDATE {$table} SET payment_method = 'cpsw_paypal_standard' WHERE payment_method = 'restore_paypal_standard'" );
	} else {
		$wpdb->query( $wpdb->prepare(
			"UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_key = %s AND meta_value = %s",
			'cpsw_paypal_standard',
			'_payment_method',
			'restore_paypal_standard'
		) );
	}

	update_option( 'cpsw_order_payment_method_migrated', 'yes' );
}

add_action( 'woocommerce_init', 'cpsw_migrate_order_payment_method_ids', 1 );
