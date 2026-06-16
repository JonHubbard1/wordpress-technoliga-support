<?php
/**
 * Plugin Name: Technoliga Support
 * Plugin URI:  https://technoliga.co.uk
 * Description: Manage support tickets for your Technoliga BMS products directly from WordPress admin.
 * Version:     1.1.9
 * Author:      Technoliga
 * Author URI:  https://technoliga.co.uk
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: technoliga-support
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TECHNOLIGA_SUPPORT_VERSION', '1.1.9' );
define( 'TECHNOLIGA_SUPPORT_PATH', plugin_dir_path( __FILE__ ) );
define( 'TECHNOLIGA_SUPPORT_URL', plugin_dir_url( __FILE__ ) );
define( 'TECHNOLIGA_SUPPORT_SLUG', 'technoliga-support' );

/** Autoloader */
spl_autoload_register( function ( $class ) {
	$prefix = 'Technoliga_Support\\';
	$base_dir = TECHNOLIGA_SUPPORT_PATH . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . 'class-' . str_replace( '\\', '/', strtolower( str_replace( '_', '-', $relative_class ) ) ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/** Update checker */
add_action( 'admin_init', array( 'Technoliga_Support\Update_Checker', 'register' ) );

/** Bootstrap */
add_action( 'plugins_loaded', array( 'Technoliga_Support\Main', 'instance' ) );
