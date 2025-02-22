<?php
/**
 * A File full of functions.
 *
 * @package SecureLoginOAuth
 */

use SecureLoginOAuth\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'google_auth_wp' ) ) {
	/**
	 * Get an instance of the plugin
	 *
	 * @return Plugin
	 */
	function google_auth_wp() {
		return Plugin::get_instance( __FILE__ );
	}
}

if ( ! function_exists( 'gauthwp_rest_permission_callback' ) ) :
	/**
	 * Checks the persmission for rest routes.
	 *
	 * @return bool
	 */
	function gauthwp_rest_permission_callback() {
		return ( is_user_logged_in() && current_user_can( 'manage_options' ) ) ? true : false;
	}
endif;

if ( ! function_exists( 'gauthwp_rest_validate_callback_boolean' ) ) {

	/**
	 * Validate `boolean` for rest routes;
	 *
	 * @param  mixed $param The input value.
	 * @return bool
	 */
	function gauthwp_rest_validate_callback_boolean( $param ) {
		return is_bool( $param );
	}
}

if ( ! function_exists( 'gauthwp_rest_validate_callback_string' ) ) {
	/**
	 * Validate `string` for rest routes;
	 *
	 * @param  mixed $param The input value.
	 * @return bool
	 */
	function gauthwp_rest_validate_callback_string( $param ) {
		return is_string( $param );
	}
}

if ( ! function_exists( 'gauthwp_is_debug' ) ) {
	/**
	 * Check if WP_DEBUG is on.
	 *
	 * @return bool
	 */
	function gauthwp_is_debug() {
		return defined( 'WP_DEBUG' ) && ( true === WP_DEBUG );
	}
}
