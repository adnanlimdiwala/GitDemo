<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Mdsupport
 * @subpackage Mdsupport/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mdsupport
 * @subpackage Mdsupport/includes
 * @author     multidots <info@multidots.com>
 */
class Mdsupport_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        add_role( 'support_role', 'Supporter', array(
            'read' => true, // true allows this capability
            'edit_posts' => true, // Allows user to edit their own posts
            'edit_pages' => true, // Allows user to edit pages
            'edit_others_posts' => true, // Allows user to edit others posts not just their own
            'edit_published_posts' => true,
            'create_posts' => true, // Allows user to create new posts
            'manage_categories' => true, // Allows user to manage post categories
            'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
            'edit_themes' => false, // false denies this capability. User can’t edit your theme
            'install_plugins' => false, // User cant add new plugins
            'update_plugin' => false, // User can’t update any plugins
            'update_core' => false, // user cant perform core updates
            'level_9' => true,
            'level_1' => true,
        ) );
	}

}
