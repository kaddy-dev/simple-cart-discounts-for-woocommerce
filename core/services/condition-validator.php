<?php
if (!defined('ABSPATH')) exit;

class DCW_Condition_Validator
{

    public function validate(array $conditions, WC_Cart $cart): bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            if (!$this->validateSingle($condition, $cart)) {
                return false;
            }
        }

        return true;
    }

    protected function validateSingle($condition, WC_Cart $cart): bool
    {

        switch ($condition->type) {

            case 'cart_total':

                return $this->cart_total($condition, $cart);

            default:
                return false;
        }

        return false;
    }

    private function cart_total($condition, WC_Cart $cart): bool
    {
        $total = $cart->get_cart_contents_total();

        if ($condition->operator == '>') {
            return $total > $condition->value;
        }

        if ($condition->operator == '<') {
            return $total < $condition->value;
        }

        if ($condition->operator == '>=') {
            return $total >= $condition->value;
        }

        if ($condition->operator == '<=') {
            return $total <= $condition->value;
        }

        if ($condition->operator == '=') {
            return $total == $condition->value;
        }

        if ($condition->operator == '!=') {
            return $total != $condition->value;
        }

        return false;
    }
}