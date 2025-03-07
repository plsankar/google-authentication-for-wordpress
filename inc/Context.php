<?php
/**
 * Class Context
 *
 * @package SecureLoginOAuth\Context;
 */

namespace SecureLoginOAuth;

/**
 * Class representing the main context for the plugin.
 *
 * @since 1.0.0
 */
final class Context {

	/**
	 * Version of the plugin.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $version = '1.0.0';

	/**
	 * Instance of the context class.
	 *
	 * @var Context|null
	 */
	private static ?Context $instance = null;

	/**
	 * Main file of the plugin.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $plugin_file;

	/**
	 * Script tags to have the module tag.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public array $module_scripts = array( 'admin', 'vite', 'login-google' );

	/**
	 * Manifest file generated by vite.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public ?array $manifest = null;

	/**
	 * Create an instance of the context class.
	 *
	 * @since 1.0.0
	 * @param string $plugin_file Main plugin file.
	 * @throws \Exception Throws an exception if plugin file path is not given or empty.
	 */
	public function __construct( string $plugin_file ) {
		if ( null === $plugin_file || empty( $plugin_file ) ) {
			throw new \Exception( __CLASS__ . "can't be initiated without the plugin main file path" );
		}
		$this->plugin_file = $plugin_file;

		add_action( 'wp_loaded', array( $this, 'register_assets' ) );
	}

	/**
	 * Create an instance of the context class.
	 *
	 * @since 1.0.0
	 * @param string $plugin_file Main file of the plugin.
	 * @return void
	 */
	public static function init( string $plugin_file ) {
		if ( null === self::$instance ) {
			self::$instance = new Context( $plugin_file );
		}
	}

	/**
	 * Create an instance of the context class.
	 *
	 * @since 1.0.0
	 * @return Context
	 * @throws \Exception Throws an exception if plugin context is not initiated.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			throw new \Exception( __CLASS__ . "can't accces the context before initiating." );
		}
		return self::$instance;
	}

	/**
	 * Get the plugin path or the given folder/file path.
	 *
	 * @since 1.0.0
	 * @param string $path The path to a file or leave empty to get the main plugin path.
	 * @return string
	 */
	public function plugin_dir( string $path = '' ) {
		return trailingslashit( plugin_dir_path( $this->plugin_file ) . trailingslashit( $path ) );
	}

	/**
	 * Get the plugin url or the given folder/file url.
	 *
	 * @param string $path The path to a file or leave empty to get the main plugin path.
	 * @return string
	 */
	public function plugin_dir_url( string $path = '' ) {
		return plugin_dir_url( $this->plugin_file ) . trailingslashit( $path );
	}

	/**
	 * Filter function to mark script tags as modules.
	 *
	 * @param string $html The html output.
	 * @param string $handle Handle of the script.
	 * @return string
	 */
	public function module_script_loader( $html, $handle ) {
		if ( ! in_array( str_replace( 'gauthwp-', '', $handle ), $this->module_scripts, true ) ) {
			return $html;
		}
		return str_replace( '<script ', ' <script type="module"', $html );
	}

	/**
	 * Register assets for the plugin.
	 *
	 * @return void
	 */
	public function register_assets() {

		wp_register_style( 'gauthwp-fonts', 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Sora:wght@100..800&display=swap', array(), $this->version, 'all' );

		if ( 'development' === wp_get_environment_type() ) {

			wp_enqueue_style( 'gauthwp-fonts' );

			add_filter( 'script_loader_tag', array( $this, 'module_script_loader' ), 10, 2 );
            // phpcs:ignore
            wp_register_script('gauthwp-vite', 'http://localhost:5173/@vite/client', array(), null, false);
			wp_add_inline_script(
				'gauthwp-vite',
				"import RefreshRuntime from 'http://localhost:5173/@react-refresh'
                RefreshRuntime.injectIntoGlobalHook(window)
                window.\$RefreshReg$ = () => {}
                window.\$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true",
				'before'
			);

            // phpcs:ignore
            wp_register_script('gauthwp-admin', 'http://localhost:5173/src/admin/index.tsx', array('gauthwp-vite'), null, false);
			wp_localize_script(
				'gauthwp-admin',
				'gauthwp_admin',
				array(
					'adminurl'       => admin_url(),
					'pluginAdminUrl' => admin_url( 'admin.php' ),
					'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
					'rest_url'       => esc_url_raw( rest_url() ),
					'rest_nonce'     => wp_create_nonce( 'wp_rest' ),
				)
			);

            // phpcs:ignore
			wp_register_script( 'gauthwp-login-google', 'http://localhost:5173/src/google/index.ts', array( 'gauthwp-vite' ), null, true );
		} else {

			wp_enqueue_style( 'gauthwp-fonts' );

			wp_register_style( 'gauthwp-admin', $this->get_manifest_file( 'src/admin.css' ), array(), $this->version, 'all' );
			wp_register_script( 'gauthwp-admin', $this->get_manifest_file( 'src/admin.tsx' ), array(), $this->version, true );

			wp_register_style( 'gauthwp-login-google', $this->get_manifest_file( 'src/google/index.css' ), array(), $this->version, 'all' );
			wp_register_script( 'gauthwp-login-google', $this->get_manifest_file( 'src/google/index.ts' ), array(), $this->version, true );
		}
	}

	/**
	 * Fetch file from vite manifest.
	 *
	 * @param string $name Name of the file.
	 * @return string
	 */
	public function get_manifest_file( string $name ) {
		if ( ! $this->manifest ) {
			ob_start();
			include $this->plugin_dir( 'dist/manifest.json' );
			$this->manifest = json_decode( ob_get_clean(), true );
		}
		return plugin_dir_url( $this->manifest[ $name ]['file'] );
	}
}
