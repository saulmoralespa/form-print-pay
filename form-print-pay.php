<?php

/*
Plugin Name: Form print pay
Description: Formulario personalizable con impresión y medio de pago paypal
Version: 1.0.0
Author: Saul Morales Pacheco
Author URI: http://saulmoralespa.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: form-print-pay
Domain Path: /languages/
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; //Exit if accessed directly
}
if (!defined('FPP_FORM_PRINT_PAY_PLUGIN_VERSION')){
	define('FPP_FORM_PRINT_PAY_PLUGIN_VERSION', '1.0.0');
}
add_action('init', 'fpp_form_print_pay_init', 0);
function fpp_form_print_pay_init()
{
	if( ! session_id() ) {
		session_start();
	}
    load_plugin_textdomain( 'form-print-pay', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	if(!requeriments_fpp_form_print_pay()){
		return;
	}

	fpp_form_print_pay()->form_print_run();
}
add_action('notices_action_tag', 'fpp_form_print_pay_notices', 10, 1);
function fpp_form_print_pay_notices($notice){
	?>
	<div class="error notice">
		<p><?php echo $notice; ?></p>
	</div>
	<?php
}
function requeriments_fpp_form_print_pay()
{
	if ( version_compare( '5.6.0', PHP_VERSION, '>' ) ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			$php = __( 'Form print pay: Requires php version 5.6.0 or higher.', 'form-print-pay' );
			do_action('notices_action_tag', $php);
		}
		return false;
	}
	if (!function_exists('curl_version')){
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			$curl = __( 'Form print pay: Requires cURL extension to be installed.', 'form-print-pay' );
			do_action('notices_action_tag', $curl);
		}
		return false;
	}
	$openssl = __( 'Form print pay: Requires OpenSSL >= 1.0.1 to be installed on your server.', 'form-print-pay' );
	if ( ! defined( 'OPENSSL_VERSION_TEXT' ) ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			do_action('notices_action_tag', $openssl);
		}
		return false;
	}
	preg_match( '/^OpenSSL ([\d.]+)/', OPENSSL_VERSION_TEXT, $matches );
	if ( ! version_compare( $matches[1], '1.0.1', '>=' ) ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			do_action('notices_action_tag', $openssl);
		}
		return false;
	}
	return true;
}

function fpp_form_print_pay()
{
	static $plugin;
	if (!isset($plugin))
	{
		require_once ('includes/class-form-print-pay-plugin.php');
		$plugin = new FPP_Form_Print_Pay_plugin(__FILE__,FPP_FORM_PRINT_PAY_PLUGIN_VERSION,'form print pay');
	}
	return $plugin;
}
function fpp_activate_form_print_pay(){
	$upload_dir = wp_upload_dir();
	$dir = $upload_dir['basedir'] . '/form-print-pay/';
	if(!is_dir($dir)){
		fpp_form_print_pay()->createDirUploads($dir);
	}
	wp_schedule_event( time(), 'daily', 'fpp_form_print_pay' );
}
function fpp_deactivation_form_print_pay(){
	wp_clear_scheduled_hook( 'fpp_form_print_pay' );
}
register_activation_hook(__FILE__,'fpp_activate_form_print_pay');
register_deactivation_hook( __FILE__, 'fpp_deactivation_form_print_pay' );