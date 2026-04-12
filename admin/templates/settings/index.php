<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <hr class="wp-header-end">

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('dcw_save_settings', 'dcw_nonce'); ?>
        <input type="hidden" name="action" value="dcw_save_settings">

        <table class="form-table">
            <tr>
                <th><label for="calculate_discount_by">Calculate discount by</label></th>
                <td>
                    <select name="dcw_settings[calculate_discount_by]" id="calculate_discount_by">
                        <option value="sale_price" <?php selected($options['calculate_discount_by'], 'sale_price'); ?>>
                            Sale price
                        </option>
                        <option value="regular_price" <?php selected($options['calculate_discount_by'], 'regular_price'); ?>>
                            Regular price
                        </option>
                    </select>
                </td>
            </tr>

<!--            <tr>
                    <th><label for="apply_cart_discount_as">Apply cart discount as</label></th>
                    <td>
                                <select name="dcw_settings[apply_cart_discount_as]" id="apply_cart_discount_as">
                                    <option value="fee"
            <?php //selected($options['apply_cart_discount_as'], 'fee'); ?>Fee</option>
                                   <option value="coupon"
            <?php //selected($options['apply_cart_discount_as'], 'coupon'); ?>Coupon</option>
                                </select>
                    </td>
            </tr>-->

        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">Save Settings</button>
        </p>
    </form>
</div>