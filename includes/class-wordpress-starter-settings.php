<?php
/*
	Copyright 2013 Michael Cannon (email: mc@aihr.us)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * WordPress Starter settings class
 *
 * Based upon http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */

require_once WPS_DIR_LIB . 'aihrus-framework/class-aihrus-settings.php';

if ( class_exists( 'WordPress_Starter_Settings' ) )
	return;


class WordPress_Starter_Settings extends Aihrus_Settings {
	const ID   = 'wordpress-starter-settings';
	const NAME = 'WordPress Starter Settings';

	public static $admin_page;
	public static $class      = __CLASS__;
	public static $defaults   = array();
	public static $plugin_assets;
	public static $plugin_url = 'http://wordpress.org/plugins/wordpress-starter/';
	public static $sections = array();
	public static $settings = array();
	public static $version;


	public function __construct() {
		parent::__construct();

		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );
	}


	public static function admin_init() {
		$version       = wps_get_option( 'version' );
		self::$version = WordPress_Starter::VERSION;
		self::$version = apply_filters( 'wps_version', self::$version );

		if ( $version != self::$version )
			self::initialize_settings();

		if ( ! WordPress_Starter::do_load() )
			return;

		self::load_options();
		self::register_settings();
	}


	public static function admin_menu() {
		self::$admin_page = add_options_page( esc_html__( 'WordPress Starter Settings', 'wordpress-starter' ), esc_html__( 'WordPress Starter', 'wordpress-starter' ), 'manage_options', self::ID, array( __CLASS__, 'display_page' ) );

		add_action( 'admin_print_scripts-' . self::$admin_page, array( __CLASS__, 'scripts' ) );
		add_action( 'admin_print_styles-' . self::$admin_page, array( __CLASS__, 'styles' ) );
		add_action( 'load-' . self::$admin_page, array( __CLASS__, 'settings_add_help_tabs' ) );

		add_screen_meta_link(
			'wsp_importer_link',
			esc_html__( 'WordPress Starter Processor', 'wordpress-starter' ),
			admin_url( 'tools.php?page=' . WordPress_Starter::ID ),
			self::$admin_page,
			array( 'style' => 'font-weight: bold;' )
		);
	}


	public static function init() {
		load_plugin_textdomain( 'wordpress-starter', false, '/wordpress-starter/languages/' );

		self::$plugin_assets = WordPress_Starter::$plugin_assets;
	}


	public static function sections() {
		self::$sections['general'] = esc_html__( 'General', 'wordpress-starter' );
		self::$sections['testing'] = esc_html__( 'Testing', 'wordpress-starter' );

		parent::sections();

		self::$sections = apply_filters( 'wps_sections', self::$sections );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function settings() {
		// General
		self::$settings['paging'] = array(
			'title' => esc_html__( 'Enable Paging?', 'wordpress-starter' ),
			'desc' => esc_html__( 'For `[wps_widget_list]`', 'wordpress-starter' ),
			'type' => 'select',
			'choices' => array(
				'' => esc_html__( 'Disable', 'wordpress-starter' ),
				1 => esc_html__( 'Enable', 'wordpress-starter' ),
				'before' => esc_html__( 'Before wps', 'wordpress-starter' ),
				'after' => esc_html__( 'After wps', 'wordpress-starter' ),
			),
			'std' => 1,
			'widget' => 0,
		);

		// Post Type
		$desc        = __( 'URL slug-name for <a href="%1s">wps archive</a> page.', 'wordpress-starter' );
		$has_archive = wps_get_option( 'has_archive', '' );
		$site_url    = site_url( '/' . $has_archive );

		self::$settings['has_archive'] = array(
			'title' => esc_html__( 'Archive Page URL', 'wordpress-starter' ),
			'desc' => sprintf( $desc, $site_url ),
			'std' => 'wps-archive',
			'validate' => 'sanitize_title',
			'widget' => 0,
		);

		// Testing
		self::$settings['debug_mode'] = array(
			'section' => 'testing',
			'title' => esc_html__( 'Debug Mode?', 'wordpress-starter' ),
			'desc' => esc_html__( 'Bypass Ajax controller to handle posts_to_import directly for testing purposes.', 'wordpress-starter' ),
			'type' => 'checkbox',
			'std' => 0,
		);

		self::$settings['posts_to_import'] = array(
			'title' => esc_html__( 'Posts to Import', 'wordpress-starter' ),
			'desc' => esc_html__( "A CSV list of post ids to import, like '1,2,3'.", 'wordpress-starter' ),
			'std' => '',
			'type' => 'text',
			'section' => 'testing',
			'validate' => 'ids',
		);

		self::$settings['skip_importing_post_ids'] = array(
			'title' => esc_html__( 'Skip Importing Posts', 'wordpress-starter' ),
			'desc' => esc_html__( "A CSV list of post ids to not import, like '1,2,3'.", 'wordpress-starter' ),
			'std' => '',
			'type' => 'text',
			'section' => 'testing',
			'validate' => 'ids',
		);

		self::$settings['limit'] = array(
			'title' => esc_html__( 'Import Limit', 'wordpress-starter' ),
			'desc' => esc_html__( 'Useful for testing import on a limited amount of posts. 0 or blank means unlimited.', 'wordpress-starter' ),
			'std' => '',
			'type' => 'text',
			'section' => 'testing',
			'validate' => 'intval',
		);

		parent::settings();

		self::$settings = apply_filters( 'wps_settings', self::$settings );

		foreach ( self::$settings as $id => $parts )
			self::$settings[ $id ] = wp_parse_args( $parts, self::$default );
	}


	public static function get_defaults( $mode = null, $old_version = null ) {
		$old_version = wps_get_option( 'version' );

		return parent::get_defaults( $mode, $old_version );
	}


	public static function display_page( $disable_donate = false ) {
		$disable_donate = wps_get_option( 'disable_donate' );

		parent::display_page( $disable_donate );
	}


	public static function initialize_settings( $version = null ) {
		$version = wps_get_option( 'version', self::$version );

		parent::initialize_settings( $version );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public static function validate_settings( $input, $options = null, $do_errors = false ) {
		$validated = parent::validate_settings( $input, $options, $do_errors );

		if ( empty( $do_errors ) )
			$input = $validated;
		else {
			$input  = $validated['input'];
			$errors = $validated['errors'];
		}

		$input['version']        = self::$version;
		$input['donate_version'] = WordPress_Starter::VERSION;

		$input = apply_filters( 'wps_validate_settings', $input, $errors );
		if ( empty( $do_errors ) )
			$validated = $input;
		else {
			$validated = array(
				'input' => $input,
				'errors' => $errors,
			);
		}

		return $validated;
	}


	public static function settings_add_help_tabs() {
		$screen = get_current_screen();
		if ( self::$admin_page != $screen->id )
			return;

		$screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:', 'wordpress-starter' ) . '</strong></p><p>' .
			esc_html__( 'These WordPress Starter Settings establish the default option values for shortcodes, theme functions, and widget instances.', 'wordpress-starter' ) .
			'</p><p>' .
			sprintf(
				__( 'View the <a href="%s">WordPress Starter documentation</a>.', 'wordpress-starter' ),
				esc_url( self::$plugin_url )
			) .
			'</p>'
		);

		$screen->add_help_tab(
			array(
				'id'     => 'tw-general',
				'title'     => esc_html__( 'General', 'wordpress-starter' ),
				'content' => '<p>' . esc_html__( 'Show or hide optional fields.', 'wordpress-starter' ) . '</p>'
			)
		);

		do_action( 'wps_settings_add_help_tabs', $screen );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function display_setting( $args = array(), $do_echo = true, $input = null ) {
		$content = apply_filters( 'wps_display_setting', '', $args, $input );
		if ( empty( $content ) )
			$content = parent::display_setting( $args, false, $input );

		if ( ! $do_echo )
			return $content;

		echo $content;
	}


}


function wps_get_options() {
	$options = get_option( WordPress_Starter_Settings::ID );

	if ( false === $options ) {
		$options = WordPress_Starter_Settings::get_defaults();
		update_option( WordPress_Starter_Settings::ID, $options );
	}

	return $options;
}


function wps_get_option( $option, $default = null ) {
	$options = get_option( WordPress_Starter_Settings::ID, null );

	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return $default;
}


function wps_set_option( $option, $value = null ) {
	$options = get_option( WordPress_Starter_Settings::ID );

	if ( ! is_array( $options ) )
		$options = array();

	$options[$option] = $value;
	update_option( WordPress_Starter_Settings::ID, $options );
}


?>
