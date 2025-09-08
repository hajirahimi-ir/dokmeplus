<div class="wrap">
    <h1><?php echo esc_html(dokmeplus_t('list_title')); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=dokmeplus_add')) ?>" class="button-primary"><?php echo esc_html(dokmeplus_t('add_button')); ?></a>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php echo esc_html(dokmeplus_t('label_title')); ?></th>
                <th><?php echo esc_html(dokmeplus_t('shortcode')); ?></th>
                <th><?php echo esc_html(dokmeplus_t('actions')); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $all = get_option('dokmeplus_buttons', []);
        if ($all) {
            foreach ($all as $id => $btn) {
                $edit_url = esc_url( admin_url('admin.php?page=dokmeplus_add&edit_id=' . rawurlencode($id)) );
                $delete_url = wp_nonce_url( admin_url('admin.php?page=dokmeplus&delete_id=' . rawurlencode($id)), 'dokmeplus_delete_' . $id );
                echo '<tr>';
                echo '<td>' . esc_html($btn['title']) . '</td>';
                echo '<td>[dokmeplus id="' . esc_attr($id) . '"]</td>';
                echo '<td><a href="' . $edit_url . '">' . esc_html(dokmeplus_t('edit')) . '</a> | <a href="' . esc_url($delete_url) . '" onclick="return confirm(\'' . esc_js(dokmeplus_t('confirm_delete')) . '\')">' . esc_html(dokmeplus_t('delete')) . '</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">' . esc_html(dokmeplus_t('no_buttons')) . '</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
