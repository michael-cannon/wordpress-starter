<?php
/**
 * Plugin Name: WordPress Starter by Aihrus
 * Plugin URI: http://wordpress.org/plugins/wordpress-starter/
 * Description: TBD
 * Version: 1.0.0
 * Author: Michael Cannon
 * Author URI: http://aihr.us/resume/
 * License: GPLv2 or later
 * Text Domain: wordpress-starter
 * Domain Path: /languages
 */


/**
 * Copyright 2013 Michael Cannon (email: mc@aihr.us)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

if ( ! defined( 'WPS_AIHR_VERSION' ) )
	define( 'WPS_AIHR_VERSION', '1.0.3' );

if ( ! defined( 'WPS_BASE' ) )
	define( 'WPS_BASE', plugin_basename( __FILE__ ) );

if ( ! defined( 'WPS_DIR' ) )
	define( 'WPS_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'WPS_DIR_INC' ) )
	define( 'WPS_DIR_INC', WPS_DIR . 'includes/' );

if ( ! defined( 'WPS_DIR_LIB' ) )
	define( 'WPS_DIR_LIB', WPS_DIR_INC . 'libraries/' );

if ( ! defined( 'WPS_NAME' ) )
	define( 'WPS_NAME', 'WordPress Starter by Aihrus' );

if ( ! defined( 'WPS_PREMIUM_LINK' ) )
	define( 'WPS_PREMIUM_LINK', '<a href="https://aihr.us/products/wordpress-starter-premium/">Purchase WordPress Starter Premium</a>' );

if ( ! defined( 'WPS_VERSION' ) )
	define( 'WPS_VERSION', '1.0.0' );

require_once WPS_DIR_INC . 'requirements.php';

global $wps_activated;

$wps_activated = true;
if ( ! wps_requirements_check() ) {
	$wps_activated = false;

	return false;
}

require_once WPS_DIR_INC . 'class-wordpress-starter.php';


add_action( 'plugins_loaded', 'wordpress_starter_init', 99 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
if ( ! function_exists( 'wordpress_starter_init' ) ) {
	function wordpress_starter_init() {
		if ( ! is_admin() )
			return;

		if ( ! function_exists( 'add_screen_meta_link' ) )
			require_once WPS_DIR_LIB . 'screen-meta-links.php';

		if ( WordPress_Starter::version_check() ) {
			global $WordPress_Starter;
			if ( is_null( $WordPress_Starter ) )
				$WordPress_Starter = new WordPress_Starter();

			global $WordPress_Starter_Settings;
			if ( is_null( $WordPress_Starter_Settings ) )
				$WordPress_Starter_Settings = new WordPress_Starter_Settings();
			
			do_action( 'wps_init' );
		}
	}
}


register_activation_hook( __FILE__, array( 'WordPress_Starter', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'WordPress_Starter', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'WordPress_Starter', 'uninstall' ) );


if ( ! function_exists( 'wordpress_starter_shortcode' ) ) {
	function wordpress_starter_shortcode( $atts ) {
		return WordPress_Starter::wordpress_starter_shortcode( $atts );
	}
}

?>
