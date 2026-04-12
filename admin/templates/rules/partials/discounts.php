<?php if (!defined('ABSPATH')) exit; ?>

<div class="dcw-discounts">

    <?php if (!empty($rule->discounts)) : ?>
        <?php foreach ($rule->discounts as $index => $discount) : ?>
            <div class="dcw-discount-row">

                <select name="discounts[<?php echo $index; ?>][type]">
                    <option value="fixed" <?php selected($discount->type, 'fixed'); ?>>Fixed</option>
                    <option value="percent" <?php selected($discount->type, 'percent'); ?>>Percent</option>
                </select>

                <input type="text"
                    name="discounts[<?php echo $index; ?>][value]"
                    value="<?php echo esc_attr($discount->value ?? ''); ?>"
                    class="small-text"
                >

                <button type="button" class="button dcw-remove-discount">×</button>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<button type="button" class="button button-add-entity" id="dcw-add-discount">+ Add Discount</button>

<!-- TEMPLATE -->
<template id="dcw-discount-row-template">
    <div class="dcw-discount-row">

        <select name="__name__[type]">
            <option value="fixed">Fixed</option>
            <option value="percent">Percent</option>
        </select>

        <input type="text"
            name="__name__[value]"
            class="small-text"
        >

        <button type="button" class="button dcw-remove-discount">×</button>

    </div>
</template>