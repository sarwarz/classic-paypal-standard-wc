=== Classic PayPal Standard for WooCommerce ===
Contributors: sarwarzahan
Donate link: https://sarwarzahan.com
Tags: woocommerce, paypal, payment gateway, payment, standard
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 5.6
Requires Plugins: woocommerce
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Re-enables the PayPal Standard payment gateway for WooCommerce.

== Description ==

Classic PayPal Standard for WooCommerce allows you to use the PayPal Standard gateway as a payment method for <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>.

As of version 3.0 of the upstream project, this plugin includes PayPal Standard code, so it can keep working even if WooCommerce removes that integration from core in a future release.

Previously, related work only re-enabled the menu item while PayPal Standard still lived inside WooCommerce. Because WooCommerce has been deprecating PayPal Standard, this plugin provides a self-contained gateway you can rely on without disruption.

PayPal has confirmed that they have no current plans to discontinue support for Standard PayPal.

This plugin is maintained by an official PayPal Partner. It is not an official WooCommerce add-on or extension and is not affiliated with WooCommerce, WordPress, or Automattic Inc.

Project home: <a href="https://github.com/sarwarz/classic-paypal-standard-wc">GitHub</a>

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/classic-paypal-standard-wc` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Open **WooCommerce &rarr; Settings &rarr; Payments**, enable **PayPal Standard** (Classic PayPal Standard), and configure your PayPal email, identity token, and other options as needed.

== Frequently Asked Questions ==

= Does this work with the latest version of WooCommerce? =

Yes. The plugin declares compatibility with supported WooCommerce releases; see the main plugin file headers for **WC tested up to**.

= Can I use this alongside other PayPal payment gateways? =

Yes. You can use this gateway alongside other PayPal gateways (for example PayPal Payments). Standard PayPal can run alongside those gateways without conflict.

= Where can I get support? =

Please open an issue on the <a href="https://github.com/sarwarz/classic-paypal-standard-wc">GitHub repository</a> or use your usual support channel for the distribution where you obtained the plugin.

= Migrating from WooCommerce’s built-in PayPal Standard =

If native PayPal Standard settings are still present, an admin notice may offer to copy them into this plugin. Complete the steps there; there is no separate **Start Migration** link on the Plugins screen.

== Screenshots ==

1. WooCommerce **Payments** settings with Classic PayPal Standard available and configurable.

== Changelog ==

= 1.0.0 =
* Fork release: version reset, author metadata updated, deactivation survey / remote tracking removed, PayPal build notation updated.
* Prefix and option migration for gateway id `cpsw_paypal_standard` and related settings.
* Bundled GitHub plugin update checker (optional token / filters).
* Fix: PDT processing on the thank-you page now fires `woocommerce_thankyou_{gateway_id}` so it matches the renamed gateway.
* Improvement: PayPal NVP (refund/capture) uses `wp_safe_remote_post`, consistent with IPN/PDT.
* Improvement: Migration dismiss AJAX uses `absint` / `wp_unslash` on request data; nonces verified with `wp_unslash`.
* UI: Removed the **Start Migration** row action from the Plugins list (migration remains via the admin notice when applicable).

= 3.1.0 =
* 12/16/25
* New - Added full WooCommerce Blocks support for block-based checkout
* New - Added PayPal Standard Diagnostics to WooCommerce System Status page
* Enhancement - Updated payment method display to show PayPal logo only (removed text label)
* Enhancement - Improved description formatting with better spacing for sandbox mode message
* Enhancement - Optimized icon display for both classic and block-based checkout (24px height)
* Fix - Resolved "no payment methods available" error with WooCommerce 10.4+ block-based checkout
* Fix - Improved gateway availability checks for better reliability
* Fix - Ensured proper HTML rendering in payment method descriptions for blocks checkout

= 3.0.1 =
* 9/22/25
* Fix - Fixed PHP deprecated warning that occurred with PHP 8.2+.

= 3.0 =
* 4/30/25
* New - Built-in PayPal Standard code (useful if WooCommerce removes PayPal Standard from core later).

= 1.0.6 =
* Tested: WordPress 6.7
* Removed: Email to admin

= 1.0.5 =
* Tested: WooCommerce 9.4.0-beta

= 1.0.4 =
* Fix: plugin not working with WooCommerce v. 9.1.4

= 1.0.3 =
* Checked compatibility with High-Performance Order Storage (HPOS)

= 1.0.2 =
* Checked compatibility with WordPress 6.1 and WooCommerce 7.2.3

= 1.0.1 =
* Checked compatibility with WordPress 6.0.2 and WooCommerce 6.8.2

= 1.0.0 (upstream predecessor) =
* Initial release of the original restore / bridge plugin (lineage before built-in PayPal Standard code).

== Upgrade Notice ==

= 1.0.0 =
Fork under new slug and maintainer. If you used a legacy build, options and order payment methods are migrated automatically where possible.
