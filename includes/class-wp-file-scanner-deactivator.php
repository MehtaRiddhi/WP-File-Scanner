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

		wpfs_drop_table();

	}

}
