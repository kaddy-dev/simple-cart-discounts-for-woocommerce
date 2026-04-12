<?php
if (!defined('ABSPATH')) exit;

class DCW_Cart_Manager
{

    private $rule_repository;
    private $condition_validator;

    public $settings;

    public function __construct(DCW_Rule_Repository     $repository,
                                DCW_Condition_Validator $validator)
    {

        $this->rule_repository = $repository;
        $this->condition_validator = $validator;

        $this->settings = get_option('dcw_settings', [
            'calculate_discount_by' => 'sale_price',
            'apply_cart_discount_as' => 'fee'
        ]);

        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_discounts']);
        add_filter('woocommerce_package_rates', [$this, 'apply_discounts_free_shipping'], 100, 2);
    }

    public function apply_discounts(WC_Cart $cart)
    {
        $rules = $this->rule_repository->get_enabled();

        foreach ($rules as $rule) {
            if (empty($rule->enabled)) continue;

            $condition_validated = $this->condition_validator->validate($rule->conditions, $cart);

            switch ($rule->type) {
                case 'cart_discount':
                    $discount = new DCW_CartDiscount($rule, $this->settings);
                    break;
                case 'free_shipping':
                    // check apply_discounts_free_shipping
                    $discount = null;
                    break;
                case 'free_gift':
                    $discount = new DCW_FreeGift($rule);
                    break;
                default:
                    $discount = null;
                    break;
            }

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

    public function apply_discounts_free_shipping($rates, $package)
    {
        $rules = $this->rule_repository->get_enabled();

        foreach ($rules as $rule) {

            if ($rule->type !== 'free_shipping') continue;

            $condition_validated = $this->condition_validator->validate($rule->conditions, WC()->cart);

            if ($condition_validated) {
                $discount = new DCW_FreeShipping($rule);
                $discount->setFreeRates($rates);
                break;
            }
        }

        return $rates;
    }


}