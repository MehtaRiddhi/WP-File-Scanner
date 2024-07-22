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
	 * load init.
	 */
	public function wpfs_admin_init() {
		global $wpdb;

		/**
     	* Handle Scan Now button click
     	*/
		$scan_now   = filter_input( INPUT_POST, 'scan_now', FILTER_SANITIZE_STRING );
		$scan_nonce = filter_input( INPUT_POST, 'scan_nonce', FILTER_SANITIZE_STRING );

		if ( isset( $scan_now ) && isset( $scan_nonce ) && wp_verify_nonce( $scan_nonce, 'scan_action' ) ) {

			// Clear previous scan results
			$wpdb->query( $wpdb->prepare( "TRUNCATE TABLE " . $wpdb->prefix . 'file_scanner' ) );

			// Scan the root directory
			$scan_results = $this->scan_directory( ABSPATH );

			// Insert new scan results
			foreach ( $scan_results as $result ) {
				$wpdb->insert(
					$wpdb->prefix . 'file_scanner',
					array(
						'type'        => $result['type'],
						'size'        => $result['size'],
						'nodes'       => $result['nodes'],
						'path'        => $result['path'],
						'name'        => $result['name'],
						'extension'   => $result['extension'],
						'permissions' => $result['permissions']
					),
					array(
						'%s', // Type
						'%s', // Size
						'%d', // Nodes
						'%s', // Path
						'%s', // Name
						'%s', // Extension
						'%s'  // Permissions
					)
				);
			}

			// Clear cache for refreshed data
			wp_cache_delete( 'file_scan_results', 'file_scan_results' );
			wp_cache_delete( 'file_scan_total_items', 'file_scan_results' );

			// Redirect to avoid form resubmission
			wp_redirect( esc_url_raw( add_query_arg( 'page', 'file-scan', admin_url( 'admin.php' ) ) ) );
			exit;
		}
	}

	/**
	 /**
	 * Creates the custom admin page
	 *
	 * This function is called when the `admin_menu` hook is triggered, and it adds
	 * a new menu item to the WordPress admin menu.
	 */
	public function wpfs_admin_menu() {

		add_menu_page(
            __( 'WP File Scanner', 'wp-file-scanner' ),
        	__( 'WP File Scanner', 'wp-file-scanner' ),
        	'manage_options',
            'file-scan',
            array( $this, 'wpfs_admin_page' ),
			'dashicons-admin-generic',
			4
        );
	}


	/**
 	* Scans a directory and its subdirectories recursively.
	 *
	 * @param string $dir The directory to scan.
	 * @param array  $results The results array to store the scan results.
	 *
	 * @return array The scan results.
	 */
	public function scan_directory_sub( $dir, &$results = array() ) {
	    $files = scandir( $dir );

	    foreach ( $files as $value ) {
	        $path = realpath( $dir . DIRECTORY_SEPARATOR . $value );

	        if ( ! is_dir( $path ) ) {
	            $results[] = array(
	                'type'        => 'file',
	                'size'        => $this->wpfs_format_size( filesize( $path ) ),
	                'nodes'       => 0,
	                'path'        => $path,
	                'name'        => basename( $path ),
	                'extension'   => pathinfo( $path, PATHINFO_EXTENSION ),
	                'permissions' => substr( sprintf( '%o', fileperms( $path ) ), - 4 )
	            );
	        } elseif ( $value !== "." && $value !== ".." ) {
	            $subDirNodes   = 0;
	            $subDirResults = array();
	            $this->scan_directory_sub( $path, $subDirResults );
	            foreach ( $subDirResults as $res ) {
	                $subDirNodes += $res['nodes'];
	            }
	            $results[] = array(
	                'type'        => 'directory',
	                'size'        => '',
	                'nodes'       => $subDirNodes,
	                'path'        => $path,
	                'name'        => basename( $path ),
	                'extension'   => '',
	                'permissions' => substr( sprintf( '%o', fileperms( $path ) ), - 4 )
	            );
	            $results   = array_merge( $results, $subDirResults );
	        }
	    }

	    return $results;
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
	 * Scans a list of directories and their subdirectories recursively.
	 *
	 * @param string $dir The directory to scan.
	 * @param array  $results The results array to store the scan results.
	 *
	 * @return array The scan results.
	*/
	public function scan_directory( $dir, &$results = array() ) {

		$directories = array(
		    ABSPATH . 'wp-admin',
		    ABSPATH . 'wp-content',
		    //ABSPATH . 'wp-includes'
		);

		foreach ( $directories as $dir ) {
        	$files = scandir( $dir );

	        foreach ( $files as $value ) {
	            $path = realpath( $dir . DIRECTORY_SEPARATOR . $value );

	            if ( ! is_dir( $path ) ) {
	                $results[] = array(
	                    'type'        => 'file',
	                    'size'        => $this->wpfs_format_size( filesize( $path ) ),
	                    'nodes'       => 0,
	                    'path'        => $path,
	                    'name'        => basename( $path ),
	                    'extension'   => pathinfo( $path, PATHINFO_EXTENSION ),
	                    'permissions' => substr( sprintf( '%o', fileperms( $path ) ), - 4 )
	                );
	            } elseif ( $value !== "." && $value !== ".." ) {
	                $subDirNodes   = 0;
	                $subDirResults = array();
	                $this->scan_directory_sub( $path, $subDirResults );
	                foreach ( $subDirResults as $res ) {
	                    $subDirNodes += $res['nodes'];
	                }
	                $results[] = array(
	                    'type'        => 'directory',
	                    'size'        => '',
	                    'nodes'       => $subDirNodes,
	                    'path'        => $path,
	                    'name'        => basename( $path ),
	                    'extension'   => '',
	                    'permissions' => substr( sprintf( '%o', fileperms( $path ) ), - 4 )
	                );
	                $results   = array_merge( $results, $subDirResults );
	            }
	        }
    	}

    	return $results;
	}


	

	/**
	 * Display admin page
	 *
	 * @since 1.0.0
	 */
	public function wpfs_admin_page() {
		global $wpdb;

		// Fetch results for display with pagination
		$items_per_page = 10;
		$paged          = filter_input( INPUT_GET, 'paged', FILTER_VALIDATE_INT );
		$paged          = $paged ? $paged : 1;
		$offset         = ( $paged - 1 ) * $items_per_page;

		$total_items = wp_cache_get( 'file_scan_total_items', 'file_scan_results' );

		if ( false === $total_items ) {
			$total_items = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . $wpdb->prefix . 'file_scanner' ) );
			wp_cache_set( 'file_scan_total_items', $total_items, 'file_scan_results' );
		}

		$results = wp_cache_get( 'file_scan_results', 'file_scan_results' );

		if ( false === $results ) {
			$results = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}file_scanner LIMIT %d, %d", $offset, $items_per_page )
			);
			wp_cache_set( 'file_scan_results', $results, 'file_scan_results' );
		}

		$pagination_args = array(
			'total_items' => $total_items,
			'total_pages' => ceil( $total_items / $items_per_page ),
			'per_page'    => $items_per_page,
		);

		include_once plugin_dir_path( __FILE__ ) . 'partials/wp-file-scanner-admin-display.php';
	}


}