<?php
if (!defined('ABSPATH')) exit;

$all = get_option('dokmeplus_buttons', []);

// پیام‌ها
if (isset($_GET['msg'])) {

    $messages = [
        'deleted'  => '<div class="notice notice-success"><p>دکمه با موفقیت حذف شد.</p></div>',
        'copied'   => '<div class="notice notice-success"><p>دکمه با موفقیت کپی شد.</p></div>',
        'limit'    => '<div class="notice notice-error"><p>نسخه رایگان فقط اجازه ساخت ۳ دکمه را دارد.</p></div>',
        'invalid'  => '<div class="notice notice-error"><p>درخواست نامعتبر بود.</p></div>',
        'notfound' => '<div class="notice notice-error"><p>دکمه پیدا نشد.</p></div>',
    ];

    if (isset($messages[$_GET['msg']])) {
        echo $messages[$_GET['msg']];
    }
}

echo '<h1>' . esc_html(dokmeplus_t('list_title')) . '</h1>';
?>

<table class="wp-list-table widefat striped">
    <thead>
        <tr>
            <th><?php echo esc_html(dokmeplus_t('list_title')); ?></th>
            <th><?php echo esc_html(dokmeplus_t('shortcode')); ?></th>
            <th><?php echo esc_html(dokmeplus_t('actions')); ?></th>
        </tr>
    </thead>

    <tbody>

<?php if (!empty($all)) : ?>

    <?php foreach ($all as $id => $item): ?>

        <?php
        /** لینک‌ها */
        $edit_url = admin_url('admin.php?page=dokmeplus_add&edit_id=' . $id);

        $delete_url = wp_nonce_url(
            admin_url('admin.php?page=dokmeplus&delete_id=' . $id),
            'dokmeplus_delete_' . $id
        );

        $copy_url = wp_nonce_url(
            admin_url('admin.php?page=dokmeplus&copy_id=' . $id),
            'dokmeplus_copy_' . $id
        );
        ?>

        <tr>
            <td><?php echo esc_html($item['title'] ?? 'بدون عنوان'); ?></td>

            <!-- ⭐ ستون شورت‌کد ⭐ -->
            <td>
                <code>[dokmeplus id="<?php echo esc_attr($id); ?>"]</code>
            </td>

            <td>
                <a href="<?php echo esc_url($edit_url); ?>">
                    <?php echo esc_html(dokmeplus_t('edit')); ?>
                </a> |

                <a href="<?php echo esc_url($delete_url); ?>"
                   onclick="return confirm('<?php echo esc_attr(dokmeplus_t('confirm_delete')); ?>')">
                    <?php echo esc_html(dokmeplus_t('delete')); ?>
                </a> |

                <a href="<?php echo esc_url($copy_url); ?>">
                    کپی
                </a>
            </td>
        </tr>

    <?php endforeach; ?>

<?php else: ?>

        <tr>
            <td colspan="3"><?php echo esc_html(dokmeplus_t('no_buttons')); ?></td>
        </tr>

<?php endif; ?>

    </tbody>
</table>
