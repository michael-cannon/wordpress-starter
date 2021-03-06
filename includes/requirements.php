<?php
/**
WordPress Starter
Copyright (C) 2015 Axelerant

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once WPS_DIR_LIB . 'aihrus-framework/aihrus-framework.php';


function wps_requirements_check( $force_check = false ) {
	$check_okay = get_transient( 'wps_requirements_check' );
	if ( empty( $force_check ) && false !== $check_okay ) {
		return $check_okay;
	}

	$deactivate_reason = false;
	if ( ! function_exists( 'aihr_check_aihrus_framework' ) ) {
		$deactivate_reason = esc_html__( 'Missing Aihrus Framework' );
		add_action( 'admin_notices', 'wps_notice_aihrus' );
	} elseif ( ! aihr_check_aihrus_framework( WPS_BASE, WPS_NAME, WPS_AIHR_VERSION ) ) {
		$deactivate_reason = esc_html__( 'Old Aihrus Framework version detected' );
	}

	if ( ! aihr_check_php( WPS_BASE, WPS_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old PHP version detected' );
	}

	if ( ! aihr_check_wp( WPS_BASE, WPS_NAME ) ) {
		$deactivate_reason = esc_html__( 'Old WordPress version detected' );
	}

	if ( ! empty( $deactivate_reason ) ) {
		aihr_deactivate_plugin( WPS_BASE, WPS_NAME, $deactivate_reason );
	}

	$check_okay = empty( $deactivate_reason );
	if ( $check_okay ) {
		delete_transient( 'wps_requirements_check' );
		set_transient( 'wps_requirements_check', $check_okay, HOUR_IN_SECONDS );
	}

	return $check_okay;
}


function wps_notice_aihrus() {
	$help_url  = esc_url( 'https://nodedesk.zendesk.com/hc/en-us/articles/202381391' );
	$help_link = sprintf( __( '<a href="%1$s">Update plugins</a>. <a href="%2$s">More information</a>.' ), self_admin_url( 'update-core.php' ), $help_url );

	$text = sprintf( esc_html__( 'Plugin "%1$s" has been deactivated as it requires a current Aihrus Framework. Once corrected, "%1$s" can be activated. %2$s' ), WPS_NAME, $help_link );

	aihr_notice_error( $text );
}

?>
