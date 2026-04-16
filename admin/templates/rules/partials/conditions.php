<?php if (!defined('ABSPATH')) exit; ?>

<div class="dcw-conditions dcw-rows">

    <?php if (!empty($rule->conditions)) : ?>
        <?php foreach ($rule->conditions as $index => $condition) : ?>
            <div class="dcw-condition-row dcw-row">

                <select name="conditions[<?php echo $index; ?>][type]">
                    <option value="cart_total" <?php selected($condition->type, 'cart_total'); ?>>Cart Total</option>
                </select>

                <select name="conditions[<?php echo $index; ?>][operator]">
                    <option value=">" <?php selected($condition->operator ?? '', '>'); ?>>></option>
                    <option value="<" <?php selected($condition->operator ?? '', '<'); ?>><</option>
                    <option value=">=" <?php selected($condition->operator ?? '', '>='); ?>>≥</option>
                    <option value="<=" <?php selected($condition->operator ?? '', '<='); ?>>≤</option>
                    <option value="=" <?php selected($condition->operator ?? '', '='); ?>>=</option>
                    <option value="!=" <?php selected($condition->operator ?? '', '!='); ?>>!=</option>
                </select>

                <input type="text" 
                    name="conditions[<?php echo $index; ?>][value]" 
                    value="<?php echo esc_attr($condition->value ?? ''); ?>"
                >

                <button type="button" class="button dcw-remove-condition">×</button>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<button type="button" class="button button-add-entity" id="dcw-add-condition">+ Add Condition</button>


<!-- TEMPLATE -->
<template id="dcw-condition-row-template">
    <div class="dcw-condition-row dcw-row">

        <select name="__name__[type]">
            <option value="cart_total">Cart Total</option>
        </select>

        <select name="__name__[operator]">
            <option value=">">></option>
            <option value="<"><</option>
            <option value=">=">≥</option>
            <option value="<=">≤</option>
            <option value="=">=</option>
            <option value="!=">!=</option>
        </select>

        <input type="text" 
            name="__name__[value]"
        >

        <button type="button" class="button dcw-remove-condition">×</button>

    </div>
</template>