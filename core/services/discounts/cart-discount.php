<?php
if (!defined('ABSPATH')) exit;

class DCW_CartDiscount implements DCW_DiscountInterface {
    protected $rule;
    protected $settings;

    private $applied = false;

    public function __construct(DCW_Rule $rule, array $settings) {
        $this->rule = $rule;
        $this->settings = $settings;
    }

    public function apply(WC_Cart $cart) {

        if ($this->applied) {
            return;
        }

        $this->applied = true;

        $discounts = $this->rule->discounts;

        if(empty($discounts)) {
            return;
        }

        $totalDiscount = 0;

        $cartTotal = $this->getCartTotal($cart);

        foreach ($discounts as $discount) {

            if ($discount->type === 'fixed') {
                $totalDiscount += (float) $discount->value;
            }

            if ($discount->type === 'percent') {

                $totalDiscount += ($cartTotal * ((float)$discount->value / 100));
            }
        }

        if ($totalDiscount <= 0) {
            return;
        }

        $this->apply_discount($cart, $totalDiscount);
    }

    public function deactivate(WC_Cart $cart) {
        $coupon_code = 'dcw_' . $this->rule->id;

        if (WC()->cart->has_discount($coupon_code)) {
            WC()->cart->remove_coupon($coupon_code);
        }
    }

    private function apply_discount(WC_Cart $cart, float $amount) {

        $amount = round($amount, wc_get_price_decimals());

        if ($this->settings['apply_cart_discount_as'] === 'fee') {

            $cart->add_fee(
                $this->rule->name,
                -$amount,
                false
            );

        } elseif ($this->settings['apply_cart_discount_as'] === 'coupon') {

            $coupon_code = 'dcw_' . $this->rule->id;

            if (!WC()->cart->has_discount($coupon_code)) {
                $cart->apply_coupon($coupon_code);
            }
        }
    }

    private function getCartTotal(WC_Cart $cart): float {

        switch ($this->settings['calculate_discount_by']) {

            case 'sale_price':
                return (float) $cart->get_cart_contents_total();

            case 'regular_price':
                $total = 0;

                foreach ($cart->get_cart() as $item) {
                    $product = $item['data'];
                    $total += $product->get_regular_price() * $item['quantity'];
                }

                return $total;

            default:
                return (float) $cart->get_cart_contents_total();
        }
    }

}