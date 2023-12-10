<?php
/**
 * Main plugin class.
 *
 * @package GoogleAuthForWP\Plugin
 */

namespace GoogleAuthForWP;

/**
 * Class representing the main plugin.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Instance of this plugin.
	 *
	 * @since 1.0.0
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Create an instance of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Get or create instance of the plugin.
	 *
	 * @since 1.0.0
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Plugin();
		}
		return self::$instance;
	}

	/**
	 * Initates the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		$this->register();
		Settings::get_instance()->init();
		Auth::get_instance()->init();
	}

	/**
	 * Register all the hooks and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ), 10, 1 );
		}

		if ( Settings::get_instance()->get_option( 'show_on_login' ) ) {
			add_action( 'login_enqueue_scripts', array( self::$instance, 'login_page' ), 1 );
		}
	}

	/**
	 * Register scripts for the login page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function login_page() {
		wp_enqueue_script( 'gauthwp-login-google' );
		wp_enqueue_style( 'gauthwp-login-google' );

		wp_localize_script(
			'gauthwp-login-google',
			'gauthwp_login_google',
			array(
				'args' => array(
					'pluginurl' => Context::get_instance()->plugin_dir_url(),
					'authUrl'   => admin_url( 'admin-ajax.php' ) . '?action=gauthwp_login',
				),
			)
		);
	}

	/**
	 * Register admin menu items on `register_admin_menu` hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_admin_menu() {
		add_menu_page(
			__( 'Google Authetication', 'gauthwp' ),
			__( 'Google Authetication', 'gauthwp' ),
			'manage_options',
			'gauthwp',
			array( $this, 'render_admin' )
		);
	}

	/**
	 * Render admin page content on `menu` callback.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_admin() {
		echo '<div id="gauthwp-admin"></div>';
	}

	/**
	 * Enqueques scripts and styles needed for the admin page.
	 *
	 * @since 1.0.0
	 * @param string $hook The name of the admin page.
	 * @return void
	 */
	public function enqueue_admin_scripts_and_styles( $hook ) {
		if ( 'toplevel_page_gauthwp' !== $hook ) {
			return;
		}
		wp_enqueue_script( 'gauthwp-admin' );
		wp_enqueue_style( 'gauthwp-admin' );
	}

	/**
	 * Loads plugin textdomain on `init` hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'gauthwp', false, Context::get_instance()->plugin_dir( '/languages' ) );
	}
}
