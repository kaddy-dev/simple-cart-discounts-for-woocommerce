<?php
if (!defined('ABSPATH')) exit;

class DCW_FreeShipping implements DCW_DiscountInterface
{
    protected $rule;

    public function __construct(DCW_Rule $rule)
    {
        $this->rule = $rule;
    }

    public function apply(WC_Cart $cart)
    {

    }

    public function deactivate(WC_Cart $cart)
    {

    }

    public function setFreeRates(&$rates)
    {
        foreach ($rates as $rate_key => $rate) {

            $rates[$rate_key]->cost = 0;

            foreach ($rate->taxes ?? [] as $tax_key => $tax) {
                $rates[$rate_key]->taxes[$tax_key] = 0;
            }
        }
    }
}