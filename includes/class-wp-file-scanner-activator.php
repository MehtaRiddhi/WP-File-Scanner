<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://github.com/MehtaRiddhi/
 * @since      1.0.0
 *
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/includes
 * @author     Riddhi <rm.mehta.04@gmail.com>
 */
class Wp_File_Scanner_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

			/**
			 * Create the custom database table on activation
			 *
			 * This function creates the custom database table for the file scanner plugin
			 * when the plugin is activated.
			 */
		
		    // Get the WordPress database object
		    global $wpdb;

		    // Define the table name for the file scanner data
		    $table_name = $wpdb->prefix. 'file_scanner';

		    // Define the SQL query to create the table
		    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		        `id` int(11) NOT NULL AUTO_INCREMENT,
		        `type` varchar(10) NOT NULL,
		        `size` varchar(20) NOT NULL,
		        `nodes` int(11) NOT NULL,
		        `absolute_path` varchar(255) NOT NULL,
		        `name` varchar(255) NOT NULL,
		        `extension` varchar(10) NOT NULL,
		        `permissions` varchar(10) NOT NULL,
		        PRIMARY KEY (`id`)
		    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		    // Include the WordPress upgrade script to execute the SQL query
		    require_once(ABSPATH. 'wp-admin/includes/upgrade.php');

		    // Execute the SQL query to create the table
		    dbDelta($sql);
		

	}

}
