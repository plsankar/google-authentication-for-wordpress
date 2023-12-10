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
 * @access private
 * @ignore
 */
final class Plugin {


	/**
	 * Instance of this plugin.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Context of the plugin
	 *
	 * @var Context
	 */
	public Context $context;

	/**
	 * Get or create nstance of the plugin.
	 *
	 * @param Context $context Context of the plugin.
	 */
	public function __construct( Context $context = null ) {
		$this->context = $context;
	}

	/**
	 * Get or create instance of the plugin.
	 *
	 * @param string $plugin_file Main file of the plugin.
	 * @return Plugin
	 */
	public static function get_instance( string $plugin_file ) {
		if ( null === self::$instance ) {
			$context        = new Context( $plugin_file );
			self::$instance = new Plugin( $context );
		}
		return self::$instance;
	}

	/**
	 * Initates the plugin.
	 *
	 * @return void
	 */
	public function init() {
		$this->register();
		Settings::get_instance( $this->context )->init();
		Auth::get_instance()->init();
	}

	/**
	 * Register all the hooks and filters.
	 *
	 * @return void
	 */
	public function register() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ), 10, 1 );
		}
	}

	/**
	 * Register admin menu items an `register_admin_menu` hook.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_menu_page(
			'Secure Login With Google',
			'Secure Login With Google',
			'manage_options',
			'slwg',
			array( $this, 'render_admin' )
		);
	}

	/**
	 * Render admin html content on `menu` callback.
	 *
	 * @return void
	 */
	public function render_admin() {
		echo '<div id="slwg-admin"></div>';
	}

	/**
	 * Enqueques scripts and styles needed for the admin page.
	 *
	 * @param string $hook The name of the admin page.
	 * @return void
	 */
	public function enqueue_admin_scripts_and_styles( $hook ) {
		if ( 'toplevel_page_slwg' !== $hook ) {
			return;
		}
		wp_enqueue_script( 'slwg-admin' );
		wp_enqueue_style( 'slwg-admin' );
	}

	/**
	 * Loads plugin textdomain on `init` hook.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'slwg', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}