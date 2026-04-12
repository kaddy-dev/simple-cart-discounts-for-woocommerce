<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=' . DCW_PLUGIN_SLUG)); ?>" class="page-title-action">Back to Rules</a>

    <hr class="wp-header-end">

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('dcw_update_rule_' . $rule->id, 'dcw_nonce'); ?>
        <input type="hidden" name="action" value="dcw_update_rule">
        <input type="hidden" name="rule_id" value="<?php echo esc_attr($rule->id); ?>">

        <table class="form-table">
            <tr>
                <th><label for="enabled">Enabled</label></th>
                <td>
                    <input type="checkbox" id="enabled" name="enabled" value="1" <?php checked(!empty($rule->enabled)); ?>>
                </td>
            </tr>
            <tr>
                <th><label for="rule_name">Rule Name</label></th>
                <td>
                    <input type="text" id="rule_name" name="rule_name" value="<?php echo esc_attr($rule->name); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="discount_type">Discount Type</label></th>
                <td>
                    <select id="discount_type" name="discount_type">
                        <option value="cart_discount" <?php selected($rule->type, 'cart_discount'); ?>>Cart Discount</option>
                        <option value="free_shipping" <?php selected($rule->type, 'free_shipping'); ?>>Free Shipping</option>
                        <option value="free_gift" <?php selected($rule->type, 'free_gift'); ?>>Free Gift</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="show_progress_card">Show Progress Card</label></th>
                <td>
                    <input type="checkbox" id="show_progress_card" name="show_progress_card" value="1" <?php checked(!empty($rule->show_progress_card)); ?>>
                </td>
            </tr>

            <tr >
                <th><label for="cart_total">Conditions</label></th>
                <td>
                    <?php $this->render('rules/partials/conditions', ['rule' => $rule]); ?>
                </td>
            </tr>

            <tr class="<?php echo $rule->type != 'cart_discount' ? 'hidden' : ''; ?> cart_discount_opts discount_type_opts">
                <th><label for="discount_value">Discounts</label></th>
                <td>
                    <?php $this->render('rules/partials/discounts', ['rule' => $rule]); ?>
                </td>
            </tr>

            <tr class="<?php echo $rule->type != 'free_gift' ? 'hidden' : ''; ?> free_gift_opts discount_type_opts">
                <th><label for="gifts">Gifts</label></th>
                <td>
                    <?php $this->render('rules/partials/gifts', ['rule' => $rule]); ?>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Save Rule</button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=' . DCW_PLUGIN_SLUG)); ?>" class="button">Cancel</a>
        </p>
    </form>
</div>