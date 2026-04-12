<?php
if (!defined('ABSPATH')) exit;

interface DCW_DiscountInterface {
    public function apply(WC_Cart $cart);
    public function deactivate(WC_Cart $cart);
}