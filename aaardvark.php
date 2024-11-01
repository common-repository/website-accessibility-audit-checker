<?php
/**
 *
 * @see              https://aaardvarkaccessibility.com
 * @since             1.0.0
 *
 * @package AAArdvark
 *
 * @wordpress-plugin
 * Plugin Name:       AAArdvark
 * Plugin URI:        https://aaardvarkaccessibility.com
 * Description:       Connect WordPress to the AAArdvark accessibility testing service to display statistics and reports.
 * Version:           1.1.15
 * Author:      			AAArdvark
 * Author URI:  			https://aaardvarkaccessibility.com
 * License:           GPLv2 or later.
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       website-accessibility-audit-checker
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Current version.
 *
 * @var    string
 */
const VERSION = '1.1.15';
	
/*
 * Currently plugin version.
 */
define( 'AAARDVARK_VERSION', VERSION );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aaardvark-activator.php.
 */
function aaardvark_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aaardvark-activator.php';
	AAArdvark_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aaardvark-deactivator.php.
 */
function aaardvark_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aaardvark-deactivator.php';
	AAArdvark_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'aaardvark_activate' );
register_deactivation_hook( __FILE__, 'aaardvark_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aaardvark.php';

/**
 * This plugin's url.
 *
 * @since  0.0.0
 *
 * @param  string $path (optional) appended path.
 * @return string       URL and path.
 */
function aaardvark_plugin_url( $path = '' ) {
	static $url;
	$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
	
	return $url . $path;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function aaardvark_run() {
	$plugin = new AAArdvark();
	$plugin->run();
}
aaardvark_run();
