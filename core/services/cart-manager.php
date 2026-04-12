<?php
if (!defined('ABSPATH')) exit;

class DCW_Cart_Manager {

    private $rule_repository;
    private $condition_validator;

    public $settings;

    public function __construct(DCW_Rule_Repository $repository,
                                DCW_Condition_Validator $validator) {

        $this->rule_repository = $repository;
        $this->condition_validator = $validator;

        $this->settings = get_option('dcw_settings', [
            'calculate_discount_by' => 'sale_price',
            'apply_cart_discount_as' => 'fee'
        ]);

        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_discounts']);
    }

    public function apply_discounts(WC_Cart $cart) {
        $rules = $this->rule_repository->get_enabled();

        foreach ($rules as $rule) {
            if (empty($rule->enabled)) continue;

            $condition_validated = $this->condition_validator->validate($rule->conditions, $cart);

            $discount = $this->resolve_discount($rule);

            if (!$discount) {
                continue;
            }

            if ($condition_validated) {
                $discount->apply($cart);
            } else {
                $discount->deactivate($cart);
            }
        }
    }

    private function resolve_discount($rule) {
        switch ($rule->type) {
            case 'cart_discount':
                return new DCW_CartDiscount($rule, $this->settings);
            case 'free_shipping':
                return new DCW_FreeShipping($rule);
            case 'free_gift':
                return new DCW_FreeGift($rule);
            default:
                return null;
        }

        return null;
    }


}