<?php

defined( 'ABSPATH' ) || exit;

$percent = 100 * $current_amount / $cart_total_need;

if($percent > 100) $percent = 100;
if($percent < 0) $percent = 0;

$remaining = $cart_total_need - $current_amount;

if ($remaining > 0) {
    $message = sprintf(
            __('Spend <strong>%s</strong> more to get:', 'discounts-cart'),
            wc_price($remaining)
        ) . ' ' . __($rule->name, 'discounts-cart');

    $message = apply_filters('dcw_message_progress_not_applied', $message, $rule, $remaining);
} else {
    $message = __($rule->name, 'discounts-cart');
    $message = apply_filters('dcw_message_progress_applied', $message, $rule);
}

?>

<div class="dcw-discount-card dcw-discount-card-<?php echo $rule->id; ?> dcw-discount-card-<?php echo $rule->type; ?>">
        
	<div class="dcw-progress-bar-container">
		<span class="dcw-progress-label label-zero"><?php echo wc_price(0); ?></span>
	            
		<div class="dcw-progress-track">
			<div class="progress-car" style="left: <?php echo $percent; ?>%;">
				<img src="<?php echo plugins_url('site/assets/images/progress-image.png', DCW_PLUGIN_FILE); ?>"
                     alt="<?php _e('Progress Icon', 'discounts-cart'); ?>">
			</div>
		                
			<div class="dcw-progress-fill" style="width: <?php echo $percent; ?>%;"></div>
		</div>
		            
		<span class="dcw-progress-label label-target"><?php echo wc_price($cart_total_need); ?></span>
	</div>

	<div class="dcw-card-notice"><?php echo $message; ?></div>

</div>
