<div class="wrap">
    <h1><?php echo dokmeplus_t('plugin_name'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=dokmeplus_add') ?>" class="button-primary"><?php echo dokmeplus_t('add_button'); ?></a>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php echo dokmeplus_t('title'); ?></th>
                <th><?php echo dokmeplus_t('shortcode'); ?></th>
                <th><?php echo dokmeplus_t('actions'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $all = get_option('dokmeplus_buttons', []);
        if ($all) {
            foreach ($all as $id => $btn) {
                echo '<tr>';
                echo '<td>' . esc_html($btn['title']) . '</td>';
                echo '<td>[dokmeplus id="' . esc_attr($id) . '"]</td>';
                echo '<td><a href="' . admin_url('admin.php?page=dokmeplus_add&edit_id=' . $id) . '">' . dokmeplus_t('edit') . '</a> | <a href="' . admin_url('admin.php?page=dokmeplus&delete_id=' . $id) . '" onclick="return confirm(\'' . dokmeplus_t('confirm_delete') . '\')">' . dokmeplus_t('delete') . '</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">' . dokmeplus_t('no_buttons') . '</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
