<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Mdsupport
 * @subpackage Mdsupport/includes
 * @author     multidots <info@multidots.com>
 */
class Mdsupport_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        remove_role( 'support_role' );
    }

}
