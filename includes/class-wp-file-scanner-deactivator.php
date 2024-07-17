<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://https://github.com/MehtaRiddhi/
 * @since      1.0.0
 *
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/includes
 * @author     Riddhi <rm.mehta.04@gmail.com>
 */
class Wp_File_Scanner_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		/**
		 * Drops the custom database table on deactivation
		 *
		 * This function is called when the plugin is deactivated, and it is responsible
		 * for dropping the custom database table created by the `wpfs_create_table`
		 * function.
		 */
		
		  // Get the global WordPress database object
		  global $wpdb;

		  // Define the name of the custom database table
		  $table_name = $wpdb->prefix. 'file_scanner';

		  // Execute a SQL query to drop the custom database table
		  // The `IF EXISTS` clause ensures that the table is only dropped if it exists
		  $wpdb->query("DROP TABLE IF EXISTS $table_name");

	}

}
