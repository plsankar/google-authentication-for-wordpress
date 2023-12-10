<?php
/**
 * Class Settings
 *
 * @package GoogleAuthForWP\Settings;
 */

namespace GoogleAuthForWP;

use WP_REST_Response;
use WP_REST_Server;

/**
 * Class representing the main plugin
 *
 * @since 1.0.0
 * @access private
 * @ignore
 */
final class Settings {


	/**
	 * Instance of this plugin.
	 *
	 * @var GoogleAuthForWP|null
	 */
	private static ?Settings $instance = null;

	/**
	 * Get or create nstance of the plugin.
	 */
	public function __construct() {
	}

	/**
	 * Get or create instance of the Settings class.
	 *
	 * @return Settings
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Settings();
		}
		return self::$instance;
	}

	/**
	 * Initates the class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Register all the hooks and filters.
	 *
	 * @return void
	 */
	public function register() {
		register_rest_route(
			'slwg/v1',
			'/settings',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'save' ),
				'permission_callback' => 'slwg_rest_permission_callback',
				'args'                => array(
					'slwg_google_enabled'       => array(
						'type'              => 'boolean',
						'validate_callback' => 'slwg_rest_validate_callback_boolean',
						'sanitize_callback' => 'rest_sanitize_boolean',
					),
					'slwg_google_client_id'     => array(
						'type'              => 'string',
						'validate_callback' => 'slwg_rest_validate_callback_string',
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'slwg_google_client_secret' => array(
						'type'              => 'string',
						'validate_callback' => 'slwg_rest_validate_callback_string',
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'slwg_google_show_on_login' => array(
						'type'              => 'boolean',
						'validate_callback' => 'slwg_rest_validate_callback_boolean',
						'sanitize_callback' => 'rest_sanitize_boolean',
					),
				),
			)
		);

		register_rest_route(
			'slwg/v1',
			'/settings',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get' ),
				'permission_callback' => 'slwg_rest_permission_callback',
			)
		);
	}

	/**
	 * Handles the `get` request for the `v1/settings` rest route.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get() {
		return rest_ensure_response(
			new WP_REST_Response(
				$this->get_options()
			)
		);
	}

	/**
	 * Handles the `put` request for the `v1/settings` rest route.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function save( \WP_REST_Request $request ) {
		$params = wp_parse_args( $request->get_params(), $this->get_options() );

		update_option( 'slwg_google_enabled', boolval( $params['slwg_google_enabled'] ) );
		update_option( 'slwg_google_client_id', $params['slwg_google_client_id'] );
		update_option( 'slwg_google_client_secret', $params['slwg_google_client_secret'] );
		update_option( 'slwg_google_show_on_login', boolval( $params['slwg_google_show_on_login'] ) );

		return rest_ensure_response(
			new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $params,
				)
			)
		);
	}

	/**
	 * Get all options for the plugin.
	 *
	 * @return array
	 */
	public function get_options() {
		return array(
			'slwg_google_enabled'       => boolval( get_option( 'slwg_google_enabled', false ) ),
			'slwg_google_client_id'     => get_option( 'slwg_google_client_id', '' ),
			'slwg_google_client_secret' => get_option( 'slwg_google_client_secret', '' ),
			'slwg_google_show_on_login' => boolval( get_option( 'slwg_google_show_on_login', true ) ),
		);
	}

	/**
	 * Get the option for the given key.
	 *
	 * @param string $key The key for the option.
	 * @return mixed
	 */
	public function get_option( $key ) {
		return $this->get_options()[ "slwg_$key" ];
	}
}