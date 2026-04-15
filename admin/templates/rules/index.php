<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">

    <!-- WP Header -->
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=dcw-add-rule')); ?>" class="page-title-action">Add Rule</a>

    <hr class="wp-header-end">

    <!-- Rules Table -->
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
        <tr>
            <th scope="col" class="manage-column">Name</th>
            <th scope="col" class="manage-column">Type</th>
            <th scope="col" class="manage-column">Status</th>
            <th scope="col" class="manage-column">Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php if (!empty($rules)) : ?>
            <?php foreach ($rules as $rule) : ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=dcw-edit-rule&rule_id=' . $rule->id)); ?>">
                            <?php echo esc_html($rule->name); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html($rule->type); ?></td>
                    <td>
                        <label class="dcw-switch">
                            <input
                                    type="checkbox"
                                    class="dcw-toggle-rule"
                                    data-rule-id="<?php echo esc_attr($rule->id); ?>"
                                <?php checked($rule->enabled); ?>
                            >
                            <span class="dcw-slider"></span>
                        </label>
                    </td>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=dcw-edit-rule&rule_id=' . $rule->id)); ?>"
                           class="button button-small">Edit</a>

                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                              style="display:inline;">
                            <?php wp_nonce_field('dcw_delete_rule_' . $rule->id, 'dcw_nonce'); ?>
                            <input type="hidden" name="action" value="dcw_delete_rule">
                            <input type="hidden" name="rule_id" value="<?php echo esc_attr($rule->id); ?>">
                            <button type="submit" class="button button-small">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" style="text-align:center;">No discount rules found. <a
                            href="<?php echo esc_url(admin_url('admin.php?page=dcw-add-rule')); ?>">Add New Rule</a>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>