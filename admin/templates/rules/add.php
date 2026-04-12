<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=' . DCW_PLUGIN_SLUG)); ?>" class="page-title-action">Back to Rules</a>

    <hr class="wp-header-end">

    <form method="post" class="form-table" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('dcw_store_rule', 'dcw_nonce'); ?>
        <input type="hidden" name="action" value="dcw_store_rule">

        <table class="form-table">
            <tr>
                <th><label for="enabled">Enabled</label></th>
                <td>
                    <input type="checkbox" id="enabled" name="enabled" value="1" checked>
                </td>
            </tr>
            <tr>
                <th><label for="rule_name">Rule Name</label></th>
                <td>
                    <input type="text" id="rule_name" name="rule_name" value="" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="discount_type">Discount Type</label></th>
                <td>
                    <select id="discount_type" name="discount_type">
                        <option value="cart_discount" selected>Cart Discount</option>
                        <option value="free_shipping">Free Shipping</option>
                        <option value="free_gift">Free Gift</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="show_progress_card">Show Progress Card</label></th>
                <td>
                    <input type="checkbox" id="show_progress_card" name="show_progress_card" value="1" checked>
                </td>
            </tr>
            

            <tr>
                <th><label for="cart_total">Conditions</label></th>
                <td>
                    <?php $this->render('rules/partials/conditions'); ?>
                </td>
            </tr>

            <tr class="cart_discount_opts discount_type_opts">
                <th><label for="discount_value">Discounts</label></th>
                <td>
                    <?php $this->render('rules/partials/discounts'); ?>
                </td>
            </tr>

            <tr class="hidden free_gift_opts discount_type_opts">
                <th><label for="gifts">Gifts</label></th>
                <td>
                    <?php $this->render('rules/partials/gifts'); ?>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Save Rule</button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=' . DCW_PLUGIN_SLUG)); ?>" class="button">Cancel</a>
        </p>
    </form>
</div>