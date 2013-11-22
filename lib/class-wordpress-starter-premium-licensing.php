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

require_once WPSP_PLUGIN_DIR_LIB . '/aihrus/class-aihrus-licensing.php';


class WordPress_Starter_Premium_Licensing extends Aihrus_Licensing{
	public function __construct() {
		parent::__construct( WordPress_Starter_Premium::SLUG, WordPress_Starter_Premium::ITEM_NAME );

		add_filter( 'wps_settings', array( $this, 'settings' ), 5 );
	}


	public function settings( $settings ) {
		$settings['license_key'] = array(
			'section' => 'premium',
			'title' => esc_html__( 'License Key' ),
			'desc' => esc_html__( 'Required to enable premium plugin updating. Activation is automatic. Use `0` to deactivate.' ),
			'validate' => 'wpsp_update_license',
			'widget' => 0,
		);

		return $settings;
	}


}


function wpsp_update_license( $license ) {
	global $WPSP_Licensing;

	$result = $WPSP_Licensing->update_license( $license );
	if ( 32 !== strlen( $result ) )
		WordPress_Starter_Premium::set_notice( 'notice_license' );

	return $result;
}


?>
