<?php
/**
 * Secure Login With Google
 *
 * @package           SecureLoginOAuth
 * @author            plsankar <me@lakshmisankar.com>
 * @copyright         2023 plsankar. https://github.com/plsankar
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Secure Login With Google
 * Plugin URI:        https://wordpress.org/plugins/secure-login-with-oauth/
 * Description:       Seamlessly integrate Google OAuth for secure, hassle-free user access. Elevate your site's authentication standards effortlessly with this robust, user-friendly plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            plsankar
 * Author URI:        https://lakshmisankar.com
 * Text Domain:       SecureLoginOAuth
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SecureLoginOAuth\Context;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

/**
 * Initiates the context for the plugin.
 */
Context::init( __FILE__ );

google_auth_wp()->init();
