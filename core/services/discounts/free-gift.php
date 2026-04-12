<?php
if (!defined('ABSPATH')) exit;

class DCW_FreeGift implements DCW_DiscountInterface {
    protected $rule;

    public function __construct(DCW_Rule $rule) {
        $this->rule = $rule;
    }

    public function apply(WC_Cart $cart) {

        if (empty($this->rule->gifts)) return;

        foreach ($this->rule->gifts as $gift) {

            $product_id = (int) $gift->product_id;
            if (!$product_id) continue;

            if(empty($gift->quantity)) continue;

            if ($this->giftInCart($gift, $cart)) continue;

            $cart->add_to_cart($product_id, $gift->quantity, 0, [], [
                'dcw_gift' => true,
                'rule_id'  => $this->rule->id,
            ]);
        }
    }

    public function deactivate(WC_Cart $cart) {
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {

            if (empty($cart_item['dcw_gift'])) continue;

            if (($cart_item['rule_id'] ?? null) != $this->rule->id) continue;

            $cart->remove_cart_item($cart_item_key);
        }
    }

    private function giftInCart(DCW_Rule_Gift $gift, WC_Cart $cart): bool {

        $product_id = (int) $gift->product_id;
        if (!$product_id) return false;

        foreach ($cart->get_cart() as $item) {
            if (!empty($item['dcw_gift'])) {

                if($item['product_id'] == $product_id && ($item['rule_id'] ?? null) == $this->rule->id) {
                    return true;
                }

            }
        }

        return false;
    }
}