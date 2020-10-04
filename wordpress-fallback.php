<?php

/**
 * Wordpress fallback
 *
 * @link              https://github.com/MaximVanhove
 * @since             1.0.0
 * @package           Wordpress_fallback
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress fallback
 * Plugin URI:        https://github.com/MaximVanhove/WordpressFallback
 * Description:       Load assets from your production environment
 * Version:           1.0.0
 * Author:            Maxim Vanhove
 * Author URI:        https://github.com/MaximVanhove
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-fallback
 * Domain Path:       /languages
 * Requires PHP:      7.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Autoload
 */
require __DIR__.'/vendor/autoload.php';

/**
 * Require plugin
 */
require_once 'includes/wordpress-fallback-plugin.php';

/**
 * Create plugin
 */
$plugin_file_name = plugin_basename(__FILE__);
WordpressfallbackPlugin::create($plugin_file_name);
