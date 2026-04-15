<?php if (!defined('ABSPATH')) exit; ?>

<div class="dcw-gifts dcw-rows">

    <?php if (!empty($rule->gifts)) : ?>
        <?php foreach ($rule->gifts as $index => $gift) : 
            $product = wc_get_product($gift->product_id);
            if ($product) :
            ?>

                <div class="dcw-gift-row dcw-row">

                    <select
                        name="gifts[<?php echo $index; ?>][product_id]"
                        class="wc-product-search"
                        style="width:300px;"
                        data-placeholder="<?php esc_attr_e('Search for products...', 'discounts-cart'); ?>"
                        data-action="woocommerce_json_search_products_and_variations"
                    >
                        <option value="<?php echo esc_attr($gift->product_id); ?>" selected="selected">
                            <?php echo esc_html($product->get_name()); ?>
                        </option>
                    </select>

                    <input
                        type="number"
                        name="gifts[<?php echo $index; ?>][quantity]"
                        value="<?php echo esc_attr($gift->quantity); ?>"
                        min="1"
                    >

                    <button type="button" class="button dcw-remove-gift">×</button>

                </div>

            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<button type="button" class="button button-add-entity" id="dcw-add-gift">+ Add Gift</button>


<template id="dcw-gift-row-template">
    <div class="dcw-gift-row dcw-row">

        <select
            name="__name__[product_id]"
            class="wc-product-search"
            style="width:300px;"
            data-placeholder="<?php esc_attr_e('Search for products...', 'discounts-cart'); ?>"
            data-action="woocommerce_json_search_products_and_variations"
        ></select>

        <input
            type="number"
            name="__name__[quantity]"
            value="1"
            min="1"
        >

        <button type="button" class="button dcw-remove-gift">×</button>

    </div>
</template>