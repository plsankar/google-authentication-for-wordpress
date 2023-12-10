<?php
/**
 * Google Authentication for WP
 *
 * @package           GoogleAuthForWP
 * @author            plsankar <me@lakshmisankar.com>
 * @copyright         2023 plsankar. https://github.com/plsankar
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Google Authentication for WP
 * Plugin URI:        https://wordpress.org/plugins/google-authentication-for-wp/
 * Description:       Seamlessly integrate Google OAuth for secure, hassle-free user access. Elevate your site's authentication standards effortlessly with this robust, user-friendly plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            plsankar
 * Author URI:        https://lakshmisankar.com
 * Text Domain:       googleauthforwp
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GoogleAuthForWP\Context;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

/**
 * Initiates the context for the plugin.
 */
Context::init( __FILE__ );

google_auth_wp()->init();
