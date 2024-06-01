<?php
/**
 * WooCommerce Add Ribbon to Product plugin
 *
 * @package              kagg/wc-ribbon
 * @author               Ivan Ovsyannikov, KAGG Design
 * @license              GPL-2.0-or-later
 * @wordpress-plugin
 *
 * Plugin Name:          WooCommerce Add Ribbon to Product
 * Plugin URI:           https://kagg.eu/en/
 * Description:          WooCommerce Add Ribbon to Product
 * Version:              1.3.0
 * Requires at least:    4.4
 * Requires PHP:         7.4
 * Author:               Ivan Ovsyannikov, KAGG Design
 * Author URI:           https://kagg.eu/en/
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          kagg-wc-ribbon
 * Domain Path:          /languages/
 */

use KAGG\WCRibbon\Main;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

/**
 * Plugin version.
 */
const KAGG_WC_RIBBON_VERSION = '1.3.0';

/**
 * Path to the plugin dir.
 */
const KAGG_WC_RIBBON_PATH = __DIR__;

/**
 * Plugin dir url.
 */
define( 'KAGG_WC_RIBBON_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Main plugin file.
 */
const KAGG_WC_RIBBON_FILE = __FILE__;

/**
 * Get plugin main class.
 *
 * @return Main
 */
function kagg_wc_ribbon(): Main {
	require_once KAGG_WC_RIBBON_PATH . '/vendor/autoload.php';

	return Main::get_instance();
}

kagg_wc_ribbon();
