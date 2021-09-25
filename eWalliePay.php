<?php
/* @wordpress-plugin
 * Plugin Name:       eWalliePay
 * Plugin URI:        https://ewallie.com
 * Description:       Your payments made easy
 * Version:           1.0.0
 * WC requires at least: 3.0
 * WC tested up to: 5.5
 * Author:            Enoch C. Jallah
 * Author URI:        https://github.com/ecjallah
 * Text Domain:       eWallie Pay
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if(eWaliePay_is_woocommerce_active()){
	add_filter('woocommerce_payment_gateways', 'add_eWalliePay_class');
	function add_eWalliePay_class( $gateways ){
		$gateways[] = 'CLASS_eWalliePay';
		return $gateways;
	}

	add_action('plugins_loaded', 'init_eWalliePay_class');
	function init_eWalliePay_class(){
		require 'Class.eWalliePay.php';
	}

	// add_action( 'plugins_loaded', 'other_payment_load_plugin_textdomain' );
	// function other_payment_load_plugin_textdomain() {
	//   load_plugin_textdomain( 'woocommerce-other-payment-gateway', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	// }



}


/**
 * @return bool
 */
function eWaliePay_is_woocommerce_active()
{
	$active_plugins = (array) get_option('active_plugins', array());

	if (is_multisite()) {
		$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	}

	return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}
