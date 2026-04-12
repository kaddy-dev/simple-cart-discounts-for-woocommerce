<?php
if (!defined('ABSPATH')) exit;

class DCW_FreeShipping implements DCW_DiscountInterface {
    protected $rule;

    public function __construct(DCW_Rule $rule) {
        $this->rule = $rule;
    }

    public function apply(WC_Cart $cart) {
        WC()->session->set('dcw_shipment_free', true);
    }

    public function deactivate(WC_Cart $cart) {
        WC()->session->set('dcw_shipment_free', false);
    }
}