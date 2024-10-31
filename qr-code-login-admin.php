<?php
/*
 * Plugin Name: Qr Code Login Admin
 * Plugin URI: https://www.4wp.it/qr-code-login-admin
 * Description: This plugin allows only admins to login into their site without a password with Qr-code.
 * Author: 4wpBari
 * Author URI: www.4wp.it
 * Version: 1.0.2
 * Requires at least: 4.4
 * License: GPLv2
 * Text Domain: automaticqrla
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

define( 'QRLA_NAME','QR Code Login Admin' );
define( 'QRLA_REQUIRED_PHP_VERSION', '5.3' );                          // because of get_called_class()
define( 'QRLA_REQUIRED_WP_VERSION',  '3.1' );                          // because of esc_textarea()
define( 'QRLA_VER', '1.0.2' );
define( 'QRLA_PATH', __FILE__ );
define( 'QRLA_DIR', plugin_dir_path( __FILE__ ) );
define( 'QRLA_URL', plugin_dir_url( __FILE__ ) );
define( 'QRLA_CLASS', QRLA_DIR . '/class/' );
define( 'QRLA_TEMPLATE', QRLA_DIR . '/templates/' );

require_once QRLA_CLASS . 'class.qrla.php';

QRLAAutoLogin::init();