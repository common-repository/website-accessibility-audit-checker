<?php
/**
 * Fired during plugin deactivation.
 *
 * @see       https://aaardvarkaccessibility.com
 * @since      1.0.0
 *
 * @package    AAArdvark
 * @subpackage AAArdvark/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 *
 * @author     N Squared <support@aaardvarkaccessibility.com>
 */
class AAArdvark_Deactivator {

	/**
	 * Short Description. (use period).
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( 'aaardvark_options' );
	}
}
