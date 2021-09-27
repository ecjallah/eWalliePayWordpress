<?php
/* @wordpress-plugin
 * Plugin Name:       eWallie Pay
 * Plugin URI:        https://github.com/ecjallah/eWalliePayWordpress
 * Description:       With eWallie, you can receive payments seamlessly in real-time. All your customers need is their eWallie Username/User ID.
 * Version:           1.0.0
 * WC requires at least: 3.0
 * WC tested up to: 5.5
 * Author:            Enoch C. Jallah
 * Author URI:        https://github.com/ecjallah
 * Text Domain:       eWalliePay
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
