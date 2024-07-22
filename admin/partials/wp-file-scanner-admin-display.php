<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://example.com
 * @since 1.0.0
 *
 * @package    Wp_File_Scanner
 * @subpackage Wp_File_Scanner/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$pagination_base = add_query_arg('paged', '%#%');
$pagination      = paginate_links(
    array(
        'base'      => $pagination_base,
        'format'    => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total'     => $pagination_args['total_pages'],
        'current'   => $paged
    )
);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post">
        <?php wp_nonce_field('scan_action', 'scan_nonce'); ?>
        <p>
            <input type="submit" name="scan_now" class="button button-primary" value="<?php esc_attr_e('Scan Now', 'wp-file-scanner'); ?>" />
        </p>
    </form>

    <div class="file-list">
        <table class="file-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?php esc_html_e('File Type', 'wp-file-scanner'); ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?php esc_html_e('File Size', 'wp-file-scanner'); ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?php esc_html_e('Node Count', 'wp-file-scanner'); ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?php esc_html_e('File Path', 'wp-file-scanner'); ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?php esc_html_e('File Name', 'wp-file-scanner'); ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?php esc_html_e('File Permissions', 'wp-file-scanner'); ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?php esc_html_e('File Extension', 'wp-file-scanner'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)) : ?>
                    <tr>
                        <td colspan="7" style="padding: 10px; border: 1px solid #ddd; text-align: center;"><?php esc_html_e('The list is vacant; no files or directories have been located. Initiate a scan to add items.', 'wp-file-scanner'); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($results as $result) : ?>
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($result->type); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($result->size); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($result->nodes); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($result->path); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($result->name); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($result->permissions); ?></td>
                            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo esc_html($result->extension); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="tablenav">
        <div class="tablenav-pages">
            <?php echo wp_kses_post($pagination); ?>
        </div>
    </div>
    
</div>