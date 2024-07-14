<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://github.com/MehtaRiddhi/
 * @since      1.0.0
 *
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/admin/partials
 */

// Get the paginated files using the wpfs_admin_page function
$files = wpfs_admin_page();

// Display the admin page content
?>
<div class="wrap">
    <h1><?php _e('File Scanner Admin Page', 'wp-file-scanner'); ?></h1>
    <table>
        <thead>
            <tr>
                <th><?php _e('File Name', 'wp-file-scanner'); ?></th>
                <th><?php _e('File Size', 'wp-file-scanner'); ?></th>
                <th><?php _e('File Type', 'wp-file-scanner'); ?></th>
                <th><?php _e('Absolute Path', 'wp-file-scanner'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file) {?>
                <tr>
                    <td><?php echo esc_html($file['name']); ?></td>
                    <td><?php echo esc_html(size_format($file['size'])); ?></td>
                    <td><?php echo esc_html($file['type']); ?></td>
                    <td><a href="<?php echo esc_url($file['absolute_path']); ?>" target="_blank"><?php echo esc_html($file['absolute_path']); ?></a></td>
                </tr>
            <?php }?>
        </tbody>
    </table>

    <!-- Display pagination links -->
    <?php if ($files['pagination']['prev']) {?>
        <a href="<?php echo esc_url($files['pagination']['prev']); ?>"><?php _e('Previous', 'wp-file-scanner'); ?></a>
    <?php }?>
    <?php if ($files['pagination']['next']) {?>
        <a href="<?php echo esc_url($files['pagination']['next']); ?>"><?php _e('Next', 'wp-file-scanner'); ?></a>
    <?php }?>
</div>

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
