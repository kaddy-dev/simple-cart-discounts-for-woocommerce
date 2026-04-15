<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>
    <hr class="wp-header-end">

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('dcw_save_settings', 'dcw_nonce'); ?>
        <input type="hidden" name="action" value="dcw_save_settings">

        <!-- DISCOUNTS -->
        <h2 class="hndle">Discounts</h2>
        <div class="postbox">
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th><label for="calculate_discount_by">Calculate discount by</label></th>
                        <td>
                            <select name="dcw_settings[calculate_discount_by]" id="calculate_discount_by">
                                <option value="sale_price" <?php selected($options['calculate_discount_by'] ?? '', 'sale_price'); ?>>
                                    Sale price
                                </option>
                                <option value="regular_price" <?php selected($options['calculate_discount_by'] ?? '', 'regular_price'); ?>>
                                    Regular price
                                </option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- SHIPPING -->
        <!--        <h2 class="hndle">Shipping</h2>-->
        <!--        <div class="postbox">-->
        <!--            <div class="inside">-->
        <!--                <p>No settings yet</p>-->
        <!--            </div>-->
        <!--        </div>-->

        <!-- GIFTS -->
        <h2 class="hndle">Gifts</h2>
        <div class="postbox">
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th><label for="use_additional_detail_on_gifts">Show additional detail</label></th>
                        <td>
                            <label class="dcw-switch">
                                <input
                                        type="checkbox"
                                        value="1"
                                        name="dcw_settings[use_additional_detail_on_gifts]"
                                        id="use_additional_detail_on_gifts"
                                    <?php checked($options['use_additional_detail_on_gifts'] ?? false); ?>
                                >
                                <span class="dcw-slider"></span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="additional_detail_on_gifts_name">Additional detail</label></th>
                        <td>
                            Name:
                            <input
                                    type="text"
                                    name="dcw_settings[additional_detail_on_gifts_name]"
                                    id="additional_detail_on_gifts_name"
                                    placeholder="Gift"
                                    value="<?php echo $options['additional_detail_on_gifts_name'] ?? ''; ?>"
                            >

                            Value:
                            <input
                                    type="text"
                                    name="dcw_settings[additional_detail_on_gifts_value]"
                                    id="additional_detail_on_gifts_value"
                                    placeholder="free item"
                                    value="<?php echo $options['additional_detail_on_gifts_value'] ?? ''; ?>"
                            >
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary">Save Settings</button>
        </p>
    </form>
</div>