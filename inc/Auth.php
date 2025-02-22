<?php
/**
 * Class Auth
 *
 * @package SecureLoginOAuth\Auth
 */

namespace SecureLoginOAuth;

use Google;
use Google\Client;

/**
 * Class representing the auth for the plugin.
 *
 * @since 1.0.0
 */
final class Auth {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var Auth|null
	 */
	private static ?Auth $instance = null;

	/**
	 * Nonces of used in the auth process.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private array $nonces = array(
		'callback' => 'gauthwp-ajax-callback',
		'login'    => 'gauthwp-ajax-login',
	);

	/**
	 * Instance of the Google client.
	 *
	 * @since 1.0.0
	 * @var Client
	 */
	private ?Client $google_client = null;

	/**
	 * Create an instance of the context class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Get or create instance of the class.
	 *
	 * @since 1.0.0
	 * @return Auth
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Auth();
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
		add_action( 'wp_ajax_nopriv_gauthwp_callback', array( self::$instance, 'ajax_google_callback' ) );
		add_action( 'wp_ajax_gauthwp_callback', array( self::$instance, 'ajax_google_callback' ) );

		add_action( 'wp_ajax_nopriv_gauthwp_login', array( self::$instance, 'ajax_google_login' ) );
		add_action( 'wp_ajax_gauthwp_login', array( self::$instance, 'ajax_google_login' ) );
	}

	/**
	 * Redirect to Google OAuth login page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_google_login() {
		if ( false === Settings::get_instance()->get_option( 'enabled' ) ) {
			wp_die(
				esc_html__( 'Google login is disabled.', 'gauthwp' )
			);
			exit;
		}

		if (
			empty( Settings::get_instance()->get_option( 'client_id' ) ) ||
			empty( Settings::get_instance()->get_option( 'client_secret' ) )
		) {
			wp_die(
				esc_html__( 'Google login is not configured.', 'gauthwp' )
			);
			exit;
		}

		$client = $this->get_google_client();
		$state  = array(
			'nonce' => wp_create_nonce( $this->nonces['callback'] ),
		);

        // phpcs:ignore
		if ( isset( $_GET['redirect_url'] ) || empty( $_GET['redirect_url'] ) ) {
            // phpcs:ignore
            $state['redirect_url'] = $_GET['redirect_url'];
		}

		$client->setState(
			wp_json_encode(
				$state
			)
		);

		$auth_url = $client->createAuthUrl();

        // phpcs:ignore
        wp_redirect(wp_sanitize_redirect($auth_url));
		exit;
	}

	/**
	 * Callback that handles the OAuth code.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_google_callback() {

		if ( ! ( isset( $_GET['code'] ) && ! empty( $_GET['code'] ) ) ) {
			wp_die(
				esc_html(
					gauthwp_is_debug()
					? 'Outh code is missing from the request'
					: __( 'You can not access this page directly.', 'gauthwp' )
				)
			);
			exit;
		}

		if ( ! ( isset( $_GET['state'] ) && ! empty( $_GET['state'] ) ) ) {
			wp_die(
				esc_html(
					gauthwp_is_debug()
					? 'Outh state is missing from the request'
					: __( 'You can not access this page directly.', 'gauthwp' )
				)
			);
			exit;
		}

        // phpcs:ignore
        $state = json_decode(stripslashes($_GET['state']), JSON_FORCE_OBJECT);

		if ( ( ! isset( $state['nonce'] ) || ( ! wp_verify_nonce( $state['nonce'], $this->nonces['callback'] ) ) ) ) {
			wp_die(
				esc_html(
					gauthwp_is_debug()
					? 'Nonce verfification failed.'
					: __( 'You can not access this page directly.', 'gauthwp' )
				)
			);
			exit;
		}

		$code = $_GET['code'];

		$client = $this->get_google_client();

		$token = $client->fetchAccessTokenWithAuthCode( $code );

		if ( ! $token || isset( $token['error'] ) ) {
			wp_die( esc_html( $token['error_description'] ) );
			exit;
		}

		$data = null;

		try {
			$client->setAccessToken( $token );
			$data = $client->verifyIdToken();
		} catch ( \Throwable $th ) {
			wp_die( 'Oops!, an unknown error occured.' );
			exit;
		}

		if ( ! email_exists( $data['email'] ) ) {
			$user_data = array(
				'user_login'   => $data['email'],
				'user_pass'    => wp_generate_password(),
				'user_email'   => $data['email'],
				'display_name' => $data['name'],
				'first_name'   => $data['given_name'],
				'last_name'    => $data['family_name'],
			);

			$new_user_id = wp_insert_user(
				wp_slash( $user_data )
			);

			if ( is_wp_error( $new_user_id ) ) {
				wp_die( esc_html( $new_user_id['errors'][0] ) );
				exit;
			}

			wp_new_user_notification( $new_user_id );
		}

		$user = get_user_by( 'email', $data['email'] );
		if ( ! $user ) {
			return;
		}

		do_action( 'wp_login', $user->user_login, $user->user_email );
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );

		$url = home_url();

		if ( isset( $state['redirect_url'] ) ) {
			$url = $state['redirect_url'];
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Creates an instace of the google client.
	 *
	 * @since 1.0.0
	 * @return Google\Client
	 */
	public function get_google_client() {
		if ( null !== $this->google_client ) {
			return $this->google_client;
		}
		$google_client = new Google\Client();
		$google_client->setClientId( Settings::get_instance()->get_option( 'client_id' ) );
		$google_client->setClientSecret( Settings::get_instance()->get_option( 'client_secret' ) );
		$google_client->addScope( 'https://www.googleapis.com/auth/userinfo.email' );
		$google_client->addScope( 'https://www.googleapis.com/auth/userinfo.profile' );
		$google_client->setRedirectUri( admin_url( 'admin-ajax.php' ) . '?action=gauthwp_callback' );
		return $google_client;
	}
}
