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
 * Class representing the settings for the plugin
 *
 * @since 1.0.0
 */
final class Settings {

	/**
	 * Instance of this plugin.
	 *
	 * @since 1.0.0
	 * @var Settings|null
	 */
	private static ?Settings $instance = null;

	/**
	 * Create an instance of the Settings class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Get or create instance of the Settings class.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		$this->register();
	}

	/**
	 * Register all the hooks and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_rest' ) );
	}

	/**
	 * Register all the hooks and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_rest() {
		register_rest_route(
			'gauthwp/v1',
			'/settings',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'save' ),
				'permission_callback' => 'gauthwp_rest_permission_callback',
				'args'                => array(
					'gauthwp_google_enabled'       => array(
						'type'              => 'boolean',
						'validate_callback' => 'gauthwp_rest_validate_callback_boolean',
						'sanitize_callback' => 'rest_sanitize_boolean',
					),
					'gauthwp_google_client_id'     => array(
						'type'              => 'string',
						'validate_callback' => 'gauthwp_rest_validate_callback_string',
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'gauthwp_google_client_secret' => array(
						'type'              => 'string',
						'validate_callback' => 'gauthwp_rest_validate_callback_string',
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'gauthwp_google_show_on_login' => array(
						'type'              => 'boolean',
						'validate_callback' => 'gauthwp_rest_validate_callback_boolean',
						'sanitize_callback' => 'rest_sanitize_boolean',
					),
				),
			)
		);

		register_rest_route(
			'gauthwp/v1',
			'/settings',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get' ),
				'permission_callback' => 'gauthwp_rest_permission_callback',
			)
		);
	}

	/**
	 * Handles the `get` request for the `v1/settings` rest route.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function save( \WP_REST_Request $request ) {
		$params = wp_parse_args( $request->get_params(), $this->get_options() );

		update_option( 'gauthwp_google_enabled', boolval( $params['gauthwp_google_enabled'] ) );
		update_option( 'gauthwp_google_client_id', $params['gauthwp_google_client_id'] );
		update_option( 'gauthwp_google_client_secret', $params['gauthwp_google_client_secret'] );
		update_option( 'gauthwp_google_show_on_login', boolval( $params['gauthwp_google_show_on_login'] ) );

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
	 * @since 1.0.0
	 * @return array
	 */
	public function get_options() {
		return array(
			'gauthwp_google_enabled'       => boolval( get_option( 'gauthwp_google_enabled', false ) ),
			'gauthwp_google_client_id'     => get_option( 'gauthwp_google_client_id', '' ),
			'gauthwp_google_client_secret' => get_option( 'gauthwp_google_client_secret', '' ),
			'gauthwp_google_show_on_login' => boolval( get_option( 'gauthwp_google_show_on_login', true ) ),
		);
	}

	/**
	 * Get the option for the given key.
	 *
	 * @since 1.0.0
	 * @param string $key The key for the option.
	 * @return mixed
	 */
	public function get_option( $key ) {
		return $this->get_options()[ "gauthwp_$key" ];
	}
}
