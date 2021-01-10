<?php

/**
 * Fallback
 *
 * @link              https://github.com/starringjane/wordpress-fallback
 * @since             1.0.0
 * @package           Fallback
 *
 * @wordpress-plugin
 * Plugin Name:       Fallback
 * Plugin URI:        https://github.com/starringjane/wordpress-fallback
 * Description:       Load assets from your production environment
 * Version:           1.0.1
 * Author:            Starring Jane
 * Author URI:        https://github.com/starringjane
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fallback
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
require_once 'includes/WLAFP_Plugin.php';

/**
 * Create plugin
 */
$plugin_file_name = plugin_basename(__FILE__);
WLAFP_Plugin::create($plugin_file_name);
