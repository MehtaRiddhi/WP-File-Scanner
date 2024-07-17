<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://github.com/MehtaRiddhi/
 * @since      1.0.0
 *
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/admin
 * @author     Riddhi <rm.mehta.04@gmail.com>
 */
class Wp_File_Scanner_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_File_Scanner_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_File_Scanner_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-file-scanner-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_File_Scanner_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_File_Scanner_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-file-scanner-admin.js', array( 'jquery' ), $this->version, false );

	}


    /**
	 * Creates the custom admin page
	 *
	 * This function is called when the `admin_menu` hook is triggered, and it adds
	 * a new menu item to the WordPress admin menu.
	 */
    public function wpfs_admin_menu() {
    	// Add a new menu page to the WordPress admin menu
        add_menu_page(
            __( 'WP File Scanner', 'wp-file-scanner' ),
        	__( 'WP File Scanner', 'wp-file-scanner' ),
        	'manage_options',
            'wp-file-scanner',
            array( $this, 'wpfs_admin_page' ),
			'dashicons-admin-site',
			6
        );
    }

    /**
 	 * Admin page function
 	 *
 	 * This function generates the admin page for the file scanner plugin.
 	*/
    public function wpfs_admin_page() {
        // Render the admin page content
        include_once plugin_dir_path( __FILE__ ) . 'partials/wp-file-scanner-admin-display.php';
    }

    /**
	 * Format size function
	 *
	 * This function takes a file size in bytes as input and returns a human-readable
	 * string representing the size in a suitable unit (B, Kb, Mb, Gb, Tb).
	 *
	 * @param int $size The file size in bytes
	 * @return string The formatted file size
	 */
	public function wpfs_format_size($size) {
	    // Define an array of units to use for formatting the size
	    $units = array('B', 'Kb', 'Mb', 'Gb', 'Tb');

	    // Initialize an index to keep track of the current unit
	    $index = 0;

	    // Loop until the size is less than 1024
	    while ($size >= 1024) {
	        // Divide the size by 1024 to move to the next unit
	        $size /= 1024;
	        // Increment the index to move to the next unit
	        $index++;
	    }

	    // Return the formatted size as a string, rounded to 2 decimal places
	    return round($size, 2) . ' ' . $units[$index];
	}

	/**
	 * Scan files and directories function
	 *
	 * This function scans specific directories and their subdirectories for files and directories,
	 * and stores the results in the database.
	 *
	 * @param array $directories Optional. An array of directories to scan. Default is an empty array.
	 * @param int $limit Optional. The maximum number of files to scan. Default is 100.
	 */
	public function wpfs_scan_files($directories = array(), $limit = 100) {
	    // Get the WordPress database object
	    global $wpdb;

	    $directories = array();

	    // Define the table name for the file scanner data
	    $table_name = $wpdb->prefix. 'file_scanner';

	    // Clear the table for fresh scan results
	    $wpdb->query("DELETE FROM $table_name");

	    // Set the root directory for the scan
	    $root_dir = ABSPATH;

	    // If no directories are specified, scan only the WordPress core directories
	    if (empty($directories)) {
	        $directories = array(
	            'wp-admin',
	            'wp-content',
	            'wp-includes'
	        );
	    }

	    // Initialize an empty array to store the files
	    $files = array();

	    // Initialize a counter to keep track of the number of files scanned
	    $count = 0;

	    // Loop through the specified directories
	    foreach ($directories as $dir) {

	        // Sanitize the file path using wp_normalize_path()
            $sanitized_path = wp_normalize_path( $root_dir. '/'. $dir );

            $dir_path = ''; 
            
	        // Validate the directory path
	        if (!is_dir($dir_path)) {
	            continue; // Skip if not a directory
	        }

	        // Open the directory
	        $handle = opendir($dir_path);

	        // Loop through the files and directories in the directory
	        while (($file = readdir($handle)) !== false) {
	            // Skip the current directory (.) and parent directory (..)
	            if ($file === '.' || $file === '..') {
	                continue;
	            }

	            // Get the file path and name
	            $file_path = $dir_path. '/'. $file;
	            $file_name = $file;

	            $format_size_var = $this->wpfs_format_size(filesize($file_path));

	            // Get file information
	            $file_info = array();
	            $file_info['type'] = __('file', 'wp-file-scanner');
	            $file_info['size'] = $format_size_var;
	            $file_info['nodes'] = 0;
	            $file_info['absolute_path'] = $file_path;
	            $file_info['name'] = $file_name;
	            $file_info['extension'] = pathinfo($file_name, PATHINFO_EXTENSION);
	            $file_info['permissions'] = substr(sprintf('%o', fileperms($file_path)), -4);

	            // Sanitize the data before inserting into the database
	            $file_info['type'] = sanitize_text_field($file_info['type']);
	            $file_info['size'] = sanitize_text_field($file_info['size']);
	            $file_info['nodes'] = intval($file_info['nodes']);
	            $file_info['absolute_path'] = sanitize_text_field($file_info['absolute_path']);
	            $file_info['name'] = sanitize_text_field($file_info['name']);
	            $file_info['extension'] = sanitize_text_field($file_info['extension']);
	            $file_info['permissions'] = sanitize_text_field($file_info['permissions']);

	            // Prepare the SQL query with placeholders
	            $wpdb->insert(
	                $table_name,
	                $file_info,
	                array(
	                    '%s',
	                    '%s',
	                    '%d',
	                    '%s',
	                    '%s',
	                    '%s',
	                    '%s'
	                )
	            );

	            // Increment the file count
	            $count++;

	            // If the limit is reached, break out of the loop
	            if ($count >= $limit) {
	                break 2;
	            }
	        }

	        // Close the directory
	        closedir($handle);
	    }
	}

    /**
	 * Pagination function
	 *
	 * This function generates pagination data for the file scanner plugin.
	 *
	 * @param int $per_page The number of items to display per page.
	 * @param int $page The current page number.
	 * @return array An array containing the pagination data.
	 */
	public function wpfs_pagination($per_page, $page) {
    // Scan the files and get the total files count

		$directories = array();

	    $this->wpfs_scan_files($directories, $limit = 100);
	    
	    global $wpdb;

	    $table_name = $wpdb->prefix. 'file_scanner';
	    $total_files = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

	    // Calculate the total number of pages
	    $total_pages = ceil($total_files / $per_page);

	    // Calculate the offset for the current page
	    $offset = ($page - 1) * $per_page;

	    // Get the files for the current page
	    $files = $wpdb->get_results("SELECT * FROM $table_name LIMIT $offset, $per_page");

	    // Generate the pagination HTML
	    $pagination_html = '<div class="tablenav">';
	    $pagination_html .= '<div class="tablenav-pages">';
	    $pagination_html .= '<span class="displaying-num">'. sprintf(__('Displaying %d - %d of %d', 'wp-file-scanner'), ($page - 1) * $per_page + 1, min($page * $per_page, $total_files), $total_files). '</span>';
	    $pagination_html .= '<span class="pagination-links">';
	    $pagination_html .= '<a class="first-page" href="?page=1">'. __('«', 'wp-file-scanner'). '</a>';
	    $pagination_html .= '<a class="prev-page" href="?page='. max(1, $page - 1). '">‹</a>';
	    $pagination_html .= '<span class="paging-input">';
	    $pagination_html .= '<input class="current-page" type="text" value="'. $page. '" size="2">';
	    $pagination_html .= '<span class="total-pages">'. __('of', 'wp-file-scanner'). ' '. $total_pages. '</span>';
	    $pagination_html .= '</span>';
	    $pagination_html .= '<a class="next-page" href="?page='. min($total_pages, $page + 1). '">›</a>';
	    $pagination_html .= '<a class="last-page" href="?page='. $total_pages. '">»</a>';
	    $pagination_html .= '</span>';
	    $pagination_html .= '</div>';
	    $pagination_html .= '</div>';

	    // Return the pagination data
	    return array(
	        'files' => $files,
	        'html' => $pagination_html
	    );
	}


}
