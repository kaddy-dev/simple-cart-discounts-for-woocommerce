<?php
if (!defined('ABSPATH')) exit;

class DCW_Site_Manager
{

    private $rule_repository;
    private $condition_validator;

    public $settings;

    private $k_managed_free_shipping = false;

    public function __construct(DCW_Rule_Repository     $repository,
                                DCW_Condition_Validator $validator)
    {
        $this->rule_repository = $repository;
        $this->condition_validator = $validator;

        $this->settings = get_option('dcw_settings');

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 10);

        add_action('woocommerce_checkout_create_order_line_item', [$this, 'mark_gift_in_checkout'], 10, 4);
        add_action('woocommerce_before_calculate_totals', [$this, 'calculate_totals'], 999);
        add_action('woocommerce_after_cart_item_quantity_update', [$this, 'manage_gift_quantity'], 10, 4);

        if (isset($this->settings['use_additional_detail_on_gifts']) && $this->settings['use_additional_detail_on_gifts']) {
            add_action('woocommerce_get_item_data', [$this, 'manage_gift_item_data'], 10, 2);
        }

        add_action('woocommerce_review_order_after_order_total', [$this, 'render_progress_card'], 11);
        add_action('dcw_render_progress_card', [$this, 'render_progress_card'], 20);

        add_action('init', [$this, 'register_block_progress_card']);
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'dcw-site',
            plugins_url('site/assets/script.js', DCW_PLUGIN_FILE),
            ['jquery'],
            DCW_VERSION,
            true
        );

        wp_enqueue_style(
            'dcw-site-style',
            plugins_url('site/assets/style.css', DCW_PLUGIN_FILE),
            null,
            DCW_VERSION
        );
    }

    public function mark_gift_in_checkout($item, $cart_item_key, $values, $order)
    {
        if (!empty($values['dcw_gift'])) {
            $item->add_meta_data('dcw_gift', true, true);
        }

        if (!empty($values['rule_id'])) {
            $item->add_meta_data('rule_id', $values['rule_id'], true);
        }
    }

    public function manage_gift_price($cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) return;

        foreach ($cart->get_cart() as $cart_item) {
            if (!empty($cart_item['dcw_gift'])) {

                /**
                 * Change gift price
                 *
                 * @param float $price Current gift price (default 0)
                 * @param array $cart_item Cart item
                 */
                $price = apply_filters('dcw_gift_price', 0, $cart_item);

                $cart_item['data']->set_price($price);
            }
        }
    }

    // Disable update quantity of gifts
    public function manage_gift_quantity($cart_item_key, $quantity, $old_quantity, $cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        $cart_item = $cart->get_cart_item($cart_item_key);
        $rule_id = $cart_item['rule_id'] ?? null;

        if (!$rule_id) return;

        if (empty($cart_item['dcw_gift'])) {
            return;
        }

        $rules = $this->rule_repository->get_enabled();

        foreach ($rules as $rule) {

            if ($rule->id != $rule_id) continue;

            foreach ($rule->gifts as $gift) {
                if ((int)$gift->product_id === (int)$cart_item['product_id']) {
                    $correct_qty = (int)$gift->quantity ?: $old_quantity;
                    if ($quantity != $correct_qty) {
                        $cart->set_quantity($cart_item_key, $correct_qty);
                    }
                    return;
                }
            }
        }
    }

    public function manage_gift_item_data($item_data, $cart_item)
    {
        if (!empty($cart_item['dcw_gift'])) {
            $gift_data = [
                'name' => !empty($this->settings['additional_detail_on_gifts_name']) ? __($this->settings['additional_detail_on_gifts_name'], 'discounts-cart') : '',
                'value' => !empty($this->settings['additional_detail_on_gifts_value']) ? __($this->settings['additional_detail_on_gifts_value'], 'discounts-cart') : '',
            ];

            /**
             * Change data gift in cart
             *
             * @param array $gift_data Name and Value gift data
             * @param array $cart_item Cart item
             */
            $gift_data = apply_filters('dcw_gift_item_data', $gift_data, $cart_item);

            $item_data[] = $gift_data;
        }

        return $item_data;
    }

    public function calculate_totals($cart)
    {
        $this->manage_sort_gift($cart);
        $this->manage_gift_price($cart);
    }

    public function manage_sort_gift($cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) return;

        $cart_contents = $cart->get_cart();

        $regular = [];
        $gifts = [];

        foreach ($cart_contents as $key => $item) {
            if (!empty($item['dcw_gift'])) {
                $gifts[$key] = $item;
            } else {
                $regular[$key] = $item;
            }
        }

        $sorted = $regular + $gifts;

        $cart->cart_contents = $sorted;
    }

    public function render_progress_card()
    {
        $rules = $this->rule_repository->get_enabled();

        $cart = WC()->cart;

        if (!$cart) return;

        $cart_total = $cart->get_cart_contents_total();

        if (isset($this->settings['progress_card_show']) && $this->settings['progress_card_show'] == 'nearest_discount') {
            $this->render_nearest_progress_card($rules, $cart_total);
        } else {
            $this->render_all_progress_card($rules, $cart_total);
        }

    }

    protected function render_nearest_progress_card($rules, $cart_total)
    {
        $nearestRule = null;
        $nearestNeed = null;
        $maxRule = null;
        $maxNeed = 0;

        foreach ($rules as $rule) {

            if (empty($rule->enabled)) continue;
            if (!$rule->show_progress_card) continue;

            $cart_total_need = 0;

            foreach ($rule->conditions as $condition) {
                if ($condition->type == 'cart_total') {
                    $cart_total_need = $condition->value;
                }
            }

            if (!$cart_total_need) continue;

            if ($cart_total_need > $maxNeed) {
                $maxNeed = $cart_total_need;
                $maxRule = $rule;
            }

            if ($cart_total_need > $cart_total) {

                if ($nearestNeed === null || $cart_total_need < $nearestNeed) {
                    $nearestNeed = $cart_total_need;
                    $nearestRule = $rule;
                }
            }
        }

        if ($nearestRule) {
            $this->view_progress_card($nearestRule, $nearestNeed, $cart_total);
        } elseif ($maxRule) {
            $this->view_progress_card($maxRule, $maxNeed, $cart_total);
        }
    }

    protected function render_all_progress_card($rules, $cart_total)
    {
        foreach ($rules as $rule) {

            if (empty($rule->enabled)) continue;
            if (!$rule->show_progress_card) continue;

            $cart_total_need = 0;

            foreach ($rule->conditions as $condition) {
                if ($condition->type == 'cart_total') {
                    $cart_total_need = $condition->value;
                }
            }

            if (!$cart_total_need) continue;

            $this->view_progress_card($rule, $cart_total_need, $cart_total);
        }
    }

    public function view_progress_card($rule, $cart_total_need, $cart_total)
    {
        $view = 'discount-card';

        $view_file = DCW_PLUGIN_PATH . 'site/templates/' . $view . '.php';

        if (file_exists($view_file)) {
            extract(array(
                'rule' => $rule,
                'cart_total_need' => $cart_total_need,
                'current_amount' => $cart_total,
            ));
            include $view_file;
        }
    }

    public function block_render_progress_card()
    {
        ob_start();
        $this->render_progress_card();
        return ob_get_clean();
    }

    public function register_block_progress_card()
    {
        register_block_type('discounts-cart/progress-card', [
            'render_callback' => [$this, 'block_render_progress_card'],
        ]);
    }

}