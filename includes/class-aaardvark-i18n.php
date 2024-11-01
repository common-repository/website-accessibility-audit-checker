<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://aaardvarkaccessibility.com
 * @since      1.0.0
 *
 * @package    AAArdvark
 * @subpackage AAArdvark/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    AAArdvark
 * @subpackage AAArdvark/includes
 * @author     N Squared <support@aaardvarkaccessibility.com>
 */
class AAArdvark_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'website-accessibility-audit-checker',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
